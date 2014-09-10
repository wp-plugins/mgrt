<?php

namespace Mgrt\Tests\Command;

use Mgrt\Model\Domain;

class DomainsTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testListDomainsReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'domains/listDomains',
        ));
        $domains = $this->client->getDomains();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $domains);
        $this->assertEquals($domains->count(), 3);
        foreach ($domains as $domain) {
            $this->assertInstanceOf('\Mgrt\Model\Domain', $domain);
        }
    }

    public function testGetDomainReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'domains/getDomain',
        ));
        $domain = $this->client->getDomain(1);

        $this->assertInstanceOf('\Mgrt\Model\Domain', $domain);
    }

    public function testCheckDomainReturnsCorrectData()
    {
        $domain = new Domain();
        $domain->setId(1);

        $this->setMockResponse($this->client, array(
            'domains/checkDomain',
        ));
        $domain = $this->client->checkDomain($domain);

        $this->assertInstanceOf('\Mgrt\Model\Domain', $domain);
    }
}
