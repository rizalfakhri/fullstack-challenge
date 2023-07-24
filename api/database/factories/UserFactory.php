<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // better coordinate generation with predefined center point.
        [$latitude, $longitude] = $this->generate_random_coordinate();

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'longitude' => $longitude,
            'latitude' => $latitude,
        ];
    }


    public function generate_random_coordinate() {
        $centerLatitude  = -6.401994716099995;
        $centerLongitude = 106.79402488730072;
        $radiusKm = 100;
        $earthRadiusKm = 6371;

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

        return [$newLatitude, $newLongitude];
    }
}
