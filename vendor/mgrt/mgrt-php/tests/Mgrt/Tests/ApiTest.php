<?php

namespace Mgrt\Tests;

use Mgrt\Client;

/**
 * @covers Mgrt\Client
 */
class ApiTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @covers Mgrt\Client::factory
     */
    public function testFactory()
    {
        $client = Client::factory(array(
            'public_key' => '',
            'private_key' => '',
        ));
        $this->assertEquals('https://api.mgrt.net', $client->getBaseUrl());
    }

    /**
     * @expectedException Guzzle\Common\Exception\InvalidArgumentException
     * @dataProvider failingFactoryAuthConfig
     */
    public function testFactoryAuth($config)
    {
        $client = Client::factory($config);
    }

    /**
     * Data provider for testing if the client auth configuration is valid
     *
     * @return array
     */
    public function failingFactoryAuthConfig()
    {
        return array(
            array(array()),
            array(array('public_key')),
            array(array('private_key')),
        );
    }
}
