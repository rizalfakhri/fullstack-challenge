<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Contracts\Weather\WeatherProvider;
use App\Services\Geo\GeoCoordinate;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GetWeatherReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $paginatedUsers = User::paginate(20);

        if($paginatedUsers instanceof LengthAwarePaginator) {

            $weatherReportPromises = [];

            $paginatedUsers->getCollection()->each(function($user) use(&$weatherReportPromises) {

                $promise = new Promise(
                    function() use(&$promise, $user) {

                        $geoCoordinate = GeoCoordinate::make($user->latitude, $user->longitude);

                        try {
                            $promise->resolve(app(WeatherProvider::class)->getCurrentWeather($geoCoordinate)->toArray());
                        } catch(\Exception $e) {
                            $promise->resolve([]);
                        }

                    }
                );

                $weatherReportPromises[] = $promise;

            });


            $results = collect(Utils::all($weatherReportPromises)->wait());

            $paginatedUsers->setCollection(
                $paginatedUsers->getCollection()->map(function($user, $index) use($results) {
                    $user->weather_report = $results[$index];

                    return $user;
                })
            );

        }

        return $paginatedUsers;
    }
}
