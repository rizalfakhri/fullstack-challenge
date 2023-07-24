<?php

namespace App\Services\Weather;

use App\Enums\TemperatureUnit;
use App\Contracts\Weather\Temperature as TemperatureContract;

final class Temperature implements TemperatureContract {

    /**
     * The temperature.
     *
     * @var  int|float $temperature
     */
    protected $temperature;

    /**
     * The temperature unit.
     *
     * @var  TemperatureUnit $unit
     */
    protected $unit;

    /**
     * Set the temperature.
     *
     * @param  int|float $temperature
     * @param  TemperatureUnit $unit
     * @return self
     */
    public function setTemperature(int|float $temperature, TemperatureUnit $unit) {
        $this->unit        = $unit;
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Get the temperature.
     *
     * @return int|float
     */
    public function getTemperature() : string {
        return sprintf("%s%s", $this->temperature, utf8_encode($this->unit->value));
    }
}
