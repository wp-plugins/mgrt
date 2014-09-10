<?php

namespace Mgrt\Tests\Command;

use Mgrt\Model\Campaign;

class CampaignsTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testListCampaignsReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'campaigns/listCampaigns',
        ));
        $campaigns = $this->client->getCampaigns();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $campaigns);
        $this->assertEquals($campaigns->count(), 2);
        foreach ($campaigns as $campaign) {
            $this->assertInstanceOf('\Mgrt\Model\Campaign', $campaign);
        }
    }

    public function testGetCampaignReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'campaigns/getCampaign',
        ));
        $campaign = $this->client->getCampaign(1);
        $this->assertInstanceOf('\Mgrt\Model\Campaign', $campaign);

        $mailingLists = $campaign->getMailingLists();
        foreach ($mailingLists as $mailingList) {
            $this->assertInstanceOf('\Mgrt\Model\MailingList', $mailingList);
        }
    }

    public function testCreateCampaignReturnsCorrectData()
    {
        $campaign = $this->getFakeCampaign();

        $this->setMockResponse($this->client, array(
            'campaigns/createCampaign',
        ));
        $result = $this->client->createCampaign($campaign);

        $this->assertInstanceOf('\Mgrt\Model\Campaign', $result);
        $this->assertInternalType('integer', $result->getId());
    }

    public function testUpdateCampaignReturnsCorrectData()
    {
        $campaign = $this->getFakeCampaign();
        $campaign->setId(1);

        $this->setMockResponse($this->client, array(
            'campaigns/updateCampaign',
        ));
        $result = $this->client->updateCampaign($campaign);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testDeleteCampaignReturnsCorrectData()
    {
        $campaign = $this->getFakeCampaign();
        $campaign->setId(1);

        $this->setMockResponse($this->client, array(
            'campaigns/deleteCampaign',
        ));
        $result = $this->client->deleteCampaign($campaign);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testUnscheduleCampaignReturnsCorrectData()
    {
        $campaign = $this->getFakeCampaign();
        $campaign->setId(1);

        $this->setMockResponse($this->client, array(
            'campaigns/unscheduleCampaign',
        ));
        $result = $this->client->unscheduleCampaign($campaign);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function getFakeCampaign()
    {
        $campaign = new Campaign();
        $campaign
            ->setName('test')
            ->setFromMail('john@doe.com')
            ->setFromName('John Doe')
            ->setReplyMail('john@smith.com')
            ->setBody('<html></html>')
            ->setSubject("John's first newsletter")
        ;

        return $campaign;
    }
}
