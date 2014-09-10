<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;

class Domain extends BaseModel
{
    protected $id;
    protected $domain_name;
    protected $checked_at;
    protected $spf_fqdn;
    protected $spf_status;
    protected $dkim_fqdn;
    protected $dkim_status;
    protected $public_key;
}
