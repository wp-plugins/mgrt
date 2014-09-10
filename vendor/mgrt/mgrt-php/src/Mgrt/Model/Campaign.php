<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;

class Campaign extends BaseModel
{
    protected $id;
    protected $mailing_lists = array();
    protected $name;
    protected $subject;
    protected $body;
    protected $from_mail;
    protected $from_name;
    protected $reply_mail;
    protected $created_at;
    protected $updated_at;
    protected $scheduled_at;
    protected $sent_at;
    protected $tracking_ends_at;
    protected $status;
    protected $is_public;
    protected $share_url;

    public function setMailingLists(array $datas)
    {
        foreach ($datas as $key => $value) {
            $mailingList = new MailingList();
            $this->mailing_lists[] = $mailingList->fromArray($value);
        }

        return $this;
    }
}
