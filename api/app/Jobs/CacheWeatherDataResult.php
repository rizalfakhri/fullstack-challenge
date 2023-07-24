<?php

namespace App\Jobs;

use App\Contracts\Geo\GeoCoordinate;
use App\Services\Geo\Spatial\QuadTree\Node;
use App\Services\Geo\Spatial\QuadTree\QuadTree;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class CacheWeatherDataResult implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Cache TTL.
     *
     * @var  int $ttl
     */
    protected $ttl = 60; // 1 hour

    /**
     * The cache key string.
     *
     * @var  string $cacheKey
     */
    protected string $cacheKey;

    /**
     * The GeoCoordinate instance.
     *
     */
    protected GeoCoordinate $coordinate;

    /**
     * The JSON encoded data to be cached.
     *
     * @var  string $resultToCache
     */
    protected string $resultToCache;

    /**
     * Create a new job instance.
     *
     * @param  string $cacheKey The key to where the cache should be saved.
     * @param  GeoCoordinate $coordinate Represent which coordinate and
     *                                   geohash the result should be assigned to
     * @param  string $dataToCache JSON encoded weather result that should be cached.
     */
    public function __construct(string $cacheKey, GeoCoordinate $coordinate, string $resultToCache)
    {
        $this->cacheKey      = $cacheKey;
        $this->coordinate    = $coordinate;
        $this->resultToCache = $resultToCache;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cachedData = Cache::get($this->cacheKey);

        if($cachedData) {
            $this->appendToExistingCache($cachedData);
        }
        else
        {
            $dataToCache = [
                'first_cached_at' => now()->timestamp,
                'serialized_root_tree' => '',
                'cached_hash' => []
            ];

            $dataToCache['cached_hash'][$this->coordinate->getHash()] = $this->resultToCache;

            $tree = new QuadTree;
            $tree->addNode($this->coordinate);

            $dataToCache['serialized_root_tree'] = serialize($tree->getRootNode());

            Cache::put($this->cacheKey, json_encode($dataToCache), now()->addMinutes($this->ttl));
        }
    }


    private function appendToExistingCache(string $cachedData) {
        $cachedData    = json_decode($cachedData, true);
        $firstCachedAt = Carbon::createFromTimestamp(Arr::get($cachedData, 'first_cached_at'));
        $remainingTtl  = $this->ttl - $firstCachedAt->diffInMinutes(now());


        // preparing to append the cached data
        Cache::forget($this->cacheKey);

        if($remainingTtl < 1 || $remainingTtl > $this->ttl) {
            // cache stale, re-cache data

            return app()->call([$this, 'handle']);
        }

        $root = unserialize(Arr::get($cachedData, 'serialized_root_tree'));

        if($root instanceof Node) {
            $tree = new QuadTree($root);
            $tree->addNode($this->coordinate);

            $cachedData['serialized_root_tree'] = serialize($tree->getRootNode());
        }

        $cachedData['cached_hash'][$this->coordinate->getHash()] = $this->resultToCache;
        Cache::put($this->cacheKey, json_encode($cachedData), now()->addMinutes($remainingTtl));
    }
}
