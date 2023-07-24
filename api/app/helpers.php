<?php


/**
 * Convert UTC seconds offset to readable timezone.
 *
 * @param  int|null $ofset
 * @return string|bool
 */
function tz_offset_to_name(?int $offset) : string|bool|null
{
    if(!$offset) {
        return false;
    }

    $abbrarray = timezone_abbreviations_list();
    foreach ($abbrarray as $abbr)
    {
        foreach ($abbr as $city)
        {
            if ($city['offset'] == $offset)
            {
                return $city['timezone_id'];
            }
        }
    }

    return false;
}
