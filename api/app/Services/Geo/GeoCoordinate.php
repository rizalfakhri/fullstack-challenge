<?php

namespace App\Services\Geo;

use App\Contracts\Geo\GeoCoordinate as GeoCoordinateContract;
use Sk\Geohash\Geohash;

final class GeoCoordinate implements GeoCoordinateContract {

    /**
     * Hash Precision Level
     *
     * @var  string
     */
    private const PRECISION_LEVEL = 9;

    /**
     * The coordinate latitude.
     *
     * @var float $latitude
     */
    protected float $latitude;

    /**
     * The coordinate longitude.
     *
     * @var float $longitude
     */
    protected float $longitude;

    /**
     * The coordinate geohash.
     *
     * @var string $hash
     */
    protected string $hash;

    /**
     * Build new GeoCoordinate instance.
     *
     * @param  float $latitude
     * @param  float $longitude
     * @return void
     */
    public function __construct(float $latitude, float $longitude)
    {
       $this->latitude  = $latitude;
       $this->longitude = $longitude;

       $hasher = new Geohash;
       $this->hash = $hasher->encode($latitude, $longitude, self::PRECISION_LEVEL);
    }

    /**
     * Return the latitude.
     *
     * @return float
     */
    public function getLatitude() : float {
        return $this->latitude;
    }

    /**
     * Return the longitude.
     *
     * @return float
     */
    public function getLongitude() : float {
        return $this->longitude;
    }

    /**
     * Return the geohash.
     *
     * @return string
     */
    public function getHash() : string {
        return $this->hash;
    }

    /**
     * Make new GeoCoordinate instance.
     *
     * @param  float $latitude
     * @param  float $longitude
     * @return GeoCoordinate
     */
    public static function make(float $latitude, float $longitude) : GeoCoordinate
    {
        return new static($latitude, $longitude);
    }

}
