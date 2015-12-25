# PHP API Client Library for Wrike Project Management

This package provides easy access to the Wrike API in your PHP application.

## Installation

To install, use Composer:

```
composer require isevltd/wrike-php
```

## Usage

### Add your Wrike client app credentials

Wrike uses OAuth2 to authenticate and track API requests. So in order to use the API you will need to register an API client app first. You can do that [here](https://developers.wrike.com/getting-started/).

```php
// Register your credentials globally
\IsevLtd\Wrike\Client::registerCredentials([
    'client_id'     => '{your-client-id}',
    'client_secret' => '{your-client-secret}',
    'redirect_url'  => '{your-apps-callback-url}'
]);

$wrike = new \IsevLtd\Wrike\Client;

- OR -

// Per-instance credentials
$wrike = new \IsevLtd\Wrike\Client([
    'client_id'     => '{your-client-id}',
    'client_secret' => '{your-client-secret}',
    'redirect_url'  => '{your-apps-callback-url}'
]);

- THEN -

// Call methods

$wrike->getFolders();
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/isevltd/Wrike-PHP/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Simon Hamp](https://github.com/simonhamp)
- [All Contributors](https://github.com/isevltd/Wrike-PHP/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/isevltd/Wrike-PHP/blob/master/LICENSE) for more information.
