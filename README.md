# YandexMoney Provider for OAuth 2.0 Client

This package provides Yandex Money OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

This package is compliant with [PSR-1][], [PSR-2][] and [PSR-4][]. If you notice compliance oversights, please send
a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

## Requirements

The following versions of PHP are supported.

* PHP 5.5
* PHP 5.6
* PHP 7.0
* HHVM

## Installation

To install, use composer:

```
composer require kudmni/oauth2-yandex-money
```

## Usage

### Authorization Code Flow

```php
$provider = new PrCy\OAuth2\Client\Provider\YandexMoney([
    'clientId'     => '{yandex-money-app-id}',
    'clientSecret' => '{yandex-money-app-secret}',
    'redirectUri'  => 'https://example.com/callback-url',
]);

if (!empty($_GET['error'])) {

    // Got an error, probably user denied access
    exit('Got error: ' . $_GET['error']);

} elseif (empty($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->state;
    header('Location: ' . $authUrl);
    exit;

} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    // State is invalid, possible CSRF attack in progress
    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up an account data
    try {

        // We got an access token, let's now get the owner details
        $ownerDetails = $provider->getResourceOwner($token);

        // Use these details
        printf('Your account is %s', $ownerDetails->getAccount());

    } catch (Exception $e) {

        // Failed to get account details
        exit('Something went wrong: ' . $e->getMessage());

    }

    // Use this to interact with an API on the account behalf
    echo $token->accessToken;

    // Use this to get a new access token if the old one expires
    echo $token->refreshToken;

    // Number of seconds until the access token will expire, and need refreshing
    echo $token->expires;
}
```

### Refreshing a Token

```php
$provider = new PrCy\OAuth2\Client\Provider\YandexMoney([
    'clientId'     => '{yandex-money-app-id}',
    'clientSecret' => '{yandex-money-app-secret}',
    'redirectUri'  => 'https://example.com/callback-url',
]);

$grant = new PrCy\OAuth2\Client\Grant\RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/kudmni/oauth2-yandex-money/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Woody Gilk](https://github.com/shadowhand)
- [All Contributors](https://github.com/kudmni/oauth2-yandex-money/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/kudmni/oauth2-yandex-money/blob/master/LICENSE) for more information.
