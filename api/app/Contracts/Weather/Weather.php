<?php

namespace App\Contracts\Weather;

interface Weather {

    /**
     * Set current weather condition.
     *
     * @param  string $condition
     * @return self
     */

    public function setWeatherCondition(string $condition);

    /**
     * Set the description for the current weather condition.
     *
     * @param  string $description
     * @return self
     */

    public function setWeatherConditionDescription(string $description);

    /**
     * Set the icon for the current weather condition.
     *
     * @param  string $iconUrl
     * @return self
     */

    public function setWeatherConditionIcon(string $iconUrl);

    /**
     * Add temperature to the current weather temperature set.
     *
     * @param  Temperature $temperature
     * @return self
     */

    public function addTemperature(Temperature $temperature);

    /**
     * Set the current weather wind condition.
     *
     * @param  Wind $wind
     * @return self
     */

    public function setWind(Wind $wind);

    /**
     * Set the Astronomy object for current weather.
     *
     * @param  Astronomy $astronomy
     * @return self
     */

    public function setAstronomy(Astronomy $astronomy);

    /**
     * Set the location of the current weather condition.
     *
     * @param  Location $location
     * @return self
     */

    public function setLocation(Location $location);


    /**
     * Get current weather condition.
     *
     * @return  string
     */

    public function getWeatherCondition() : ?string;

    /**
     * Get the description for the current weather condition.
     *
     * @return  string
     */

    public function getWeatherConditionDescription() : ?string;

    /**
     * Get the icon for the current weather condition.
     *
     * @return $iconUrl
     */

    public function getWeatherConditionIcon() : ?string;

    /**
     * Get the current weather temperature set.
     *
     * @return  array<Temperature>
     */

    public function getTemperatures() : array;

    /**
     * Get the current weather wind condition.
     *
     * @return  Wind
     */

    public function getWind() : ?Wind;

    /**
     * Get the Astronomy object for current weather.
     *
     * @return  Astronomy
     */

    public function getAstronomy() : ?Astronomy;

    /**
     * Get the location of the current weather condition.
     *
     * @return  Location
     */

    public function getLocation() : ?Location;

}
