<?php

namespace App\Services\WeatherProviders;

use GuzzleHttp\Client;
use App\Contracts\Weather\Weather;
use App\Jobs\CacheWeatherDataResult;
use Illuminate\Support\Facades\Cache;
use App\Services\Geo\Spatial\QuadTree\Node;
use App\Contracts\Weather\Concerns\HasForecast;
use App\Services\Geo\Spatial\QuadTree\QuadTree;
use App\Exceptions\InvalidWeatherDataException;
use App\Contracts\Weather\WeatherProvider;
use App\Contracts\Geo\GeoCoordinate;
use App\Contracts\Weather\Astronomy;
use App\Services\Weather\Forecast;
use App\Contracts\Weather\Location;
use App\Contracts\Weather\Temperature;
use App\Contracts\Weather\Wind;
use App\Enums\SpeedUnit;
use App\Enums\TemperatureUnit;
use App\Exceptions\WeatherRequestException;
use Carbon\Carbon;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Pest\Support\Arr;

final class WeatherApiProvider implements WeatherProvider {

    /**
     * The Weather Provider config.
     *
     * @var array $config
     */
    protected $config = [];


    /**
     * The Weather Provider Api Endpoint.
     *
     * @var string $endpoint
     */
    protected $endpoint = 'http://api.weatherapi.com/v1/';

    protected const CACHE_KEY = 'weather_api.cached_weather';

    /**
     * Build the class instance.
     *
     * @param  array $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get the current weather for the given GeoCoordinate object.
     *
     * @param  GeoCoordinate $coordinate
     * @return Weather
     * @throws InvalidArgumentException
     * @throws WeatherRequestException
     */
    public function getCurrentWeather(GeoCoordinate $coordinate) : Weather {
        $weather = $this->getWeatherData($coordinate);

        $forecast       = Arr::get($weather, 'forecast', []);
        $currentWeather = Arr::get($weather, 'current_weather');

        if(!$currentWeather instanceof Weather) {
            throw new InvalidWeatherDataException(sprintf("Weather data for coordinate %s,%s is invalid.", $coordinate->getLatitude(), $coordinate->getLongitude()));
        }

        if($currentWeather instanceof HasForecast) {
            $currentWeather->setForecast($forecast);
        }

        return $currentWeather;
    }

    private function getWeatherData(GeoCoordinate $coordinate) {
        $weatherData = $this->fetchWeatherDataFromCache($coordinate);

        if(!$weatherData) {
            $weatherData = $this->fetchWeatherFromApi($coordinate);

            CacheWeatherDataResult::dispatch(self::CACHE_KEY, $coordinate, json_encode($weatherData));
        }

        return $this->formatWeatherData($weatherData);
    }

    private function fetchWeatherDataFromCache(GeoCoordinate $coordinate) {
        $cachedData = Cache::get(self::CACHE_KEY);

        if(! $cachedData) {
            return;
        }

        $cachedData = json_decode($cachedData, true);
        $cachedHashes = Arr::get($cachedData, 'cached_hash');

        if(isset($cachedHashes[$coordinate->getHash()])) {
            return json_decode($cachedHashes[$coordinate->getHash()], true);
        }

        $root = unserialize(Arr::get($cachedData, 'serialized_root_tree'));

        if($root instanceof Node) {
            $tree = new QuadTree($root);

            $closestHash  = $tree->findClosestHash($coordinate);

            if($closestHash && isset($cachedHashes[$closestHash])) {

                return json_decode($cachedHashes[$closestHash], true);
            }
        }
    }

    private function fetchWeatherFromApi(GeoCoordinate $coordinate) : array {

        $client = new Client([
            'base_uri' => $this->endpoint,
            'query' => [
                'q' => sprintf("%s,%s", $coordinate->getLatitude(), $coordinate->getLongitude()),
                'key' => Arr::get($this->config, 'api_key')
            ]
        ]);

        $currentWeatherPromise = $client->getAsync('current.json');
        $astronomyPromise      = $client->getAsync('astronomy.json');
        $forecastPromise       = $client->getAsync('forecast.json');

        try {

            $results = collect(Utils::all([
                'current_weather' => $currentWeatherPromise,
                'astronomy' => $astronomyPromise,
                'forecast' => $forecastPromise
            ])->wait())->map(function(Response $response) {
                return $response->getBody()->getContents();
            })->toArray();

            return $results;

        } catch(\Exception $e) {
            throw new WeatherRequestException("Unexpected Error during weather request.", 500, $e);
        }

    }

