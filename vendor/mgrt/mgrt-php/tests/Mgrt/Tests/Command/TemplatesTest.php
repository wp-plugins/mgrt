<?php

namespace Mgrt\Tests\Command;

use Mgrt\Model\Template;

class TemplatesTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testListTemplatesReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'templates/listTemplates',
        ));
        $templates = $this->client->getTemplates();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $templates);
        $this->assertEquals($templates->count(), 2);
        foreach ($templates as $template) {
            $this->assertInstanceOf('\Mgrt\Model\Template', $template);
        }
    }

    public function testGetTemplateReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'templates/getTemplate',
        ));
        $template = $this->client->getTemplate(1);

        $this->assertInstanceOf('\Mgrt\Model\Template', $template);
    }

    public function testCreateTemplateReturnsCorrectData()
    {
        $template = new Template();
        $template->setName('test');

        $this->setMockResponse($this->client, array(
            'templates/createTemplate',
        ));
        $result = $this->client->createTemplate($template);

        $this->assertInstanceOf('\Mgrt\Model\Template', $template);
    }

    public function testUpdateTemplateReturnsCorrectData()
    {
        $template = new Template();
        $template->setId(1);
        $template->setName('test');

        $this->setMockResponse($this->client, array(
            'templates/updateTemplate',
        ));
        $result = $this->client->updateTemplate($template);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }

    public function testDeleteTemplateReturnsCorrectData()
    {
        $template = new Template();
        $template->setId(1);

        $this->setMockResponse($this->client, array(
            'templates/deleteTemplate',
        ));
        $result = $this->client->deleteTemplate($template);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(true, $result);
    }
}
