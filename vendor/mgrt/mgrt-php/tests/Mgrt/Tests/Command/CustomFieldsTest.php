<?php

namespace Mgrt\Tests\Command;


class CustomFieldsTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testGetCustomFieldsReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'custom-fields/getCustomFields',
        ));
        $customFields = $this->client->getCustomFields();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $customFields);
        $this->assertEquals($customFields->count(), 7);
        foreach ($customFields as $customField) {
            $this->assertInstanceOf('\Mgrt\Model\CustomField', $customField);
        }
    }
}
