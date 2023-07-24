<?php

namespace App\Services\Weather;

use Carbon\Carbon;
use App\Contracts\Weather\Weather;
use App\Contracts\Weather\Location;
use App\Contracts\Weather\Forecast as WeatherForecast;

final class Forecast implements WeatherForecast {

    /**
     * The forecast date.
     *
     * @var  Carbon $date
     */
    protected Carbon $date;

    /**
     * The forecasted weather.
     *
     * @var  Weather $weather
     */
    protected $weather;

    /**
     * The Location of the forecasted weather.
     *
     * @var  Location $location
     */
    protected $location;

    public function __construct(?Carbon $date, ?Weather $weather, ?Location $location)
    {
       $this->date     = $date;
       $this->weather  = $weather;
       $this->location = $location;
    }

    /**
     * The date this forecast is for.
     *
     * @return Carbon
     */
    public function getDate() : Carbon {
        return $this->date;
    }

    /**
     * The weather condition for this particular forecast.
     *
     * @return Weather
     */
    public function getWeather() : Weather {
        return $this->weather;
    }

    /**
     * The location for this forecast.
     *
     * @return Location
     */
    public function getLocation() : Location {
        return $this->location;
    }

    /**
     * Statically build the forecast data.
     *
     * @inheritdoc
     */
    public static function make(?Carbon $date, ?Weather $weather, ?Location $location) : static {
        return new static($date, $weather, $location);
    }
}
