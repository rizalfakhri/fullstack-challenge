<?php

namespace App\Services\Geo\Spatial\QuadTree;

final class Node {

    /**
     * The Node Latitude.
     *
     * @var float $latitude
     */
    public float $latitude;

    /**
     * The Node Longitude.
     *
     * @var float $longitude
     */
    public float $longitude;

    /**
     * The Node Geohash.
     *
     * @var string $hash
     */
    public string $hash;

    /**
     * The North East Bounding Box.
     *
     * @var Node $NE
     */
    public ?Node $NE;

    /**
     * The North West Bounding Box.
     *
     * @var Node $NW
     */
    public ?Node $NW;

    /**
     * The South East Bounding Box.
     *
     * @var Node $SE
     */
    public ?Node $SE;

    /**
     * The South West Bounding Box.
     *
     * @var Node $SW
     */
    public ?Node $SW;

    /**
     * Build the node.
     *
     * @param  float $latitude
     * @param  float $longitude
     * @param  string $hash
     * @return void
     */
    public function __construct(float $latitude, float $longitude, string $hash)
    {
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
        $this->hash      = $hash;
        $this->NE        = null;
        $this->NW        = null;
        $this->SE        = null;
        $this->SW        = null;
    }
}

