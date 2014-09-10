<?php

namespace Mgrt\Tests\Command;

class InvoicesTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @var Mgrt\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('mgrt', true);
    }

    public function testListInvoicesReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'invoices/listInvoices',
        ));
        $invoices = $this->client->getInvoices();

        $this->assertInstanceOf('\Mgrt\Model\ResultCollection', $invoices);
        $this->assertEquals($invoices->count(), 1);
        foreach ($invoices as $invoice) {
            $this->assertInstanceOf('\Mgrt\Model\Invoice', $invoice);
        }
    }

    public function testGetInvoiceReturnsCorrectData()
    {
        $this->setMockResponse($this->client, array(
            'invoices/getInvoice',
        ));
        $invoice = $this->client->getInvoice(1);
        $this->assertInstanceOf('\Mgrt\Model\Invoice', $invoice);

        $invoiceLines = $invoice->getInvoiceLines();
        foreach ($invoiceLines as $invoiceLine) {
            $this->assertInstanceOf('\Mgrt\Model\InvoiceLine', $invoiceLine);
        }
    }
}
