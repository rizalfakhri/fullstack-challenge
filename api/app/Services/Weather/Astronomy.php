<?php

namespace App\Services\Weather;

use Carbon\Carbon;
use App\Contracts\Weather\Astronomy as AstronomyContract;

final class Astronomy implements AstronomyContract {

    /**
     * The sunrise local time.
     *
     * @var  Carbon $sunriseLocalTime
     */
    protected ?Carbon $sunriseLocalTime = null;

    /**
     * The sunset local time.
     *
     * @var  Carbon $sunsetLocalTime
     */
    protected ?Carbon $sunsetLocalTime = null;


    /**
     * The moonrise local time.
     *
     * @var  Carbon $moonriseLocalTime
     */
    protected ?Carbon $moonriseLocalTime = null;

    /**
     * The moonset local time.
     *
     * @var  Carbon $moonsetLocalTime
     */
    protected ?Carbon $moonsetLocalTime = null;

    /**
     * Set the sunrise local time.
     *
     * @param  Carbon $localTime
     * @return self
     */
    public function setSunrise(Carbon $localTime) {
        $this->sunriseLocalTime = $localTime;

        return $this;
    }

    /**
     * Set the sunset local time.
     *
     * @param  Carbon $localTime
     * @return self
     */
    public function setSunset(Carbon $localTime) {
        $this->sunsetLocalTime = $localTime;

        return $this;
    }

    /**
     * Set the moonrise local time.
     *
     * @param  Carbon $localTime
     * @return self
     */
    public function setMoonrise(Carbon $localTime) {
        $this->moonriseLocalTime = $localTime;

        return $this;
    }

    /**
     * Set the moonset local time.
     *
     * @param  Carbon $localTime
     * @return self
     * */
    public function setMoonset(Carbon $localtime) {
        $this->moonsetLocalTime = $localtime;

        return $this;
    }

    /**
     * Get the sunrise local time.
     *
     * @return Carbon|null
     */
    public function getSunrise() : ?Carbon {
        if(!$this->sunriseLocalTime) {
            return null;
        }

        return $this->sunriseLocalTime;
    }

    /**
     * Get the sunset local time.
     *
     * @return Carbon|null
     */
    public function getSunset() : ?Carbon {
        if(!$this->sunsetLocalTime) {
            return null;
        }

        return $this->sunsetLocalTime;
    }

    /**
     * Get the moonrise local time.
     *
     * @return Carbon|null
     */
    public function getMoonrise() : ?Carbon {
        if(!$this->moonriseLocalTime) {
            return null;
        }

        return $this->moonriseLocalTime;
    }

    /**
     * Get the moonset local time.
     *
     * @return Carbon|null
     */
    public function getMoonset() : ?Carbon {
        if(!$this->moonsetLocalTime) {
            return null;
        }

        return $this->moonsetLocalTime;
    }
}
