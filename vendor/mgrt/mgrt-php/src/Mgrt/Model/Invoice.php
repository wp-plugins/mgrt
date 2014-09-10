<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;

class Invoice extends BaseModel
{
    protected $id;
    protected $number;
    protected $currency;
    protected $net_amount;
    protected $tax_amount;
    protected $total_amount;
    protected $due_at;
    protected $paid_at;
    protected $invoice_lines = array();

    public function setInvoiceLines(array $datas)
    {
        foreach ($datas as $key => $value) {
            $invoiceLine = new InvoiceLine();
            $this->invoice_lines[] = $invoiceLine->fromArray($value);
        }

        return $this;
    }
}
