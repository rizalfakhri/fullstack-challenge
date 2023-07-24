<?php

use App\Services\WeatherProviders\OpenWeatherMapProvider;
use App\Services\WeatherProviders\WeatherApiProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Weather Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default weather provider that will be used on
    | requests. By default, "openweathermap" will be used, but you may
    | specify any of the other wonderful provider provided here.
    |
    | Supported: "openweathermap", "weather_api"
    |
    */

    'default' => 'weather_api',

    /*
    |--------------------------------------------------------------------------
    | Cached Weather Data Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may specify the number of minutes that you wish the cached
    | weather to be allowed to remain cached before it get flushed.
    |
    */

    'lifetime' => 60,


    /*
    |--------------------------------------------------------------------------
    | Weather Providers
    |--------------------------------------------------------------------------
    |
    | Here you may register another weather data provider.
    |
    */

    'providers' => [
        'openweathermap' => OpenWeatherMapProvider::class, /* @see https://openweathermap.org */
        'weather_api'    => WeatherApiProvider::class, /* @see https://weatherapi.com */
    ]

];
