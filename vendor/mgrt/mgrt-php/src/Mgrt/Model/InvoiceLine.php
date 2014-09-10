<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;
use Mgrt\Model\ResultCollection;

class InvoiceLine extends BaseModel
{
    protected $id;
    protected $title;
    protected $description;
    protected $quantity;
    protected $price;
}
