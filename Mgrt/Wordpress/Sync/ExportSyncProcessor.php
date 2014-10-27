<?php

namespace Mgrt\Wordpress\Sync;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Mgrt\Model\Contact;
use Mgrt\Model\CustomField;
use Mgrt\Model\MailingList;
use Mgrt\Wordpress\AbstractExecutor;
use Mgrt\Wordpress\View\Profile;

class ExportSyncProcessor extends AbstractExecutor
{
    public static $ignoreNextCall = false;

    /**
     * Fired when an user is registered
     *
     * @param WP_User $user the user object
     */
    public function onUserRegister($user_id, $fields = array())
    {
        if (self::$ignoreNextCall) {
            self::$ignoreNextCall = false;
            // callback from ImportSync, ignore
            return false;
        }

        if (false === ($wp_user = get_userdata($user_id))) {
            return false;
        }

        $email = $wp_user->get('user_email');

        if (empty($fields)) {
            $fields = $_POST;
        }

        try {
            $this->getDataManager()->makeApiCall('getContact', $email);

            return $this->onUserEdit($user_id, $wp_user, $fields); // Contact already exists; ignoring
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() != 404) {
                return false; // api error; ignoring
            }
        }

        $lists = $this->getDataManager()->getOption('lists');
        if ($lists === false) {
            $lists = array();
        }

        $mailingLists = array();
        foreach ($lists as $listId) {
            $ml = new MailingList();
            $ml->setId($listId);
            $mailingLists[] = $ml;
        }

        $customFields = array();
        if (!empty($fields)) {
            $customFields = self::makeCustomFieldArray($fields);
        }

        return $this->submitEmail($email, $mailingLists, $customFields);
    }

    /**
     * Fired when an user is deleted
     *
     * @param WP_User $user the user object
     */
    public function onUserDelete($user_id, $email = null)
    {
        if (self::$ignoreNextCall) {
            self::$ignoreNextCall = false;
            // callback from ImportSync, ignore
            return false;
        }

        if (false === ($wp_user = get_userdata($user_id))) {
            return false;
        }

        try {
            $contact = $this->getDataManager()->makeApiCall('getContact', $wp_user->get('user_email'));

            $lists = $this->getDataManager()->getOption('lists');
            if ($lists === false || $lists == null) {
                $lists = array();
            }

            $clists = array();
            foreach ($contact->getMailingListsToArray() as $mailingListId) {
                if (!in_array($mailingListId, $lists)) {
                    $clists[] = $mailingListId;
                }
            }

            if (empty($clists)) {
                return $this->getDataManager()->makeApiCall('unsubscribeContact', $contact);
            }

            $mailingLists = array();
            foreach ($clists as $listId) {
                $ml = new MailingList();
                $ml->setId($listId);
                $mailingLists[] = $ml;
            }

            $contact
                ->setMailingLists($mailingLists);

            return $this->getDataManager()->makeApiCall('updateContact', $contact);
        } catch (ClientErrorResponseException $e) {
            return false; // Contact not found or api error; ignoring
        }
    }

    /**
     * Fired when an user edit his profile
     *
     * @param WP_User $user the user object
     * @param string $field name of the field
     * @param mixed $new_state new state of option
     */
    public function onUserEdit($user_id, $old_user, $post = array(), $overwrite = true)
    {
        if (self::$ignoreNextCall) {
            self::$ignoreNextCall = false;
            // callback from ImportSync, ignore
            return false;
        }

        if (false === ($wp_user = get_userdata($user_id))) {
            return false;
        }

        if (empty($post)) {
            $post = $_POST;
        }

        if (empty($_POST) && empty($post['email'])) {
            return false; // cannot process
        }

        $old_email = is_object($old_user) ? $old_user->user_email : $old_user;

        if (empty($post['email'])) {
            return false;
        }

        try {
            $contact = $this->getDataManager()->makeApiCall('getContact', $old_email);
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                return self::onUserRegister($user_id, $post);
            }

            return false;
        }

        $lists = $this->getDataManager()->getOption('lists');
        if ($lists === false) {
            $lists = array();
        }

        $mailingLists = array();
        foreach ($lists as $listId) {
            $ml = new MailingList();
            $ml->setId($listId);
            $mailingLists[] = $ml;
        }

        $contact
            ->setEmail($post['email'])
            ->addCustomFields(self::makeCustomFieldArray($post))
            ->addMailingLists($mailingLists);

        try {
            $r = $this->getDataManager()->makeApiCall('updateContact', $contact);

            // resuscribe contact
            if ($contact->getStatus() == 'unsubscribed') {
                $this->getDataManager()->makeApiCall('resubscribeContact', $contact);
            }

            return $r;
        } catch (ClientErrorResponseException $e) {
            return false;
        }
    }

    /**
     * Submit an email to Mgrt
     * @param email $email
     * @param array $mailingLists
     * @param array $customFields
     * @param boolean $failSafe try first to create contact, then update his settings
     * @return boolean
     */
    public function submitEmail($email, $mailingLists = array(), $customFields = array(), $failSafe = false)
    {
        $contact = new Contact();
        $contact
            ->setEmail($email)
            ->addMailingLists($mailingLists);

        if (!$failSafe) {
            $contact->addCustomFields($customFields);
        }

        try {
            $contact = $this->getDataManager()->makeApiCall('createContact', $contact);
            if ($failSafe) {
                $contact->addCustomFields($customFields);
                $this->getDataManager()->makeApiCall('updateContact', $contact);
            }

            return true;
        } catch (ClientErrorResponseException $e) {
            return false;
        }
    }

    private function makeCustomFieldArray($fields, $overwrite = true)
    {
        if (!($wp_fields_relation = $this->getDataManager()->getOption('custom_fields'))) {
            $wp_fields_relation = array();
        }

        $customFields = array();
        foreach ($wp_fields_relation as $wp_field_name => $mgrt_field_id) {
            if ($mgrt_field_id == -1) {
                continue; // don't sync this field
            }

            if (empty($fields[$wp_field_name])) {
                if ($overwrite) {
                    $fields[$wp_field_name] = '';
                } else {
                    continue;
                }
            }
            $cf = new CustomField();
            $cf
                ->setId(intval($mgrt_field_id))
                ->setValue($fields[$wp_field_name]);
            $customFields[] = $cf;

        }

        $viewKey = $this->getViewManager()->getViewKey('Profile');

        if (!empty($fields[$viewKey])) {
            if (!($additional_fields = $this->getDataManager()->getOption('custom_fields_additional'))) {
                $additional_fields = array();
            }

            foreach ($additional_fields as $fieldId) {

                if (empty($fields[$viewKey][Profile::FIELD_KEY.$fieldId])) {
                    if ($overwrite) {
                        $fields[$viewKey][Profile::FIELD_KEY.$fieldId] = '';
                    } else {
                        continue;
                    }
                }

                $cf = new CustomField();
                $cf
                    ->setId(intval($fieldId))
                    ->setValue($fields[$viewKey][Profile::FIELD_KEY.$fieldId]);
                $customFields[] = $cf;
            }
        }

        return $customFields;
    }
}
