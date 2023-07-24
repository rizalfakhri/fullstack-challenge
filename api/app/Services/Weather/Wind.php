<?php

namespace App\Services\Weather;

use App\Enums\SpeedUnit;
use App\Contracts\Weather\Wind as WindContract;

final class Wind implements WindContract {

    /**
     * The wind speed.
     *
     * @var  int|float $speed
     */
    protected int|float $speed = 0;

    /**
     * The wind speed unit.
     *
     * @var  SpeedUnit|null $unit
     */
    protected ?SpeedUnit $unit = null;

    /**
     * The wind direction.
     *
     * @var  string $direction
     */
    protected string $direction = '';

    /**
     * The wind gust.
     *
     * @var  int|float $gust
     */
    protected int|float $gust = 0;

    /**
     * The wind gust speed unit.
     *
     * @var  SpeedUnit|null $gustUnit
     */
    protected ?SpeedUnit $gustUnit = null;

    /**
     * The wind degree.
     *
     * @var  int|float|null $degree
     */
    protected int|float|null $degree = 0;

    /**
     * Set the wind speed.
     *
     * @param  int|float $speed
     * @param  SpeedUnit $unit
     * @return self
     */
    public function setWindSpeed(int|float $speed, SpeedUnit $unit) {
        $this->speed = $speed;
        $this->unit  = $unit;

        return $this;
    }

    /**
     * Set the wind direction.
     *
     * @param  string $direction
     * @return self
     */
    public function setWindDirection(string $direction) {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Set the wind gust.
     *
     * @param  int|float $speed
     * @param  SpeedUnit $unit
     * @return self
     */
    public function setWindGust(int|float $gust, SpeedUnit $unit) {
        $this->gust     = $gust;
        $this->gustUnit = $unit;

        return $this;
    }

    /**
     * Set the wind degree.
     *
     * @param  int|float $degree
     * @return self
     */
    public function setWindDegree(int|float $degree) {
        $this->degree = $degree;

        return $this;
    }

    /**
     * Get the wind speed.
     *
     * @return string|null
     */
    public function getWindSpeed() : ?string {
        return sprintf("%s %s", $this->speed, $this->unit->value);
    }

    /**
     * Get the wind direction.
     *
     * @return string|null
     */
    public function getWindDirection() : ?string {
        return $this->direction;
    }

    /**
     * Get the wind gust.
     *
     * @return string|null
     */
    public function getWindGust() : ?string {
        if(!$this->gust || !$this->gustUnit) {
            return null;
        }
        return sprintf("%s %s", $this->gust, $this->gustUnit->value);
    }

    /**
     * Get the wind degree.
     *
     * @return int|float|null
     */
    public function getWindDegree(): int|float|null {
        return $this->degree;
    }
}
