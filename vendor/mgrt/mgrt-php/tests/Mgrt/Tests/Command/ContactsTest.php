<?php

namespace Mgrt\Tests\Command;

use Mgrt\Model\Contact;

class ContactsTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testListContactsReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'contacts/listContacts',
        ));
        $contacts = $this->client->getContacts();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $contacts);
        $this->assertEquals($contacts->count(), 2);
        foreach ($contacts as $contact) {
            $this->assertInstanceOf('\Mgrt\Model\Contact', $contact);
        }
    }

    public function testGetContactReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'contacts/getContact',
        ));
        $contact = $this->client->getContact(1);

        $this->assertInstanceOf('\Mgrt\Model\Contact', $contact);

        $mailingLists = $contact->getMailingLists();
        foreach ($mailingLists as $mailingList) {
            $this->assertInstanceOf('\Mgrt\Model\MailingList', $mailingList);
        }

        $customFields = $contact->getCustomFields();
        foreach ($customFields as $customField) {
            $this->assertInstanceOf('\Mgrt\Model\CustomField', $customField);
        }
    }

    public function testCreateContactReturnsCorrectData()
    {
        $contact = new Contact();
        $contact->setEmail('john@doe.com');

        $this->assertNull($contact->getId());

        $this->setMockResponse($this->client, array(
            'contacts/createContact',
        ));
        $result = $this->client->createContact($contact);

        $this->assertInstanceOf('\Mgrt\Model\Contact', $result);
        $this->assertInternalType('integer', $contact->getId());
    }

    public function testUpdateContactReturnsCorrectData()
    {
        $contact = new Contact();
        $contact->setId(1);
        $contact->setEmail('john@doe.com');

        $this->setMockResponse($this->client, array(
            'contacts/updateContact',
        ));
        $result = $this->client->updateContact($contact);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testUnsubscribeContactReturnsCorrectData()
    {
        $contact = new Contact();
        $contact->setId(1);

        $this->setMockResponse($this->client, array(
            'contacts/unsubscribeContact',
        ));
        $result = $this->client->unsubscribeContact($contact);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testResubscribeContactReturnsCorrectData()
    {
        $contact = new Contact();
        $contact->setId(1);

        $this->setMockResponse($this->client, array(
            'contacts/resubscribeContact',
        ));
        $result = $this->client->resubscribeContact($contact);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testDeleteContactReturnsCorrectData()
    {
        $contact = new Contact();
        $contact->setId(1);

        $this->setMockResponse($this->client, array(
            'contacts/deleteContact',
        ));
        $result = $this->client->deleteContact($contact);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }
}
