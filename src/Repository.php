<?php

namespace Cvsouth\CacheNull;

use Closure;
use Illuminate\Cache\Repository as BaseRepository;

class Repository extends BaseRepository
{
    /**
     * Determine if an item exists in the cache.
     *
     * @param  string  $key
     * @param  bool  $allowNull
     * @return bool
     */
    public function has($key, $allowNull = true)
    {
        if($allowNull) {
            return $this->store->exists($key);
        }
        
        return ! is_null($this->get($key));
    }

    /**
     * Determine if an item doesn't exist in the cache.
     *
     * @param  string  $key
     * @param  bool  $allowNull
     * @return bool
     */
    public function missing($key, $allowNull = true)
    {
        return ! $this->has($key, $allowNull);
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @param  \Closure  $callback
     * @param  bool  $allowNull
     * @return mixed
     */
    public function remember($key, $ttl, Closure $callback, $allowNull = true)
    {
        if($allowNull) {
            if($this->store->exists($key)) {
                return $this->get($key);
            }
        } else {
            $value = $this->get($key);

            // If the item exists in the cache we will just return this immediately and if
            // not we will execute the given Closure and cache the result of that for a
            // given number of seconds so it's available for all subsequent requests.
            if (! is_null($value)) {
                return $value;
            }
        }
        
        $this->put($key, $value = $callback(), value($ttl));

        return $value;
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     * @param  string  $key
     * @param  \Closure  $callback
     * @param  bool  $allowNull
     * @return mixed
     */
    public function sear($key, Closure $callback, $allowNull = true)
    {
        return $this->rememberForever($key, $callback, $allowNull);
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     * @param  string  $key
     * @param  \Closure  $callback
     * @param  bool  $allowNull
     * @return mixed
     */
    public function rememberForever($key, Closure $callback, $allowNull = true)
    {
        return $this->remember($key, null, $callback, $allowNull);
    }

    /**
     * Store an item in the cache if the key does not exist.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @param  bool  $allowNull
     * @return bool
     */
    public function add($key, $value, $ttl = null, $allowNull = true)
    {
        $seconds = null;

        if ($ttl !== null) {
            $seconds = $this->getSeconds($ttl);

            if ($seconds <= 0) {
                return false;
            }

            // If the store has an "add" method we will call the method on the store so it
            // has a chance to override this logic. Some drivers better support the way
            // this operation should work with a total "atomic" implementation of it.
            if (method_exists($this->store, 'add')) {
                return $this->store->add(
                    $this->itemKey($key), $value, $seconds
                );
            }
        }

        // If the value did not exist in the cache, we will put the value in the cache
        // so it exists for subsequent requests. Then, we will return true so it is
        // easy to know if the value gets added. Otherwise, we will return false.
        if ($allowNull ? !$this->has($key) : is_null($this->get($key))) {
            return $this->put($key, $value, $seconds);
        }

        return false;
    }
}
