<?php

namespace oval\Services\Lti1p3;

use Packback\Lti1p3;

class LtiCache implements Lti1p3\Interfaces\ICache
{
    public const NONCE_PREFIX = 'nonce_';
    private $cache;

    public function __construct()
    {
        $this->cache = \Cache::store('file');
    }

    public function getLaunchData(string $key): ?array
    {
        return $this->cache->get($key);
    }

    public function cacheLaunchData(string $key, array $jwtBody): void
    {
        $duration = \Config::get('cache.duration.default');
        $this->cache->put($key, $jwtBody, $duration);
    }

    public function cacheNonce(string $nonce, string $state): void
    {
        $duration = \Config::get('cache.duration.default');
        $this->cache->put(static::NONCE_PREFIX.$nonce, $state, $duration);
    }

    public function checkNonceIsValid(string $nonce, string $state): bool
    {
        return $this->cache->get(static::NONCE_PREFIX.$nonce, false) === $state;
    }

    public function cacheAccessToken(string $key, string $accessToken): void
    {
        $duration = \Config::get('cache.duration.min');
        $this->cache->put($key, $accessToken, $duration);
    }

    public function getAccessToken(string $key): ?string
    {
        return $this->cache->has($key) ? $this->cache->get($key) : null;
    }

    public function clearAccessToken(string $key): void
    {
        $this->cache->forget($key);
    }
}
