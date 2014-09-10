<?php

namespace Mgrt\Tests\Command;

use Mgrt\Model\Sender;

class SendersTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testListSendersReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'senders/listSenders',
        ));
        $senders = $this->client->getSenders();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $senders);
        $this->assertEquals($senders->count(), 2);
        foreach ($senders as $sender) {
            $this->assertInstanceOf('\Mgrt\Model\Sender', $sender);
        }
    }

    public function testGetSenderReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'senders/getSender',
        ));
        $sender = $this->client->getSender(1);

        $this->assertInstanceOf('\Mgrt\Model\Sender', $sender);
    }

    public function testDeleteSenderReturnsCorrectData()
    {
        $sender = new Sender();
        $sender->setId(1);

        $this->setMockResponse($this->client, array(
            'senders/deleteSender',
        ));
        $result = $this->client->deleteSender($sender);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }
}
