<?php

namespace App\Contracts\Weather;

use App\Enums\TemperatureUnit;

interface Temperature {

    /**
     * Set the temperature.
     *
     * @param  int|float $temperature
     * @param  TemperatureUnit $unit
     * @return self
     */
    public function setTemperature(int|float $temperature, TemperatureUnit $unit);

    /**
     * Get the temperature.
     *
     * @return int|float
     */
    public function getTemperature() : string;
}
