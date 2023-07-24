<?php

namespace App\Contracts\Weather;

use Carbon\Carbon;

interface Forecast {

    /**
     * The date this forecast is for.
     *
     * @return Carbon
     */
    public function getDate() : Carbon;

    /**
     * The weather condition for this particular forecast.
     *
     * @return Weather
     */
    public function getWeather() : Weather;

    /**
     * The location for this forecast.
     *
     * @return Location
     */
    public function getLocation() : Location;

}
