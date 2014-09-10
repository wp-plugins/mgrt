<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;

class Account extends BaseModel
{
    protected $id;
    protected $company;
    protected $address_street;
    protected $address_city;
    protected $address_zipcode;
    protected $address_country;
    protected $currency;
    protected $timezone;
    protected $credits;
    protected $plan_type;
    protected $header_background_color;
    protected $header_text_color;
    protected $platform_name;
    protected $logo_url;
    protected $login_url;
}
