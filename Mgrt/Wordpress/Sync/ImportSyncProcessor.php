<?php

namespace Mgrt\Wordpress\Sync;

use Mgrt\Model\Contact;
use Mgrt\Wordpress\AbstractExecutor;
use Mgrt\Wordpress\Manager\SyncManager;
use Mgrt\Wordpress\Model\WebhookCallInput;
use Mgrt\Wordpress\View\Profile;

class ImportSyncProcessor extends AbstractExecutor
{
    public static $ignoreNextCall = false;

    /**
     * Fired when a webhook is confirmed
     *
     * @param array $params
     */
    public function onWebhook(WebhookCallInput $webhookCall)
    {
        $callback = array(
            $this,
            $webhookCall->getEventName()
        );
        $lists = $this->getDataManager()->getOption('lists');
        $in_list = false;
        foreach ($webhookCall->getPayload()->getMailingLists() as $mailingList) {
            if (in_array($mailingList->getId(), $lists)) {
                $in_list = true;
                break;
            }
        }

        if (!$in_list) {
            echo 'BAD_MAILING_LIST : ';
            $wp_id = $this->getDataManager()->getUserIdByMgrtId($webhookCall->getPayload()->getId());
            if ($wp_id !== 0) {
                echo 'REMOVE_FROM_WP : ';
                ExportSyncProcessor::$ignoreNextCall = true;
                return $this->deleteByWpId($wp_id);
            }
            return false;
        }

        SyncManager::$is_syncing = true;
        ExportSyncProcessor::$ignoreNextCall = true;

        $r = method_exists($callback[0], $callback[1]) && is_callable($callback) && call_user_func($callback, $webhookCall->getPayload());

        ExportSyncProcessor::$ignoreNextCall = false;
        SyncManager::$is_syncing = false;

        return $r;
    }

    /**
     * Fired when an user is registered
     */
    public function onCreate(Contact $contact, $overwrite = true)
    {
        $wp_id = get_user_by('email', $contact->getEmail());
        if ($wp_id === false) {
            $wp_id = wp_create_user($contact->getEmail(), $contact->getEmail());
            if (!is_int($wp_id)) {
                return false;
            }
        } else {
            $wp_id = $wp_id->ID;
        }

        if ($wp_id == 0) {
            return false;
        }

        // re ignore possible call after edit
        ExportSyncProcessor::$ignoreNextCall = true;

        return $this->onEdit($contact, $wp_id);
    }

    public function onEdit(Contact $contact, $wp_id = 0)
    {
        if ($wp_id == 0) {
            $wp_id = $this->getDataManager()->getUserIdByMgrtId($contact->getId());
            if ($wp_id === 0) {
                return $this->onCreate($contact);
            }
        }

        $user_meta = $this->makeCustomFieldArray($contact);

        $user_meta['basic']['user_email'] = $contact->getEmail();
        $user_meta['basic']['ID'] = $wp_id;

        if (!empty($user_meta['custom'])) {
            foreach ($user_meta['custom'] as $meta_key => $meta_value) {
                $this->getDataManager()->insertOrUpdateCustomField($wp_id, $meta_key, $meta_value);
            }
        }

        return (bool)wp_update_user($user_meta['basic']);
    }


    public function onDelete(Contact $contact)
    {
        $wp_id = $this->getDataManager()->getUserIdByMgrtId($contact->getId());
        if ($wp_id === 0) {
            return false;
        }

        return $this->deleteByWpId($wp_id);
    }

    public function onUnsubscribe(Contact $contact)
    {
        return $this->onDelete($contact);
    }

    public function deleteByWpId($wp_id)
    {
        if ($wp_id === 1) {
            // do not delete master user
            return false;
        }

        include ABSPATH.'/wp-admin/includes/user.php';

        wp_delete_user($wp_id, null);
        return true;
    }

    private function makeCustomFieldArray(Contact $contact, $overwrite = true)
    {
        $wp_fields_relation = $this->getDataManager()->getOption('custom_fields');
        if ($wp_fields_relation === false) {
            return array();
        }

        $customFields = $contact->getCustomFieldsToArray();
        if (empty($customFields)) {
            $customFields = $this->getDataManager()->makeApiCall('getContact', $contact->getId())->getCustomFieldsToArray();
        }

        if (empty($customFields)) {
            return array();
        }

        $customFieldsById = array();
        foreach ($customFields as $customField) {
            $customFieldsById[$customField['id']] = $customField['value'];
        }

        $basic = array();
        foreach ($wp_fields_relation as $wp_field_name => $fieldId) {
            if ($fieldId == -1) {
                continue; // don't sync this field
            }

            if (!isset($customFieldsById[$fieldId])) {
                continue; // field not found
            }

            $cf_value = $customFieldsById[$fieldId];
            if (empty($cf_value)) {
                if ($overwrite) {
                    $cp_value = null;
                } else {
                    continue;
                }

            }

            $basic[$wp_field_name] = $cf_value;
        }

        if (!($additional_fields = $this->getDataManager()->getOption('custom_fields_additional'))) {
            $additional_fields = array();
        }
        $custom = array();
        foreach ($additional_fields as $fieldId) {

            if (!isset($customFieldsById[$fieldId])) {
                continue; // field not found
            }

            $cf_value = $customFieldsById[$fieldId];
            if (empty($cf_value)) {
                if ($overwrite) {
                    $cp_value = null;
                } else {
                    continue;
                }
            }

            $custom[Profile::FIELD_KEY.$fieldId] = $cf_value;
        }

        $custom['__mgrt_id'] = $contact->getId();

        return compact('basic', 'custom');
    }
}
