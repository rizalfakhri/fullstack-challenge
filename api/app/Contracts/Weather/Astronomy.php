<?php

namespace App\Contracts\Weather;

use Carbon\Carbon;

interface Astronomy {

    /**
     * Set the sunrise local time.
     *
     * @param  Carbon $localTime
     * @return self
     */
    public function setSunrise(Carbon $localTime);

    /**
     * Set the sunset local time.
     *
     * @param  Carbon $localTime
     * @return self
     */
    public function setSunset(Carbon $localTime);

    /**
     * Set the moonrise local time.
     *
     * @param  Carbon $localTime
     * @return self
     */
    public function setMoonrise(Carbon $localTime);

    /**
     * Set the moonset local time.
     *
     * @param  Carbon $localTime
     * @return self
     * */
    public function setMoonset(Carbon $localtime);

    /**
     * Get the sunrise local time.
     *
     * @return Carbon|null
     */
    public function getSunrise() : ?Carbon;

    /**
     * Get the sunset local time.
     *
     * @return Carbon|null
     */
    public function getSunset() : ?Carbon;

    /**
     * Get the moonrise local time.
     *
     * @return Carbon|null
     */
    public function getMoonrise() : ?Carbon;

    /**
     * Get the moonset local time.
     *
     * @return Carbon|null
     */
    public function getMoonset() : ?Carbon;
}
