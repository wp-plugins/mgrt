<?php

namespace Mgrt\Model;

use Doctrine\Common\Inflector\Inflector;
use Mgrt\Model\BaseModel;
use Mgrt\Model\Result;

class WebhookCallInput extends BaseModel
{
    protected $id;
    protected $signature;
    protected $event;
    protected $event_name;
    protected $event_type;
    protected $source;
    protected $payload;
    protected $valid;
    protected $secret_key;
    protected $is_test = false;

    public function fromInputs($input = null)
    {
        if ($this->secret_key === false) {
            return false;
        }
        if (empty($_SERVER['HTTP_X_MGRT_SIGNATURE']) || empty($_SERVER['HTTP_X_MGRT_DELIVERY'])) {
            return false;
        }
        if ($_SERVER['HTTP_X_MGRT_SIGNATURE'] == '00000000000000000000000000000' && $_SERVER['HTTP_X_MGRT_DELIVERY'] == '00000000-0000-0000-0000-000000000000') {
            // this request is an access check; exit now
            exit();
        }
        $body       = is_null($input) ? file_get_contents('php://input') : $input;
        $signature  = $_SERVER['HTTP_X_MGRT_SIGNATURE'];
        $id         = $_SERVER['HTTP_X_MGRT_DELIVERY'];

        $valid      = $this->validate($body, $signature);
        if (!$valid) {
            return $this->fromArray(compact('id', 'signature', 'valid'));
        }

        $parsedBody = json_decode($body, true);
        if ($parsedBody['payload'] == 'Hello World!') {
            // this request is a test call; exit now
            $is_test = true;

            return $this->fromArray(compact('id', 'signature', 'valid', 'is_test'));
        }

        if (!is_array($parsedBody['payload'])) {
            throw new Exception("Error Processing Request", 1);
        }

        $event      = $parsedBody['event'];
        $source     = $parsedBody['source'];

        $exp        = explode('.', $event, 2);

        $event_type = Inflector::camelize($exp[0]);
        $event_name = Inflector::camelize('on_'.$exp[1]);

        $objectName = Inflector::classify($exp[0]);

        $result     = new Result();
        $payload    = $result->fromArrayWithObject($parsedBody['payload'], $objectName);

        return $this->fromArray(compact('id', 'signature', 'event', 'event_type', 'event_name', 'source', 'payload', 'valid'));
    }

    private function validate($payload, $signature)
    {
        if (is_null($this->secret_key)) {
            return false;
        }

        return base64_encode(hash_hmac('sha1', $payload, $this->secret_key, true)) == $signature;
    }
}
