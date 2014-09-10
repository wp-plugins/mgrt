<?php

namespace Mgrt\Tests\Command;

use Mgrt\Model\MailingList;

class MailingListsTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testListMailingListsReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'mailing-lists/listMailingLists',
        ));
        $mailingLists = $this->client->getMailingLists();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $mailingLists);
        $this->assertEquals($mailingLists->count(), 2);
        foreach ($mailingLists as $mailingList) {
            $this->assertInstanceOf('\Mgrt\Model\MailingList', $mailingList);
        }
    }

    public function testGetMailingListReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'mailing-lists/getMailingList',
        ));
        $mailingList = $this->client->getMailingList(1);

        $this->assertInstanceOf('\Mgrt\Model\MailingList', $mailingList);
    }

    public function testCreateMailingListReturnsCorrectData()
    {
        $mailingList = new MailingList();
        $mailingList->setName('test');

        $this->setMockResponse($this->client, array(
            'mailing-lists/createMailingList',
        ));
        $result = $this->client->createMailingList($mailingList);

        $this->assertInstanceOf('\Mgrt\Model\MailingList', $result);
    }

    public function testUpdateMailingListReturnsCorrectData()
    {
        $mailingList = new MailingList();
        $mailingList->setId(1);
        $mailingList->setName('test');

        $this->setMockResponse($this->client, array(
            'mailing-lists/updateMailingList',
        ));
        $result = $this->client->updateMailingList($mailingList);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testDeleteMailingListReturnsCorrectData()
    {
        $mailingList = new MailingList();
        $mailingList->setId(1);

        $this->setMockResponse($this->client, array(
            'mailing-lists/deleteMailingList',
        ));
        $result = $this->client->deleteMailingList($mailingList);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testGetMailingListContactsReturnsCorrectData()
    {
        $mailingList = new MailingList();
        $mailingList->setId(1);

        $this->setMockResponse($this->client, array(
            'mailing-lists/listMailingListContacts',
        ));
        $contacts = $this->client->getMailingListContacts($mailingList);

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $contacts);
        $this->assertEquals($contacts->count(), 2);
        foreach ($contacts as $contact) {
            $this->assertInstanceOf('\Mgrt\Model\Contact', $contact);
        }
    }
}
