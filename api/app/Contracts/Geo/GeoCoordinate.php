<?php

namespace App\Contracts\Geo;

interface GeoCoordinate {

    /**
     * Return the latitude.
     *
     * @return float
     */
    public function getLatitude() : float;

    /**
     * Return the longitude.
     *
     * @return float
     */
    public function getLongitude() : float;

    /**
     * Return the geohash.
     *
     * @return string
     */
    public function getHash() : string;
}
