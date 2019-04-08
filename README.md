# Cache Null

By default in Laravel, as per [PSR-16](https://www.php-fig.org/psr/psr-16/#14-data), cached null values are inistingustable from a cache miss:

```
A cache miss will return null and therefore detecting if one stored null is not possible.
```

```php
Cache::forever('test1a', null);
$exists = Cache::has('test1a'); // returns FALSE

$value = Cache::rememberForever('test1b', function() {
    print('calculating');
    return null;
}); // 'calculating' prints every single time
```

Install this package if you want to enable support for caching null values:

```php
Cache::forever('test2a', null);
$exists = Cache::has('test2a'); // returns TRUE

$value = Cache::rememberForever('test2b', function() {
    print('calculating');
    return null;
}); // 'calculating' prints only the first time
```

## Installation

```bash
composer require cvsouth/cache-null
```

## Notices

The only cache driver currently supported by this package is `redis`.
