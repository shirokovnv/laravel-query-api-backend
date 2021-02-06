# LaravelQueryApiBackend

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

Unified API for running queries with eloquent models from any http client.

For query data format and usage in frontend see (this package)[https://github.com/shirokovnv/laravel-query-api-frontend]

> The package is in beta testing right now. 

## Installation

Via Composer

``` bash
$ composer require shirokovnv/laravel-query-api-backend
```

## Usage

Ensure all migrations done

```bash
    php artisan migrate
```

Once installed you can do stuff like this in Controller:

```php
    $queryRunner = LaravelQueryApiBackend::makeQueryRunnerInstance($request, $options);
    $queryResult = $queryRunner->run();
    $queryRunner->saveLog();
      
    return response()->json($queryResult);
```

$request is Illuminate\Http\Request or Illuminate\Foundation\Http\FormRequest

For available options see config section

Request must contain following keys:

> query_data

> query_mode

Example controller: 

> Shirokovnv\LaravelQueryApiBackend\Http\Controllers\QueryApiController

### Available query modes:

> transaction 

runs a couple of queries as a whole. If one of the queries fails,
transaction will be rolled back.

> multiple

runs a couple of queries individually.

all occurred errors will be added to the error pool with the rest of the result.


## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email shirokovnv@gmail.com instead of using the issue tracker.

## Credits

- [Nickolai Shirokov][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/shirokovnv/laravel-query-api-backend.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/shirokovnv/laravel-query-api-backend.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/shirokovnv/laravel-query-api-backend/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/shirokovnv/laravel-query-api-backend
[link-downloads]: https://packagist.org/packages/shirokovnv/laravel-query-api-backend
[link-travis]: https://travis-ci.org/shirokovnv/laravel-query-api-backend
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/shirokovnv
[link-contributors]: ../../contributors
