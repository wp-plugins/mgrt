<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;

class ApiKey extends BaseModel
{
    protected $id;
    protected $name;
    protected $public_key;
    protected $secret_key;
    protected $enabled;
    protected $created_at;
    protected $disabled_at;
}
