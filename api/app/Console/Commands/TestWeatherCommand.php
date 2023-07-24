<?php

namespace App\Console\Commands;

use App\Contracts\Weather\WeatherProvider;
use App\Services\Geo\GeoCoordinate;
use App\Services\Geo\Spatial\QuadTree\QuadTree;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TestWeatherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'r';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //$data = json_decode(Cache::get('test_cache'), true);
        //$data[] = [
            //-6.630180504493775, 105.86662150395223, 'aa'
        //];

        //$root = null;

        //$q = new QuadTree();

        //foreach($data as $i => $coordinate) {
            //[$lat, $long] = $coordinate;

            //$gc = GeoCoordinate::make($lat, $long);

            //$q->addNode($gc);

            //if($i == 0) {
                //$root = $q->getRootNode();

                ////$q = new QuadTree($root);
            //}
        //}

        //$tosearch = GeoCoordinate::make(-5.048133015580096, 105.20818840235245);

        //dd($q->findClosestHash($tosearch));



        //dd($q);

        $weatherProvider = app(WeatherProvider::class);


        dd($weatherProvider->getCurrentWeather(GeoCoordinate::make(-6.356400295925741, 106.72704406220986)));
    }
}
