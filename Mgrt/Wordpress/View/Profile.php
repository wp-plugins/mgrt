<?php

namespace Mgrt\Wordpress\View;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Mgrt\Model\CustomField;
use Mgrt\Wordpress\AbstractView;
use Mgrt\Wordpress\Bootstrap;
use Mgrt\Wordpress\Manager\SyncManager;
use Mgrt\Wordpress\Manager\ViewManager;

class Profile extends AbstractView
{
    const FIELD_KEY = 'custom_profile_field_';

    private $formFieldRelation = array();
    private $profileFields = array();
    /**
     * url validator: https://github.com/symfony/Validator/blob/master/Constraints/UrlValidator.php
     */
    const URL_PATTERN = '~^
        https?://                                 # protocol
        (
            ([\pL\pN\pS-\.])+(\.?([\pL]|xn\-\-[\pL\pN-]+)+\.?) # a domain name
                |                                              # or
            \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                 # a IP address
                |                                              # or
            \[
                (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):) {6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.) {3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):) {5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.) {3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):) {4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.) {3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):) {0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):) {3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.) {3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):) {0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):) {2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.) {3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):) {0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.) {3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):) {0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.) {3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):) {0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):) {0,6}(?:(?:[0-9a-f]{1,4})))?::))))
            \]  # a IPv6 address
        )
        (:[0-9]+)?                              # a port (optional)
        (/?|/\S+)                               # a /, nothing or a / with something
    $~ixu';

    /**
     * Date validator: https://github.com/symfony/Validator/blob/master/Constraints/DateValidator.php
     */
    const DATE_PATTERN = '/^(\d{4})-(\d{2})-(\d{2})$/';

    public static function getFieldKey()
    {
        return self::FIELD_KEY;
    }

    public function getViewKey()
    {
        return MGRT__OPTION_KEY.'-profile';
    }

    public function getViewName()
    {
        return 'Profile';
    }

    /**
     * Initialize view
     */
    public function __construct(Bootstrap $bootstrap)
    {
        parent::__construct($bootstrap);
        if (! $this->getDataManager()->hasKeys()) {
            return;
        }

        add_action('show_user_profile', array($this, 'user_profile'));
        add_action('edit_user_profile', array($this, 'user_profile'));
        add_action('personal_options_update', array($this, 'process_user_option_update'));
        add_action('edit_user_profile_update', array($this, 'process_user_option_update'));

        add_settings_section(
            'mgrt_sync_section',
            __('form.profile.section', 'mgrt-wordpress'),
            function() {},
            $this->getViewKey()
        );

        if (!($this->profileFields = $this->getDataManager()->getOption('custom_fields_additional'))) {
            $this->profileFields = array();
        }

        try {
            $custom_fields = $this->getDataManager()->makeCachedApiCall('getCustomFields');
            $this->formFieldRelation = array();
            foreach ($custom_fields as $field) {
                $this->formFieldRelation[$field->getId()] = $field;
            }

        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
            $this->ready = false;
            return;
        } catch (\ErrorException $e) {
            $this->ready = false;
            $this->getViewManager()->sheduleNotice('no_curl');
            return false;
        }
    }

    public static function buildProfileFields($args)
    {
        $output = '';
        $fieldKey = $args['fieldKey'];
        $field = $args['field'];
        $key = $args['key'];
        $value = isset($args['value']) ? $args['value'] : '';
        $choices = $field->getChoices();
        $input_type = 'text';
        switch ($field->getFieldType()) {
            case 'number':
            case 'date':
                $input_type = $field->getFieldType();
            case 'website':
            case 'text':
                $output = '<input name="'.$fieldKey.'['.$key.']" type="'.$input_type.'" class="regular-text" value="'.$value.'" />';
            break;
            case 'radio_buttons':
                $output = self::printRepeatedFields(
                    $value,
                    $choices,
                    '<div><label><input type="radio" name="'.$fieldKey.'['.$key.']" value="%1$s"%2$s />%1$s</label></div>',
                    ' checked="checked"',
                    '<fieldset class="checkbox-list">',
                    '</fieldset>'
                );
                break;
            case 'checkboxes':
                $output = self::printRepeatedFields(
                    $value,
                    $choices,
                    '<div><label><input type="checkbox" name="'.$fieldKey.'['.$key.'][]" value="%1$s"%2$s />%1$s</label></div>',
                    ' checked="checked"',
                    '<fieldset class="checkbox-list">',
                    '</fieldset>'
                );
                break;
            case 'drop_down':
                $output = self::printRepeatedFields(
                    $value,
                    $choices,
                    '<option value="%1$s"%2$s>%1$s</option>',
                    ' selected="selected"',
                    '<select name="'.$fieldKey.'['.$key.']" class="regular-select">',
                    '</select>'
                );
        }

        if (isset($args['display']) && $args['display']) {
            echo $output;
        } else {
            return $output;
        }
    }

    public static function printRepeatedFields($value, $choices, $template, $on_choice, $before = '', $after = '')
    {
        $output = $before;
        foreach ($choices as $choice) {
            $sec = '';
            if ($choice == $value || (is_array($value) && in_array($choice, $value))) {
                $sec = $on_choice;
            }
            $output .= sprintf($template, $choice, $sec);
        }

        $output .= $after;

        return $output;
    }

    public function user_profile($user)
    {
        if (! current_user_can('edit_users')) {
            return;
        }

        foreach ($this->profileFields as $fieldId) {
            if (!isset($this->formFieldRelation[$fieldId])) {
                return;
            }

            $field = $this->formFieldRelation[$fieldId];
            $key = self::FIELD_KEY.$field->getId();
            add_settings_field(
                $key,
                $field->getName(),
                array(__CLASS__, 'buildProfileFields'),
                $this->getViewKey(),
                'mgrt_sync_section',
                array(
                    'fieldKey' => $this->getViewKey(),
                    'key'   => $key,
                    'field' => $field,
                    'value' => get_user_meta($user->ID, $key, true),
                    'display' => true
                )
            );
        }

        register_setting($this->getViewKey(), $this->getViewKey(), array($this, 'process_user_option_update'));

        echo '<div class="form-profile">';
        wp_nonce_field($this->getViewKey(), 'mgrt_nonce');
        do_settings_sections($this->getViewKey());
        echo '</div>';
    }

    public function process_user_option_update($user_id)
    {
        if (!$this->getSyncManager()->shouldDoExportSync()) {
            return false;
        }
        check_admin_referer($this->getViewKey(), 'mgrt_nonce');
        if (!isset( $_POST[$this->getViewKey()])) {
            return false;
        }

        $formData = $_POST[$this->getViewKey()];

        foreach ($this->profileFields as $fieldId) {
            if (!isset($this->formFieldRelation[$fieldId])) {
                continue;
            }

            $field = $this->formFieldRelation[$fieldId];
            $key = self::FIELD_KEY.$fieldId;
            if (isset($formData[$key])) {
                if (self::validateEntry($formData[$key], $field)) {
                    update_user_meta($user_id, $key, $formData[$key]);
                }
            }
        }

    }

    public function validateEntry(&$value, $field)
    {
        if (empty($value)) {
            return false;
        }

        switch ($field->getFieldType())
        {
            case 'text':
                return true;
            case 'number':
                if (is_numeric($value)) {
                    $value = (int) $value;

                    return true;
                }

                return false;
            case 'date':
                return preg_match(self::DATE_PATTERN, $value);
            case 'website':
                return preg_match(self::URL_PATTERN, $value);
            case 'radio_buttons':
            case 'drop_down':
                return in_array($value, $field->getChoices());
            case 'checkboxes':
                foreach ($value as $val) {
                    if (!in_array($val, $field->getChoices())) {
                        return false;
                    }
                }
                return true;
            default:
                return false;
        }
    }
}
