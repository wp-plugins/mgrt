<?php

namespace Mgrt\Tests\Command;

use Mgrt\Model\ApiKey;

class ApiKeysTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testListApiKeysReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'api-keys/listApiKeys',
        ));
        $apiKeys = $this->client->getApiKeys();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $apiKeys);
        $this->assertEquals($apiKeys->count(), 2);
        foreach ($apiKeys as $apiKey) {
            $this->assertInstanceOf('\Mgrt\Model\ApiKey', $apiKey);
        }
    }

    public function testGetApiKeyReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'api-keys/getApiKey',
        ));
        $apiKey = $this->client->getApiKey(1);

        $this->assertInstanceOf('\Mgrt\Model\ApiKey', $apiKey);
    }

    public function testCreateApiKeyReturnsCorrectData()
    {
        $apiKey = new ApiKey();
        $apiKey->setName('test');

        $this->setMockResponse($this->client, array(
            'api-keys/createApiKey',
        ));
        $result = $this->client->createApiKey($apiKey);

        $this->assertInstanceOf('\Mgrt\Model\ApiKey', $result);
    }

    public function testUpdateApiKeyReturnsCorrectData()
    {
        $apiKey = new ApiKey();
        $apiKey->setId(1);
        $apiKey->setName('test');

        $this->setMockResponse($this->client, array(
            'api-keys/updateApiKey',
        ));
        $result = $this->client->updateApiKey($apiKey);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testEnableApiKeyReturnsCorrectData()
    {
        $apiKey = new ApiKey();
        $apiKey->setId(1);

        $this->setMockResponse($this->client, array(
            'api-keys/enableApiKey',
        ));
        $result = $this->client->enableApiKey($apiKey);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testDisableApiKeyReturnsCorrectData()
    {
        $apiKey = new ApiKey();
        $apiKey->setId(1);

        $this->setMockResponse($this->client, array(
            'api-keys/disableApiKey',
        ));
        $result = $this->client->disableApiKey($apiKey);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testDeleteApiKeyReturnsCorrectData()
    {
        $apiKey = new ApiKey();
        $apiKey->setId(1);

        $this->setMockResponse($this->client, array(
            'api-keys/deleteApiKey',
        ));
        $result = $this->client->deleteApiKey($apiKey);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }
}
