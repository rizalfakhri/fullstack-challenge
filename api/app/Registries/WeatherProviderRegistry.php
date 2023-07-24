<?php

namespace App\Registries;

use App\Contracts\Weather\WeatherProvider;

final class WeatherProviderRegistry {

    /**
     * Registered WeatherProvider instances.
     *
     * @var array<WeatherProvider> $providers
     */
    protected $providers = [];

    /**
     * Register new  WeatherProvider instance.
     *
     * @param  string $key
     * @param  WeatherProvider $provider
     * @param  bool $override
     * @return void
     */
    public function register($key, WeatherProvider $provider, $override = true) {
        if($this->has($key)) {
            $this->providers[$key] = $override ? $provider : $this->get($key);
        }
        else
        {
            $this->providers[$key] = $provider;
        }
    }

    /**
     * Determine if WeatherProvider with the given key exists.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key) {
        return isset($this->providers[$key]);
    }

    /**
     * Get WeatherProvider by its key.
     *
     * @param  string $key
     * @return WeatherProvider|void
     */
    public function get($key) {
        if($this->has($key)) return $this->providers[$key];
    }

    /**
     * Get all registered WeatherProvider.
     *
     * @return array<WeatherProvider>
     */
    public function all() {
        return $this->providers;
    }

}
