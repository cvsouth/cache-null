<?php

namespace Cvsouth\CacheNull\Providers;

use Cvsouth\CacheNull\Repository;
use Cvsouth\CacheNull\RedisStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

class Laravel extends BaseServiceProvider
{
    public function boot()
    {
        Cache::extend('redis', function($app)
        {
            $store = new RedisStore($app['redis'], app('config')['cache.prefix'], $config['connection'] ?? 'default');
            $repository = new Repository($store);

            if($app->bound(DispatcherContract::class)) {
                $repository->setEventDispatcher($app[DispatcherContract::class]);
            }

            return $repository;
        });
    }

    public function register()
    {

    }
}
