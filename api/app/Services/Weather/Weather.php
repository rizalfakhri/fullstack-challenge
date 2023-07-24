<?php

namespace App\Services\Weather;

use App\Contracts\Geo\GeoCoordinate;
use App\Contracts\Weather\Wind;
use App\Contracts\Weather\Temperature;
use App\Contracts\Weather\Astronomy;
use App\Contracts\Weather\Concerns\HasForecast;
use App\Contracts\Weather\Forecast;
use App\Contracts\Weather\Location;
use App\Contracts\Weather\Weather as WeatherContract;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class Weather implements WeatherContract, Arrayable, HasForecast {

    /**
     * The Current Weather condition.
     *
     * @var  string $condition
     */
    protected ?string $condition = '';

    /**
     * The current Weather condition description.
     *
     * @var  string $description
     */
    protected ?string $description = '';

    /**
     * The current Weather condition icon url.
     *
     * @var  string $iconUrl
     */
    protected ?string $iconUrl = '';

    /**
     * The current Weather location.
     *
     * @var  Location $location
     */
    protected ?Location $location;

    /**
     * The current Weather temperature set.
     *
     * @var  array<Temperature> $temperatures
     */
    protected array $temperatures = [];

    /**
     * The current Weather wind condition.
     *
     * @var  Wind $wind
     */
    protected ?Wind $wind;

    /**
     * The current Weather astronomy.
     *
     * @var  Astronomy $astronomy
     */
    protected ?Astronomy $astronomy;

    /**
     * The weather forecast.
     *
     * @var  array<Forecast> $forecast
     */
    protected $forecast = [];

    /**
     * Set current weather condition.
     *
     * @param  string $condition
     * @return self
     */
    public function setWeatherCondition(string $condition) {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Set the description for the current weather condition.
     *
     * @param  string $description
     * @return self
     */
    public function setWeatherConditionDescription(string $description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the icon for the current weather condition.
     *
     * @param  string $iconUrl
     * @return self
     */
    public function setWeatherConditionIcon(string $iconUrl) {
        $this->iconUrl = $iconUrl;

        return $this;
    }

    /**
     * Add temperature to the current weather temperature set.
     *
     * @param  Temperature $temperature
     * @return self
     */
    public function addTemperature(Temperature $temperature) {
        $this->temperatures[] = $temperature;

        return $this;
    }

    /**
     * Set the current weather wind condition.
     *
     * @param  Wind $wind
     * @return self
     */
    public function setWind(Wind $wind) {
        $this->wind = $wind;

        return $this;
    }

    /**
     * Set the Astronomy object for current weather.
     *
     * @param  Astronomy $astronomy
     * @return self
     */
    public function setAstronomy(Astronomy $astronomy) {
        $this->astronomy = $astronomy;

        return $this;
    }

    /**
     * Set the location of the current weather condition.
     *
     * @param  Location $location
     * @return self
     */
    public function setLocation(Location $location) {
        $this->location = $location;

        return $this;
    }


    /**
     * Get current weather condition.
     *
     * @return  string
     */
    public function getWeatherCondition() : ?string {
        return $this->condition;
    }

    /**
     * Get the description for the current weather condition.
     *
     * @return  string
     */
    public function getWeatherConditionDescription() : ?string {
        return $this->description;
    }

    /**
     * Get the icon for the current weather condition.
     *
     * @return  string
     */
    public function getWeatherConditionIcon() : ?string {
        return $this->iconUrl;
    }

    /**
     * Get the current weather temperature set.
     *
     * @return array<Temperature>
     */
    public function getTemperatures() : array {
        return $this->temperatures;
    }

    /**
     * Get the current weather wind condition.
     *
     * @return  Wind
     */
    public function getWind() : ?Wind {
        return $this->wind;
    }

    /**
     * Get the Astronomy object for current weather.
     *
     * @return  Astronomy
     */
    public function getAstronomy() : ?Astronomy {
        return $this->astronomy;
    }

    /**
     * Get the location of the current weather condition.
     *
     * @return  Location
     */
    public function getLocation() : ?Location {
        return $this->location;
    }

    /**
     * Set the weather forecast.
     *
     * @param  array<Forecast> $forecast
     * @return self
     */
    public function setForecast(array $forecast) {
        $this->forecast = $forecast;
    }

    /**
     * Get the weather forecast.
     *
     * @return array<Forecast>
     */
    public function getForecast() : array {
        return $this->forecast;
    }

    /**
     * Transform weather data into array.
     *
     * @return array
     */
    public function toArray()
    {
        $response = [
            'condition' => $this->getWeatherCondition(),
            'condition_description' => $this->getWeatherConditionDescription(),
            'icon' => $this->getWeatherConditionIcon()
        ];

        if($this->getLocation() instanceof Location) {
            $location   = $this->getLocation();
            $coordinate = $location->getCoordinate();

            $response['location'] = [
                'city' => $location->getCity(),
                'country' => $location->getCountry(),
                'timezone' => $location->getTimezone()
            ];

            if($coordinate instanceof GeoCoordinate) {
                $response['location']['latitude'] = $coordinate->getLatitude();
                $response['location']['longitude'] = $coordinate->getLongitude();
            }
        }

        if($this->getWind() instanceof Wind) {
            $wind = $this->getWind();

            $response['wind'] = [
                'speed' => $wind->getWindSpeed(),
                'direction' => $wind->getWindDirection(),
                'gust' => $wind->getWindGust(),
                'degree' => $wind->getWindDegree()
            ];
        }

        if($this->getAstronomy() instanceof Astronomy) {
            $astronomy = $this->getAstronomy();

            $response['astronomy'] = [
                'sunrise_at' => $astronomy->getSunrise(),
                'sunset_at' => $astronomy->getSunset(),
                'moonrise_at' => $astronomy->getMoonrise(),
                'moonset_at' => $astronomy->getMoonset()
            ];
        }

        if(!empty($this->getTemperatures())) {

            $temperatures = [];

            foreach($this->getTemperatures() as $temperature) {
                if($temperature instanceof Temperature) {
                    $temperatures[] = $temperature->getTemperature();
                }
            }

            $response['temperatures'] = $temperatures;
        }

        if(!empty($this->getForecast())) {
            $response['forecast'] = collect($this->getForecast())->map(function(Forecast $forecast) {
                $toReturn = [
                    'forecasted_date' => null,
                    'forecasted_weather' => null
                ];

                if($forecast->getDate() instanceof Carbon) {
                    $toReturn['forecasted_date'] = $forecast->getDate()->format('d/m/Y h:i A');
                }

                $forecastedWeather = $forecast->getWeather();

                if($forecastedWeather instanceof Arrayable) {
                    $toReturn['forecasted_weather'] = $forecastedWeather->toArray();
                }

                return $toReturn;
            });
        }

        return $response;
    }
}
