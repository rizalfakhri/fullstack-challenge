<?php

namespace App\Console\Commands;

use App\Contracts\Weather\WeatherProvider;
use App\Models\User;
use App\Services\Geo\GeoCoordinate;
use Illuminate\Console\Command;

class BuildWeatherCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:build-weather-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To help build weather cache without interupting user experience.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->line('Building cache...');
        $users = User::all();

        foreach ($users as $user) {
            $geoCoordinate = GeoCoordinate::make($user->latitude, $user->longitude);

            app(WeatherProvider::class)->getCurrentWeather($geoCoordinate);
        }

        $this->info("Cache built and ready.");
    }
}
