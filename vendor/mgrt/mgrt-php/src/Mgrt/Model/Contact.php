<?php

namespace Mgrt\Model;

use Mgrt\Model\BaseModel;
use Mgrt\Model\MailingList;

class Contact extends BaseModel
{
    protected $id;
    protected $status;
    protected $email;
    protected $mailing_lists = array();
    protected $custom_fields = array();
    protected $latitude;
    protected $longitude;
    protected $country_code;
    protected $time_zone;
    protected $created_at;
    protected $updated_at;

    public function addMailingList(MailingList $mailingList)
    {
        return $this->addMailingLists(array($mailingList));
    }

    public function addMailingLists(array $datas)
    {
        foreach ($datas as $key => $value) {
            if ($value instanceof MailingList) {
                $this->mailing_lists[$value->getId()] = $value;
            } else {
                $mailingList = new MailingList();
                $mailingList->fromArray($value);
                $this->mailing_lists[$mailingList->getId()] = $mailingList;
            }
        }

        return $this;
    }

    public function setMailingLists(array $datas)
    {
        $this->mailing_lists = array();

        return $this->addMailingLists($datas);
    }

    public function removeMailingList(MailingList $mailingList)
    {
        return $this->removeMailingLists(array($mailingList));
    }

    public function removeMailingLists(array $datas)
    {
        $filter = array();
        foreach ($datas as $key => $value) {
            if ($value instanceof MailingList) {
                $filter = $value->getId();
            } else {
                $filter = is_array($value) ? $value['id'] : $value;
            }

            if (isset($this->mailing_lists[$filter])) {
                unset($this->mailing_lists[$filter]);
            }
        }

        return $this;
    }

    public function addCustomField(CustomField $customField)
    {
        return $this->addCustomFields(array($customField));
    }


    public function addCustomFields(array $datas)
    {
        foreach ($datas as $key => $value) {
            if ($value instanceof CustomField) {
                $this->custom_fields[$value->getId()] = $value;
            } else {
                $customField = new CustomField();
                $customField->fromArray($value);
                $this->custom_fields[$customField->getId()] = $customField;
            }
        }

        return $this;
    }

    public function setCustomFields(array $datas)
    {
        $this->custom_fields = array();
        
        return $this->addCustomFields($datas);
    }

    public function removeCustomField(CustomField $customField)
    {
        return $this->removeCustomFields(array($customField));
    }

    public function removeCustomFields(array $datas)
    {
        $filter = array();
        foreach ($datas as $key => $value) {
            if ($value instanceof CustomField) {
                $filter = $value->getId();
            } else {
                $filter = is_array($value) ? $value['id'] : $value;
            }

            if (isset($this->custom_fields[$filter])) {
                unset($this->custom_fields[$filter]);
            }
        }

        return $this;
    }

    public function getCustomFieldsToArray()
    {
        $custom_fields = $this->getCustomFields();
        array_walk($custom_fields, function(&$customField) {
            $customField = array(
                'id' => $customField->getId(),
                'value' => $customField->getValue(),
            );
        });

        return $custom_fields;
    }

    public function getMailingListsToArray($indexed = true)
    {
        $mailing_lists = array();
        foreach ($this->getMailingLists() as $value) {
            if ($indexed) {
                $mailing_lists[] = $value->getId();
            } else {
                $mailing_lists[$value->getId()] = $value->getId();
            }
        }

        return $mailing_lists;
    }
}