    private function formatWeatherData(array $data = []) {
        $forecast       = Arr::get($data, 'forecast');
        $astronomy      = Arr::get($data, 'astronomy');
        $currentWeather = Arr::get($data, 'current_weather');

        $mergedData = [
            'current_weather' => $currentWeather,
            'astronomy' => $astronomy
        ];

        $formattedData = [
            'current_weather' => $this->generateWeatherFromData($mergedData),
            'forecast' => []
        ];

        $currentForecast = json_decode($forecast, true) ?? [];

        $forecastDay     = Arr::get($currentForecast, 'forecast.forecastday.0.hour');
        $forecastedAstro = Arr::get($currentForecast, 'forecast.forecastday.0.astro');
        $forecastedLocation = Arr::get($currentForecast, 'location');

        $weatherLocation = app(Location::class);
        $city      = Arr::get($forecastedLocation, 'name');
        $country   = Arr::get($forecastedLocation, 'country');
        $timezone  = Arr::get($forecastedLocation, 'tz_id');
        $localTime = Arr::get($forecastedLocation, 'localtime');

        if($city) $weatherLocation->setCity($city);
        if($country) $weatherLocation->setCountry($country);
        if($timezone) $weatherLocation->setTimezone($timezone);
        if($localTime) $weatherLocation->setLocalTime(Carbon::parse($localTime, $timezone));

        $forecastData = collect($forecastDay)->map(function($forecasted) use($forecastedAstro, $weatherLocation, $forecastedLocation, $timezone) {

            $forecastTime = Arr::get($forecasted, 'time');

            if(!$forecastTime || !$timezone) {
                // skip forecast.
                return;
            }

            $mergedData = [
                'current_weather' => json_encode(['current' => $forecasted, 'location' => $forecastedLocation]),
                'astronomy' => json_encode(['astronomy' => ['astro' => $forecastedAstro]])
            ];

            $weather = $this->generateWeatherFromData($mergedData);

            return Forecast::make(
                Carbon::parse($forecastTime, $timezone),
                $weather,
                $weatherLocation
            );
        })->toArray();

        $formattedData['forecast'] = $forecastData;


        return $formattedData;
    }

    private function generateWeatherFromData(array $data = []) : Weather {

        $weatherData   = json_decode(Arr::get($data, 'current_weather', '{}'), true);
        $astronomyData = json_decode(Arr::get($data, 'astronomy', '{}'), true);

        $location         = Arr::get($weatherData, 'location', []);
        $currentWeather   = Arr::get($weatherData, 'current', []);
        $weatherCondition = Arr::get($currentWeather, 'condition', []);

        $weather = app(Weather::class);

        $icon      = Arr::get($weatherCondition, 'icon');
        $condition = Arr::get($weatherCondition, 'text');

        $weather->setWeatherCondition($condition);
        $weather->setWeatherConditionIcon($icon);

        $weatherLocation = app(Location::class);
        $city      = Arr::get($location, 'name');
        $country   = Arr::get($location, 'country');
        $timezone  = Arr::get($location, 'tz_id');
        $localTime = Arr::get($location, 'localtime');

        if($city) $weatherLocation->setCity($city);
        if($country) $weatherLocation->setCountry($country);
        if($timezone) $weatherLocation->setTimezone($timezone);
        if($localTime) $weatherLocation->setLocalTime(Carbon::parse($localTime, $timezone));

        $wind = app(Wind::class);
        $windSpeed = Arr::get($currentWeather, 'wind_kph');
        $windDirection = Arr::get($currentWeather, 'wind_dir');
        $windDegree = Arr::get($currentWeather, 'wind_degree');
        $windGust = Arr::get($currentWeather, 'gust_kph');

        if($windSpeed) $wind->setWindSpeed($windSpeed, SpeedUnit::KMH);
        if($windDirection) $wind->setWindDirection($windDirection);
        if($windDegree) $wind->setWindDegree($windDegree);
        if($windGust) $wind->setWindGust($windGust, SpeedUnit::KMH);

        $temperatureCelcius = Arr::get($currentWeather, 'temp_c');
        $temperatureFahrenheit = Arr::get($currentWeather, 'temp_f');

        if($temperatureCelcius) {
            $weather->addTemperature(
                app(Temperature::class)->setTemperature($temperatureCelcius, TemperatureUnit::CELCIUS)
            );
        }

        if($temperatureFahrenheit) {
            $weather->addTemperature(
                app(Temperature::class)->setTemperature($temperatureFahrenheit, TemperatureUnit::FAHRENHEIT)
            );
        }

        $astro    = app(Astronomy::class);
        $sunrise  = Arr::get($astronomyData, 'astronomy.astro.sunrise');
        $sunset   = Arr::get($astronomyData, 'astronomy.astro.sunset');
        $moonrise = Arr::get($astronomyData, 'astronomy.astro.moonrise');
        $moonset  = Arr::get($astronomyData, 'astronomy.astro.moonset');


        if($timezone) {

            if($sunrise) {
                $dateFormat = sprintf("%s %s", now()->toDateString(), $sunrise);
                $sunriseTime = Carbon::createFromFormat(
                    'Y-m-d h:i A',
                    $dateFormat,
                    $timezone
                );

                $astro->setSunrise($sunriseTime);
            }

            if($sunset) {
                $dateFormat = sprintf("%s %s", now()->toDateString(), $sunset);
                $sunsetTime = Carbon::createFromFormat(
                    'Y-m-d h:i A',
                    $dateFormat,
                    $timezone
                );

                $astro->setSunset($sunsetTime);
            }

            if($moonrise) {
                $dateFormat = sprintf("%s %s", now()->toDateString(), $moonrise);
                $moonriseTime = Carbon::createFromFormat(
                    'Y-m-d h:i A',
                    $dateFormat,
                    $timezone
                );

                $astro->setMoonrise($moonriseTime);
            }

            if($moonset) {
                $dateFormat = sprintf("%s %s", now()->toDateString(), $moonset);
                $moonsetTime = Carbon::createFromFormat(
                    'Y-m-d h:i A',
                    $dateFormat,
                    $timezone
                );

                $astro->setMoonset($moonsetTime);
            }
        }

        $weather->setWind($wind)->setLocation($weatherLocation)->setAstronomy($astro);

        return $weather;
    }
}
