<?php

namespace App\Contracts\Weather\Concerns;

use App\Contracts\Weather\Forecast;

interface HasForecast {

    /**
     * Set the weather forecast.
     *
     * @param  array<Forecast> $forecast
     * @return self
     */
    public function setForecast(array $forecast);

    /**
     * Get the weather forecast.
     *
     * @return array<Forecast>
     */
    public function getForecast() : array;
}
