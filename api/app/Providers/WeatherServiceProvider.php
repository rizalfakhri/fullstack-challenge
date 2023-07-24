<?php

namespace App\Providers;

use App\Services\Weather\Wind;
use App\Services\Weather\Location;
use App\Services\Weather\Weather;
use App\Services\Weather\Forecast;
use App\Services\Weather\Astronomy;
use App\Services\Weather\Temperature;
use App\Contracts\Weather\WeatherProvider;
use App\Registries\WeatherProviderRegistry;
use App\Contracts\Weather\Wind as WindContract;
use App\Contracts\Weather\Location as LocationContract;
use App\Contracts\Weather\Astronomy as AstronomyContract;
use App\Contracts\Weather\Temperature as TemperatureContract;
use App\Contracts\Weather\Forecast as WeatherForecast;
use App\Contracts\Weather\Weather as WeatherContract;
use App\Services\WeatherProviders\OpenWeatherMapProvider;
use App\Services\WeatherProviders\WeatherApiProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class WeatherServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WeatherProviderRegistry::class);

        $this->registerWeatherProviderBindings();
        $this->registerWeatherProviders();

        $this->app->bind(WeatherProvider::class, function(Application $app) {
            return $app->make(WeatherProviderRegistry::class)->get(config('weather.default'));
        });

        $this->app->bind(WeatherContract::class, Weather::class);
        $this->app->bind(WeatherForecast::class, Forecast::class);
        $this->app->bind(WindContract::class, Wind::class);
        $this->app->bind(TemperatureContract::class, Temperature::class);
        $this->app->bind(LocationContract::class, Location::class);
        $this->app->bind(AstronomyContract::class, Astronomy::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    private function registerWeatherProviderBindings() {
        $this->app->bind(OpenWeatherMapProvider::class, fn () => new OpenWeatherMapProvider(config('services.openweathermap')));
        $this->app->bind(WeatherApiProvider::class, fn () => new WeatherApiProvider(config('services.weather_api')));
    }

    private function registerWeatherProviders() {
        $providers = config('weather.providers') ?: [];

        foreach($providers as $key => $provider) {
            $this->app->make(WeatherProviderRegistry::class)->register(
                $key, $this->app->make($provider)
            );
        }
    }
}
