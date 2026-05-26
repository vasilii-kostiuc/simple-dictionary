<?php

namespace App\Core\Shared\Cache;

interface CacheInterface
{
    public function remember(string $key, int $ttl, callable $callback): mixed;

    public function rememberForever(string $key, callable $callback): mixed;

    public function forget(string $key): void;
}
