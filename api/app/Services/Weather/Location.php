<?php

namespace App\Services\Weather;

use Carbon\Carbon;
use App\Contracts\Geo\GeoCoordinate;
use App\Contracts\Weather\Location as LocationContract;

final class Location implements LocationContract {


    /**
     * The location coordinate.
     *
     * @var  GeoCoordinate $coordinate
     */
    protected ?GeoCoordinate $coordinate = null;

    /**
     * The location local time.
     *
     * @var  Carbon $localTime
     */
    protected Carbon $localTime;

    /**
     * The city.
     *
     * @var  string $city
     */
    protected $city;

    /**
     * The country.
     *
     * @var  string $country
     */
    protected $country;

    /**
     * The location timezone.
     *
     * @var  string $timezone
     */
    protected $timezone;

    /**
     * Set the location coordinate.
     *
     * @param  GeoCoordinate $coordinate
     * @return self
     */
    public function setCoordinate(GeoCoordinate $coordinate) {
        $this->coordinate = $coordinate;

        return $this;
    }

    /**
     * Set local time.
     *
     * @param  Carbon $localtime
     * @return self
     */
    public function setLocalTime(Carbon $localTime) {
        $this->localTime = $localTime;

        return $this;
    }

    /**
     * Set the city.
     *
     * @param  string $city
     * @return self
     * */
    public function setCity(string $city) {
        $this->city = $city;

        return $this;
    }

    /**
     * Set the country.
     *
     * @param  string $country
     * @return self
     * */
    public function setCountry(string $country) {
        $this->country = $country;

        return $this;
    }

    /**
     * Set the timezone.
     *
     * @param  string $timezone
     * @return self
     * */
    public function setTimezone(string $timezone) {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get the location coordinate.
     *
     * @return GeoCoordinate
     */
    public function getCoordinate() : ?GeoCoordinate {
        return $this->coordinate;
    }

    /**
     * Get the local time.
     *
     * @return Carbon|null
     */
    public function getLocalTime() : ?Carbon {
        return $this->localTime;
    }

    /**
     * Get the city.
     *
     * @return string
     */
    public function getCity() : string {
        return $this->city;
    }

    /**
     * Get the country.
     *
     * @return string
     */
    public function getCountry() : string {
        return $this->country;
    }

    /**
     * Get the timezone.
     *
     * @return string
     */
    public function getTimezone() : string {
        return $this->timezone;
    }
}
