<?php

namespace PrCy\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class YandexMoney extends AbstractProvider
{
    use BearerAuthorizationTrait;

    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'account';

    public function getBaseAuthorizationUrl()
    {
        return 'https://money.yandex.ru/oauth/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://money.yandex.ru/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://money.yandex.ru/api/account-info';
    }

    protected function getDefaultScopes()
    {
        return ['account-info'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            throw new IdentityProviderException(
                $data['error_description'],
                $data['error'],
                $data
            );
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new YandexMoneyAccount($response);
    }
}
