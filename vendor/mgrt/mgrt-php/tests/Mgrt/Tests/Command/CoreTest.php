<?php

namespace Mgrt\Tests\Command;

class CoreTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testGetHelloWorldReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'core/getHelloWorld'
        ));
        $hello = $this->client->getHelloWorld();

        $this->assertEquals('Hello World!', $hello);
    }

    public function testGetSystemDateReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'core/getSystemDate'
        ));
        $datetime = $this->client->getSystemDate();

        // Raise exception if date format is incorrect
        $datetime = new \DateTime($datetime);
    }
}
