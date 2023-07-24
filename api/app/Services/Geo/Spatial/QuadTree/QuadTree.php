<?php

namespace App\Services\Geo\Spatial\QuadTree;

use App\Contracts\Geo\GeoCoordinate;

final class QuadTree {

    /**
     * Tree Depth Limitation
     *
     * @var  string
     */
    protected $depth = 10;

    /**
     * The QuadTree root.
     *
     * @var Node $root
     */

    public function __construct(?Node $root = null, $depth = 10)
    {
        $this->root  = $root;
        $this->depth = $depth;
    }

    public function getRootNode() {
        return $this->root;
    }

    public function addNode(GeoCoordinate $coordinate) {
        $this->root = $this->addNodeRecursive($this->root, $coordinate);
    }

    private function addNodeRecursive(?Node $node = null, GeoCoordinate $coordinate) {
        if (!$node) {
            return new Node(
                $coordinate->getLatitude(),
                $coordinate->getLongitude(),
                $coordinate->getHash()
            );
        }

        --$this->depth;

        if($this->depth == 0) {
            return $node;
        }

        $latitude  = $coordinate->getLatitude();
        $longitude = $coordinate->getLongitude();
        $hash      = $coordinate->getHash();


        $currentGeohashPrecision = strlen($node->hash);
        $newGeohashPrecision = strlen($hash);

        // Find the common prefix between the current hash and the new hash
        $commonPrefixLength = 0;
        while ($commonPrefixLength < $currentGeohashPrecision && $commonPrefixLength < $newGeohashPrecision &&
            $node->hash[$commonPrefixLength] === $hash[$commonPrefixLength]) {
            $commonPrefixLength++;
        }

        if ($commonPrefixLength === $newGeohashPrecision) {

            $node->NW = $this->addNodeRecursive($node->NW, $coordinate);

        } elseif ($commonPrefixLength === $currentGeohashPrecision) {

            if ($latitude >= $node->latitude) {
                if ($longitude >= $node->longitude) {
                    $node->NE = $this->addNodeRecursive($node->NE, $coordinate);
                } else {
                    $node->NW = $this->addNodeRecursive($node->NW, $coordinate);
                }
            } else {
                if ($longitude >= $node->longitude) {
                    $node->SE = $this->addNodeRecursive($node->SE, $coordinate);
                } else {
                    $node->SW = $this->addNodeRecursive($node->SW, $coordinate);
                }
            }
        } else {
            // The new geohash and the current geohash diverge at some point
            // Create a new node to represent the common prefix
            $commonPrefix = substr($hash, 0, $commonPrefixLength);
            $commonNode = new Node($latitude, $longitude, $commonPrefix);

            // Determine the correct position of the new and current nodes as children of the common node
            if ($latitude >= $node->latitude) {
                if ($longitude >= $node->longitude) {
                    $commonNode->NE = $this->addNodeRecursive($commonNode->NE, $coordinate);
                    $commonNode->NW = $node;
                } else {
                    $commonNode->NW = $this->addNodeRecursive($commonNode->NW, $coordinate);
                    $commonNode->NE = $node;
                }
            } else {
                if ($longitude >= $node->longitude) {
                    $commonNode->SE = $this->addNodeRecursive($commonNode->SE, $coordinate);
                    $commonNode->SW = $node;
                } else {
                    $commonNode->SW = $this->addNodeRecursive($commonNode->SW, $coordinate);
                    $commonNode->SE = $node;
                }
            }

            return $commonNode;
        }

        return $node;
    }


    public function findClosestHash(GeoCoordinate $coordinate, $radius = 5) {
        $latitude  = $coordinate->getLatitude();
        $longitude = $coordinate->getLongitude();

        if($this->getRootNode() instanceof Node && $this->getRootNode()->hash == $coordinate->getHash()) {
            return $coordinate->getHash();
        }

        $closestHash     = null;
        $closestDistance = PHP_FLOAT_MAX;
        $this->findClosestHashRecursive($this->root, $latitude, $longitude, $radius, $closestHash, $closestDistance);

        return $closestHash;
    }

    private function findClosestHashRecursive(?Node $node, $latitude, $longitude, $radius, &$closestHash, &$closestDistance) {
        if (!$node) {
            return;
        }

        $distance = $this->calculateDistance($node->latitude, $node->longitude, $latitude, $longitude);

        if ($distance <= $radius && $distance < $closestDistance) {
            $closestHash = $node->hash;
            $closestDistance = $distance;
            return;
        }

        $latitudeDiff = abs($latitude - $node->latitude);
        $longitudeDiff = abs($longitude - $node->longitude);

        if ($latitudeDiff < $radius || $longitudeDiff < $radius) {
            $this->findClosestHashRecursive($node->NW, $latitude, $longitude, $radius, $closestHash, $closestDistance);
            $this->findClosestHashRecursive($node->NE, $latitude, $longitude, $radius, $closestHash, $closestDistance);
            $this->findClosestHashRecursive($node->SW, $latitude, $longitude, $radius, $closestHash, $closestDistance);
            $this->findClosestHashRecursive($node->SE, $latitude, $longitude, $radius, $closestHash, $closestDistance);
        }
    }

    /**
     * Calculate the distance between 2 coordinates using haversine formula.
     *
     * @see https://en.wikipedia.org/wiki/Haversine_formula
     *
     * @param  float $lat1
     * @param  float $lon1
     * @param  float $lat2
     * @param  float $lon2
     * @return float
     * */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2) : float {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

}
