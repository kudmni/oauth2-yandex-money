<?php

namespace PrCy\OAuth2\Client\Test\Provider;

use PrCy\OAuth2\Client\Provider\YandexMoney as YandexMoneyProvider;

use Mockery as m;

class YandexMoneyTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;

    protected function setUp()
    {
        $this->provider = new YandexMoneyProvider([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none'
        ]);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);

        $this->assertContains('account-info', $query['scope']);

        $this->assertAttributeNotEmpty('state', $this->provider);
    }

    public function testBaseAccessTokenUrl()
    {
        $url = $this->provider->getBaseAccessTokenUrl([]);
        $uri = parse_url($url);

        $this->assertEquals('/oauth/token', $uri['path']);
    }

    public function testResourceOwnerDetailsUrl()
    {
        $token = m::mock('League\OAuth2\Client\Token\AccessToken', [['access_token' => 'mock_access_token']]);

        $url = $this->provider->getResourceOwnerDetailsUrl($token);
        $uri = parse_url($url);

        $this->assertEquals('/api/account-info', $uri['path']);
        $this->assertNotContains('mock_access_token', $url);

    }

    public function testAccountata()
    {
        $response = json_decode('{"account":"4100123456789","balance": 1000.00,"currency":"643","account_status":"anonymous","account_type":"personal","avatar": {"url":"http://avatars.yandex.net/get-yamoney-profile/yamoney-profile-30633298/normal","ts":"2013-03-13T20:43:00.000+04:00"},"cards_linked": [{"pan_fragment":"510000******9999","type":"MasterCard"}]}', true);

        $provider = m::mock('PrCy\OAuth2\Client\Provider\YandexMoney[fetchResourceOwnerDetails]')
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('fetchResourceOwnerDetails')
            ->times(1)
            ->andReturn($response);

        $token = m::mock('League\OAuth2\Client\Token\AccessToken');
        $account = $provider->getResourceOwner($token);

        $this->assertInstanceOf('League\OAuth2\Client\Provider\ResourceOwnerInterface', $account);

        $this->assertEquals(4100123456789, $account->getId());
        $this->assertEquals(1000.00, $account->getBalance());
        $this->assertEquals('643', $account->getCurrency());
        $this->assertEquals('anonymous', $account->getAccountStatus());
        $this->assertEquals('personal', $account->getAccountType());
        $this->assertArrayHasKey('url', $account->getAvatar());
        $this->assertNull($account->getBalanceDetails());
        $this->assertCount(1, $account->getCardsLinked());

        $account = $account->toArray();

        $this->assertArrayHasKey('account', $account);
        $this->assertArrayHasKey('balance', $account);
        $this->assertArrayHasKey('currency', $account);
        $this->assertArrayHasKey('account_status', $account);
        $this->assertArrayHasKey('account_type', $account);
        $this->assertArrayHasKey('avatar', $account);
        $this->assertArrayHasKey('cards_linked', $account);
    }

    /**
     * @expectedException League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function testErrorResponse()
    {
        $response = m::mock('GuzzleHttp\Psr7\Response');

        $response->shouldReceive('getHeader')
            ->with('content-type')
            ->andReturn(['application/json']);

        $response->shouldReceive('getBody')
            ->andReturn('{"error":400,"error_description":"I am an error"}');

        $provider = m::mock('PrCy\OAuth2\Client\Provider\YandexMoney[sendRequest]')
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('sendRequest')
            ->times(1)
            ->andReturn($response);

        $token = m::mock('League\OAuth2\Client\Token\AccessToken');
        $user = $provider->getResourceOwner($token);
    }
}
