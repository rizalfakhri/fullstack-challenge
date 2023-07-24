<?php

namespace App\Console\Commands;

use App\Services\Geo\GeoCoordinate;
use Sk\Geohash\Geohash;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 't';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Example usage:
        $centralLatitude  = -6.401994716099995;
        $centralLongitude = 106.79402488730072;
        $radiusKm         = 100;
        $numPoints        = 1000;
        $hashes           = [];

        $randomCoordinates = $this->generate_random_coordinate($centralLatitude, $centralLongitude, $radiusKm, $numPoints);

        $g = new Geohash();
        foreach($randomCoordinates as $coordinate) {
            [$lat, $long] = $coordinate;

            $coordinate[] = $g->encode($lat, $long, 10);

            $hashes[] = $coordinate;
        }

        Cache::put('test_cache', json_encode($hashes), now()->addHour());

        //$data = collect(Cache::get('test_cache'))->random();

        //[$lat, $long] = $g->decode($data);
        //dd(GeoCoordinate::make($lat, $long));


    }

    public function generate_random_coordinate($centerLatitude, $centerLongitude, $radiusKm, $numPoints) {
        $coordinates = [];
        $earthRadiusKm = 6371;

        for ($i = 0; $i < $numPoints; $i++) {
            // Convert latitude and longitude to radians
            $centerLatRad = deg2rad($centerLatitude);
            $centerLonRad = deg2rad($centerLongitude);

            // Generate random bearing (in radians) and distance (in km)
            $randomBearingRad = deg2rad(mt_rand(0, 360));
            $randomDistanceKm = mt_rand(0, $radiusKm * 1000) / 1000;

            // Convert distance to radians
            $distanceRad = $randomDistanceKm / $earthRadiusKm;

            // Calculate new latitude and longitude
            $newLatRad = asin(sin($centerLatRad) * cos($distanceRad) + cos($centerLatRad) * sin($distanceRad) * cos($randomBearingRad));
            $newLonRad = $centerLonRad + atan2(sin($randomBearingRad) * sin($distanceRad) * cos($centerLatRad), cos($distanceRad) - sin($centerLatRad) * sin($newLatRad));

            // Convert new latitude and longitude back to degrees
            $newLatitude = rad2deg($newLatRad);
            $newLongitude = rad2deg($newLonRad);

            $coordinates[] = [$newLatitude, $newLongitude];
        }

        return $coordinates;
    }
}
