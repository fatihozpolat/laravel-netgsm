# Laravel Netgsm Metotları ve Bildirim Kanalını içerir

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fatihozpolat/laravel-netgsm.svg?style=flat-square)](https://packagist.org/packages/fatihozpolat/laravel-netgsm)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/fatihozpolat/laravel-netgsm/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/fatihozpolat/laravel-netgsm/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/fatihozpolat/laravel-netgsm/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/fatihozpolat/laravel-netgsm/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/fatihozpolat/laravel-netgsm.svg?style=flat-square)](https://packagist.org/packages/fatihozpolat/laravel-netgsm)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.


## Installation

You can install the package via composer:

```bash
composer require fatihozpolat/laravel-netgsm
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-netgsm-config"
```

This is the contents of the published config file:

```php
return [
    'url' => env('NETGSM_URL', 'https://api.netgsm.com.tr'),
    'username' => env('NETGSM_USERNAME'),
    'password' => env('NETGSM_PASSWORD'),
    'header' => env('NETGSM_HEADER'),
    'tenant' => env('NETGSM_TENANT'),
    'language' => env('NETGSM_LANGUAGE', 'TR'),
];
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Fatih Özpolat](https://github.com/fatihozpolat)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
