<?php

namespace App\Infrastructure\Cache;

use App\Core\Shared\Cache\CacheInterface;
use Illuminate\Contracts\Cache\Repository;

class LaravelCache implements CacheInterface
{
    public function __construct(private readonly Repository $cache) {}

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        return $this->cache->remember($key, $ttl, $callback);
    }

    public function rememberForever(string $key, callable $callback): mixed
    {
        return $this->cache->rememberForever($key, $callback);
    }

    public function forget(string $key): void
    {
        $this->cache->forget($key);
    }
}
