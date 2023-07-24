<?php

namespace App\Contracts\Weather;

use App\Enums\SpeedUnit;

interface Wind {

    /**
     * Set the wind speed.
     *
     * @param  int|float $speed
     * @param  SpeedUnit $unit
     * @return self
     */
    public function setWindSpeed(int|float $speed, SpeedUnit $unit);

    /**
     * Set the wind direction.
     *
     * @param  string $direction
     * @return self
     */
    public function setWindDirection(string $direction);

    /**
     * Set the wind gust.
     *
     * @param  int|float $speed
     * @param  SpeedUnit $unit
     * @return self
     */
    public function setWindGust(int|float $gust, SpeedUnit $unit);

    /**
     * Set the wind degree.
     *
     * @param  int|float $degree
     * @return self
     */
    public function setWindDegree(int|float $degree);

    /**
     * Get the wind speed.
     *
     * @return string|null
     */
    public function getWindSpeed() : ?string;

    /**
     * Get the wind direction.
     *
     * @return string|null
     */
    public function getWindDirection() : ?string;

    /**
     * Get the wind gust.
     *
     * @return string|null
     */
    public function getWindGust() : ?string;

    /**
     * Get the wind degree.
     *
     * @return int|float|null
     */
    public function getWindDegree(): int|float|null;
}
