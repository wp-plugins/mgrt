<?php

namespace Mgrt\Tests\Command;

use Mgrt\Model\Webhook;

class WebhooksTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testListWebhooksReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'webhooks/listWebhooks',
        ));
        $webhooks = $this->client->getWebhooks();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $webhooks);
        $this->assertEquals($webhooks->count(), 2);
        foreach ($webhooks as $webhook) {
            $this->assertInstanceOf('\Mgrt\Model\Webhook', $webhook);
        }
    }

    public function testGetWebhookReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'webhooks/getWebhook',
        ));
        $webhook = $this->client->getWebhook('00000000-0000-0000-0000-000000000000');

        $this->assertInstanceOf('\Mgrt\Model\Webhook', $webhook);
    }

    public function testCreateWebhookReturnsCorrectData()
    {
        $webhook = new Webhook();
        $webhook->setName('test');
        $webhook->setCallbackUrl('http://example.com');

        $this->setMockResponse($this->client, array(
            'webhooks/createWebhook',
        ));
        $result = $this->client->createWebhook($webhook);

        $this->assertInstanceOf('\Mgrt\Model\Webhook', $webhook);
    }

    public function testUpdateWebhookReturnsCorrectData()
    {
        $webhook = new Webhook();
        $webhook->setId('00000000-0000-0000-0000-000000000000');
        $webhook->setName('test');
        $webhook->setCallbackUrl('http://example.com');

        $this->setMockResponse($this->client, array(
            'webhooks/updateWebhook',
        ));
        $result = $this->client->updateWebhook($webhook);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testResetKeyWebhookReturnsCorrectData()
    {
        $webhook = new Webhook();
        $webhook->setId('00000000-0000-0000-0000-000000000000');

        $this->setMockResponse($this->client, array(
            'webhooks/resetKeyWebhook',
        ));
        $result = $this->client->resetKeyWebhook($webhook);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testTriggerTestWebhookReturnsCorrectData()
    {
        $webhook = new Webhook();
        $webhook->setId('00000000-0000-0000-0000-000000000000');

        $this->setMockResponse($this->client, array(
            'webhooks/triggerTestWebhook',
        ));
        $result = $this->client->triggerTestWebhook($webhook);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testEnableWebhookReturnsCorrectData()
    {
        $webhook = new Webhook();
        $webhook->setId('00000000-0000-0000-0000-000000000000');

        $this->setMockResponse($this->client, array(
            'webhooks/enableWebhook',
        ));
        $result = $this->client->enableWebhook($webhook);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testDisableWebhookReturnsCorrectData()
    {
        $webhook = new Webhook();
        $webhook->setId('00000000-0000-0000-0000-000000000000');

        $this->setMockResponse($this->client, array(
            'webhooks/disableWebhook',
        ));
        $result = $this->client->disableWebhook($webhook);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testDeleteWebhookReturnsCorrectData()
    {
        $webhook = new Webhook();
        $webhook->setId('00000000-0000-0000-0000-000000000000');

        $this->setMockResponse($this->client, array(
            'webhooks/deleteWebhook',
        ));
        $result = $this->client->deleteWebhook($webhook);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testListWebhookCallsReturnsCorrectData()
    {
        $webhook = new Webhook();
        $webhook->setId('00000000-0000-0000-0000-000000000000');

        $this->setMockResponse($this->client, array(
            'webhooks/listWebhookCalls',
        ));
        $webhookCalls = $this->client->getWebhookCalls($webhook);

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $webhookCalls);
        $this->assertEquals($webhookCalls->count(), 2);
        foreach ($webhookCalls as $webhookCall) {
            $this->assertInstanceOf('\Mgrt\Model\WebhookCall', $webhookCall);
        }
    }

    public function testGetWebhookCallReturnsCorrectData()
    {
        $webhook = new Webhook();
        $webhook->setId('00000000-0000-0000-0000-000000000000');

        $this->setMockResponse($this->client, array(
            'webhooks/getWebhookCall',
        ));
        $webhookCall = $this->client->getWebhookCall($webhook, '00000000-0000-0000-0000-000000000000');

        $this->assertInstanceOf('\Mgrt\Model\WebhookCall', $webhookCall);
    }
}
