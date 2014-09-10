<?php

namespace Mgrt\Tests\Command;

class AccountsTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testGetAccountReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'accounts/getAccount',
        ));
        $account = $this->client->getAccount();

        $this->assertInstanceOf('\Mgrt\Model\Account', $account);
    }
}
