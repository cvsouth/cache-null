<?php

namespace Cvsouth\CacheNull;

use Illuminate\Cache\RedisStore as BaseRedisStore;

class RedisStore extends BaseRedisStore
{
    /**
     * Determine whether a key exists in the cache.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key)
    {
        $value = $this->connection()->exists($this->prefix.$key);

        return $value === 1;
    }
}
