<?php

namespace App\Contracts\Weather;

use InvalidArgumentException;
use App\Contracts\Geo\GeoCoordinate;
use App\Exceptions\WeatherRequestException;

interface WeatherProvider {

    /**
     * Get the current weather for the given GeoCoordinate object.
     *
     * @param  GeoCoordinate $coordinate
     * @return Weather
     * @throws InvalidArgumentException
     * @throws WeatherRequestException
     */
    public function getCurrentWeather(GeoCoordinate $coordinate) : Weather;
}
