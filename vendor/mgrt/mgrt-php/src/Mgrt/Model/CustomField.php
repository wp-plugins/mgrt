<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;

class CustomField extends BaseModel
{
    protected $id;
    protected $name;
    protected $field_type;
    protected $value;
    protected $choices;
}
