<?php
namespace FuelSdk\Test;
use FuelSdk\ET_Client;
use FuelSdk\ET_Asset;
use PHPUnit\Framework\TestCase;

final class OAuth2Test extends TestCase
{
    private $client;

    function __construct()
    {
        $this->client = new ET_Client(true);
    }

    public function testIfAuthTokenAndRefreshTokenDifferIfRefreshTokenIsEnforced()
    {
        $reflection = new \ReflectionClass(get_class($this->client));
        $clientid = $reflection->getProperty("clientId");
        $clientid->setAccessible(true);
        echo $clientid->getValue($clientid);
        $token = $this->client->getAuthToken();
        $refreshToken = $this->client->getRefreshToken(null);
        $this->client->refreshTokenWithOAuth2(true);

        $newtoken = $this->client->getAuthToken();
        $newrefreshToken = $this->client->getRefreshToken(null);

        $this->assertNotEquals($token, $newtoken);
        $this->assertNotEquals($refreshToken, $newrefreshToken);
    }
}
?>