<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected $cacheDuration = 3600;

    public function remember($key, $callback)
    {
        return Cache::remember($key, $this->cacheDuration, $callback);
    }

    public function forget(string $key)
    {
        Cache::forget($key);
    }
}
