<?php

use App\Registries\WeatherProviderRegistry;

test('weather provider config is set', function() {
    expect(config('services.openweathermap'))->not()->toBeEmpty();
    expect(config('services.weather_gov'))->not()->toBeEmpty();
});

it('successfully register weather registry to container', function() {
    expect(app(WeatherProviderRegistry::class))->toBeInstanceOf(WeatherProviderRegistry::class);
});

it('registers all weather providers', function() {
    $availableProviders  = config('weather.providers');
    $registeredProviders = app(WeatherProviderRegistry::class)->all();

    expect(count($registeredProviders))->toEqual(count($availableProviders));
});

