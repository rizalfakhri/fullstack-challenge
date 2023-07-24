<?php

namespace App\Services\WeatherProviders;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Enums\SpeedUnit;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use App\Enums\TemperatureUnit;
use App\Contracts\Weather\Wind;
use App\Contracts\Weather\Weather;
use Illuminate\Support\Collection;
use App\Contracts\Geo\GeoCoordinate;
use App\Contracts\Weather\Location;
use App\Contracts\Weather\Astronomy;
use App\Contracts\Weather\Concerns\HasForecast;
use App\Services\Weather\Forecast;
use App\Contracts\Weather\Temperature;
use App\Contracts\Weather\WeatherProvider;
use App\Exceptions\InvalidWeatherDataException;
use App\Exceptions\WeatherRequestException;
use App\Services\Geo\GeoCoordinate as GeoCoordinateService;
use App\Jobs\CacheWeatherDataResult;
use App\Services\Geo\Spatial\QuadTree\Node;
use App\Services\Geo\Spatial\QuadTree\QuadTree;
use Illuminate\Support\Facades\Cache;
use Pest\Support\Arr;

final class OpenWeatherMapProvider implements WeatherProvider {

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
    protected $endpoint = 'https://api.openweathermap.org/data/2.5/';

    protected const CACHE_KEY = 'openweathermap.cached_weather';

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
        $query = [
            'lat' => $coordinate->getLatitude(),
            'lon' => $coordinate->getLongitude(),
            'appid' => Arr::get($this->config, 'api_key'),
            'units' => 'metric'
        ];

        $dataEndpointClient      = new Client(['base_uri' => $this->endpoint, 'query' => $query]);

        try {

            $currentWeatherPromise = $dataEndpointClient->getAsync('weather');
            $forecastPromise       = $dataEndpointClient->getAsync('forecast');

            $results = collect(Utils::all([
                'current_weather' => $currentWeatherPromise,
                'forecast'        => $forecastPromise,
            ])->wait())->map(function(Response $response) {
                return $response->getBody()->getContents();
            })->toArray();

            return $results;

        } catch(\Exception $e) {
            throw new WeatherRequestException("Unexpected Error during weather request.", 500, $e);
        }

    }

    private function formatWeatherData(array $data = []) {
        $currentWeather = Arr::get($data, 'current_weather');
        $forecast       = Arr::get($data, 'forecast');

        $formattedData = [
            'current_weather' => $this->generateWeatherFromData(json_decode($currentWeather, true)),
            'forecast' => []
        ];

        $currentForecast = json_decode($forecast, true) ?: [];

        $forecastedCity = Arr::get($currentForecast, 'city');

        $city           = Arr::get($forecastedCity, 'name');
        $country        = Arr::get($forecastedCity, 'country');
        $timezoneOffset = Arr::get($forecastedCity, 'timezone');
        $timezone       = tz_offset_to_name($timezoneOffset);
        $coordinate     = Arr::get($forecastedCity, 'coord');
        $sunrise        = Arr::get($forecastedCity, 'sunrise');
        $sunset         = Arr::get($forecastedCity, 'sunset');

        $location = app(Location::class);
        if($city) $location->setCity($city);
        if($country) $location->setCountry($country);
        if($timezone) $location->setTimezone($timezone);
        if($coordinate) {
            $location->setCoordinate(
                GeoCoordinateService::make(
                    Arr::get($coordinate, 'lat'),
                    Arr::get($coordinate, 'lon')
                )
            );
        }

        $astronomy = app(Astronomy::class);
        if($sunrise && $sunset) {
            $sunriseLocalTime = Carbon::createFromTimestamp($sunrise)->timezone($timezone);
            $sunsetLocalTime  = Carbon::createFromTimestamp($sunset)->timezone($timezone);

            $astronomy->setSunrise($sunriseLocalTime)->setSunset($sunsetLocalTime);
        }

        $forecastData = collect(Arr::get($currentForecast, 'list', []))->map(function($forecast) use($astronomy, $location, $timezone) {

            $date = Arr::get($forecast, 'dt');

            // no forecast date, skip weather formatting.
            if(!$date) {
                return;
            }

            $forecastDate      = Carbon::createFromTimestamp($date)->timezone($timezone);
            $forecastedWeather = $this->generateWeatherFromData($forecast);

            if($forecastedWeather instanceof Weather) {
                $forecastedWeather->setAstronomy($astronomy)->setLocation($location);
            }

            return Forecast::make($forecastDate, $forecastedWeather, $location);
        })->toArray();

        $formattedData['forecast'] = $forecastData;

        return $formattedData;
    }

    private function generateWeatherFromData(array $data = []) : Weather {
        $weather = app(Weather::class);

        $userTimezoneOffset = Arr::get($data, 'timezone');
        $userTimezone       = tz_offset_to_name($userTimezoneOffset);

        $weatherCondition = Arr::get($data, 'weather.0');
        $weatherData      = Arr::get($data, 'main');

        $weather->setWeatherCondition(Arr::get($weatherCondition, 'main'));
        $weather->setWeatherConditionDescription(Arr::get($weatherCondition, 'description'));
        $weather->setWeatherConditionIcon(
            sprintf("https://openweathermap.org/img/wn/%s@2x.png", Arr::get($weatherCondition, 'icon'))
        );

        $weather->addTemperature(
            app(Temperature::class)->setTemperature(
                Arr::get($weatherData, 'temp'),
                TemperatureUnit::CELCIUS
            )
        );

        $windData = Arr::get($data, 'wind');

        $windSpeed     = Arr::get($windData, 'speed');
        $windDegree    = Arr::get($windData, 'deg');
        $windGust      = Arr::get($windData, 'gust');
        $windDirection = Arr::get($windData, 'direction');

        $wind = App(Wind::class);

        if($windSpeed) $wind->setWindSpeed($windSpeed, SpeedUnit::MPS);
        if($windDegree) $wind->setWindDegree($windDegree);
        if($windGust) $wind->setWindGust($windGust, SpeedUnit::MPS);
        if($windDirection) $wind->setWindDirection($windDirection);

        $weather->setWind($wind);

        $sysData = Arr::get($data, 'sys');

        $country = Arr::get($sysData, 'country');
        $sunrise = Arr::get($sysData, 'sunrise');
        $sunset  = Arr::get($sysData, 'sunset');

        $astronomy = app(Astronomy::class);

        if($sunrise && $sunset) {
            $sunriseLocalTime = Carbon::createFromTimestamp($sunrise)->timezone($userTimezone);
            $sunsetLocalTime  = Carbon::createFromTimestamp($sunset)->timezone($userTimezone);

            $astronomy->setSunrise($sunriseLocalTime)->setSunset($sunsetLocalTime);
        }

        $weather->setAstronomy($astronomy);

        $location = app(Location::class);

        $city       = Arr::get($data, 'name');
        $coordinate = Arr::get($data, 'coord');

        if($coordinate) {
            $location->setCoordinate(
                GeoCoordinateService::make(
                    Arr::get($coordinate, 'lat'),
                    Arr::get($coordinate, 'lon')
                )
            );
        }

        if($userTimezone) $location->setTimezone($userTimezone);
        if($city) $location->setCity($city);
        if($country) $location->setCountry($country);

        $weather->setLocation($location);

        return $weather;
    }

}
