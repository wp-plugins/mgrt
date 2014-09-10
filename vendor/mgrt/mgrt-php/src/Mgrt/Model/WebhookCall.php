<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;

class WebhookCall extends BaseModel
{
    protected $id;
    protected $webhook_id;
    protected $event;
    protected $source;
    protected $payload;
    protected $created_at;
    protected $last_sent_at;
    protected $last_error_at;
    protected $attempts;
    protected $request_headers;
    protected $request_body;
    protected $response_headers;
    protected $response_body;
    protected $response_status_code;
}
