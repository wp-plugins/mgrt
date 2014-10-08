<?php

namespace Mgrt\Wordpress\Manager;

use Mgrt\Client;
use Mgrt\Wordpress\Bootstrap;

/**
 * Main plugin class
 */
class DataManager
{
    private $bootstrap;
    private $MgrtClient = false;

    /**
     * Initialize main plugin
     */
    function __construct(Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
        if (!load_plugin_textdomain('mgrt-wordpress', false , MGRT__PLUGIN_DIR_REL.'languages')) {
            load_textdomain('mgrt-wordpress', MGRT__PLUGIN_DIR.'languages/mgrt-wordpress-fr_FR.mo');
        }
    }

    /**
     * Initialize Mgrt client
     */
    public function initMgrtClient()
    {
        if (!$this->hasKeys()) {
            return;
        }

        $this->MgrtClient = Client::factory(array(
            'public_key'    => $this->getApiKey(),
            'private_key'   => $this->getApiSecret(),
            'hostname'      => MGRT__API
        ));
    }

    /**
     * Retrieve API key
     */
    public function getApiKey()
    {
        return $this->checkOptionValue('mgrt_api_key', MGRT__OPTION_KEY.'-keys');
    }

    /**
     * Retrieve API Secret
     */
    public function getApiSecret()
    {
        return $this->checkOptionValue('mgrt_api_secret', MGRT__OPTION_KEY.'-keys');
    }

    /**
     * Retrieve any option
     */
    public function getOption($key, $group = MGRT__OPTION_KEY)
    {
        return $this->checkOptionValue($key, $group);
    }

    /**
     * Set an option
     */
    public function setOption($key, $value, $group = MGRT__OPTION_KEY)
    {
        $data = get_option($group);
        if ($data === false) {
            $data = array();
        }

        if (!array($data)) {
            $data = maybe_unserialize($data);
        }

        if (!array($data)) {
            $data = array();
        }

        $data[$key] = $value;

        return update_option($group, $data);
    }

    /**
     * Get an option's value. Clear it if empty
     */
    private function checkOptionValue($key, $group = MGRT__OPTION_KEY)
    {
        $k = get_option($group);
        if ($k === FALSE) {
            return false;
        }

        if (empty($k)) {
            delete_option($group);

            return false;
        }

        if (!isset($k[$key])) {
            return false;
        }

        if (empty($k[$key])) {
            unset($k[$key]);
            update_option($group, $k);

            return false;
        }

        return $k[$key];
    }

    /**
     * Clear all options
     */
    public function clear()
    {
        delete_option(MGRT__OPTION_KEY);
        delete_option(MGRT__OPTION_KEY.'-keys');
        delete_option(MGRT__OPTION_KEY.'-webhook');
        $this->clearCachedApiCalls();
    }

    /**
     * Check if both API Key and API Secret exists and are valid
     */
    public function hasKeys()
    {
        $k1 = $this->getApiKey();
        $k2 = $this->getApiSecret();
        $k3 = $this->checkOptionValue('valid', MGRT__OPTION_KEY.'-keys');

        return $k1 !== FALSE && $k2 !== FALSE && $k3;
    }

    /**
     * Return Mgrt client
     */
    public function getMgrtClient()
    {
        if ($this->MgrtClient === false) {
            $this->initMgrtClient();
        }

        return $this->MgrtClient;
    }

    /**
     * Return Mgrt client state
     */
    public function isMgrtClientReady()
    {
        if ($this->MgrtClient === false) {
            $this->initMgrtClient();
        }

        return $this->MgrtClient !== false;
    }

    public function insertOrUpdateMgrtId($user_id, $mgrt_id)
    {
        return $this->insertOrUpdateCustomField($user_id, '__mgrt_id', $mgrt_id);
    }

    public function getUserIdByMgrtId($mgrt_id)
    {
        global $wpdb;
        $r = $wpdb->get_results($wpdb->prepare('SELECT `user_id` FROM `'.$wpdb->usermeta.'` WHERE `meta_key` = "__mgrt_id" AND `meta_value` = "%s"', $mgrt_id));
        if (empty($r)) {
            return 0;
        }

        return (int) $r[0]->user_id;
    }

    public function getMgrtIdByUserId($user_id)
    {
        return (int) $this->getCustomField($user_id, '__mgrt_id');
    }

    public function insertOrUpdateCustomField($user_id, $field_key, $field_value)
    {
        global $wpdb;

        $exists =  $this->getCustomField($user_id, $field_key);
        if ($exists === false) {
            $query = 'INSERT INTO `'.$wpdb->usermeta.'`(`user_id`, `meta_key`, `meta_value`) VALUES (%d, "%s", "%s")';
        } else {
            $query = 'UPDATE `'.$wpdb->usermeta.'` SET `meta_value` = "%3$s" WHERE `meta_key` = "%2$s" AND `user_id` = %1$d';
        }

        if (is_array($field_value) || is_object($field_value)) {
            $field_value = serialize($field_value);
        }

        return $wpdb->query($wpdb->prepare($query, $user_id, $field_key, $field_value));
    }

    public function getCustomField($user_id, $field_key)
    {
        global $wpdb;

        $r = $wpdb->get_results( $wpdb->prepare('SELECT `meta_value` FROM `'.$wpdb->usermeta.'` WHERE `meta_key` = "%s" AND `user_id` = %d', $field_key, $user_id));
        if (empty($r)) {
            return false;
        }

        return $r[0]->meta_value;
    }

    public function clearCachedApiCalls()
    {
        global $wpdb;

        return $wpdb->query('DELETE FROM `'.$wpdb->options.'` WHERE `option_name` LIKE "_transient_mgrt_%" OR `option_name` LIKE "_transient_timeout_mgrt_%"');
    }

    public function getTotalUsers()
    {
        global $wpdb;

        $r = $wpdb->get_results('SELECT COUNT(ID) as total FROM `'.$wpdb->users.'`');
        if (empty($r)) {
            return 0;
        }

        return (int) $r[0]->total;

    }

    /**
     * Clear an API call
     * @param string $func name of the function to clear
     * @param mixed $param parameters
     */
    public function clearCachedApiCall($func, $param = array())
    {
        if (!is_array($param)) {
            $param = array($param);
        }

        $callHash = 'mgrt_'.md5($func.serialize($param));
        delete_transient($callHash);
    }

    /**
     * Make an API call without caching it
     * @param string $func name of the function to call
     * @param mixed $param parameters
     * @return object
     */
    public function makeApiCall($func, $param = array())
    {
        if ($this->MgrtClient === false) {
            $this->initMgrtClient();
        }

        if (!is_array($param)) {
            $param = array($param);
        }

        set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
            // error was suppressed with the @-operator
            if (0 === error_reporting()) {
                return false;
            }

            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
        $result = call_user_func_array(array($this->MgrtClient, $func), $param);
        restore_error_handler();

        return $result;
    }

    /**
     * Cache API call response in WP database
     * @param string $func name of the function to call
     * @param mixed $param parameters
     * @param int $duration in seconds
     * @return object
     */
    public function makeCachedApiCall($func, $param = array(), $duration = DAY_IN_SECONDS, $force = false)
    {
        if (!is_array($param)) {
            $param = array($param);
        }

        $callHash = 'mgrt_'.md5($func.serialize($param));
        if (false === ($callResult = get_transient($callHash)) || $force) {
            $callResult = $this->makeApiCall($func, $param);
            set_transient($callHash, $callResult, $duration);
        }

        return $callResult;
    }

}
