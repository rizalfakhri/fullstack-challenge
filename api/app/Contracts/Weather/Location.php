<?php

namespace App\Contracts\Weather;

use App\Contracts\Geo\GeoCoordinate;
use Carbon\Carbon;

interface Location {

    /**
     * Set the location coordinate.
     *
     * @param  GeoCoordinate $coordinate
     * @return self
     */
    public function setCoordinate(GeoCoordinate $coordinate);

    /**
     * Set local time.
     *
     * @param  Carbon $localtime
     * @return self
     */
    public function setLocalTime(Carbon $localTime);

    /**
     * Set the city.
     *
     * @param  string $city
     * @return self
     * */
    public function setCity(string $city);

    /**
     * Set the country.
     *
     * @param  string $country
     * @return self
     * */
    public function setCountry(string $country);

    /**
     * Set the timezone.
     *
     * @param  string $timezone
     * @return self
     * */
    public function setTimezone(string $timezone);

    /**
     * Get the location coordinate.
     *
     * @return GeoCoordinate|null
     */
    public function getCoordinate() : ?GeoCoordinate;

    /**
     * Get the local time.
     *
     * @return Carbon|null
     */
    public function getLocalTime() : ?Carbon;

    /**
     * Get the city.
     *
     * @return string
     */
    public function getCity() : string;

    /**
     * Get the country.
     *
     * @return string
     */
    public function getCountry() : string;

    /**
     * Get the timezone.
     *
     * @return string
     */
    public function getTimezone() : string;
}
