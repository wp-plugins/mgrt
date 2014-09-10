<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;

class Webhook extends BaseModel
{
    protected $id;
    protected $name;
    protected $callback_url;
    protected $listened_events;
    protected $listened_sources;
    protected $secret_key;
    protected $enabled;
    protected $created_at;
    protected $updated_at;
}
