<?php

namespace Mgrt\Wordpress\View;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Mgrt\Client;
use Mgrt\Wordpress\AbstractView;
use Mgrt\Wordpress\Bootstrap;
use Mgrt\Wordpress\Manager\DataManager;
use Mgrt\Wordpress\Manager\ViewManager;

/**
 * API keys config view
 */
class ApiKeys extends AbstractView
{
    /**
     * Initialize view
     */
    public function __construct(Bootstrap $bootstrap)
    {
        parent::__construct($bootstrap);
        add_settings_section(
            'mgrt_key_section',
            __('form.keys.section', 'mgrt-wordpress'),
            array($this, 'sectionCallback'),
            $this->getViewKey()
        );
        add_settings_field(
            'mgrt_api_key',
            __('plugin.api.key', 'mgrt-wordpress'),
            array($this, 'inputFieldCallback'),
            $this->getViewKey(),
            'mgrt_key_section',
            'mgrt_api_key'
        );
        add_settings_field(
            'mgrt_api_secret',
            __('plugin.api.secret', 'mgrt-wordpress'),
            array($this, 'inputFieldCallback'),
            $this->getViewKey(),
            'mgrt_key_section',
            'mgrt_api_secret'
        );
        register_setting($this->getViewKey(), $this->getViewKey(), array($this, 'validate_options'));

        /**
         * check if keys are valid with ajax
         */
        add_action('wp_ajax_mgrt_check_keys', function() {
            $api_key = $_POST['api_key'];
            $api_secret = $_POST['api_secret'];
            exit($this->checkKeys($api_key, $api_secret));
        });
    }

    public function getViewKey()
    {
        return MGRT__OPTION_KEY.'-keys';
    }

    public function getViewName()
    {
        return 'ApiKeys';
    }

    /**
     * Validate keys before storing
     *
     * @param array $options keys
     * @return array
     */
    public function validate_options($options)
    {
        if (isset($options['valid'])) {
            return $options;
        }

        if (empty($options['mgrt_api_key']) || empty($options['mgrt_api_secret'])) {
            add_settings_error($this->getViewKey(), 'keys', __('form.keys.validate.error', 'mgrt-wordpress'));
            return $options;
        }

        if (!$this->checkKeys($options['mgrt_api_key'], $options['mgrt_api_secret'])) {
            add_settings_error($this->getViewKey(), 'keys', __('form.keys.validate.error', 'mgrt-wordpress'));
            $options['valid'] = false;
        } else {
            add_settings_error($this->getViewKey(), 'keys', __('form.keys.validate.updated', 'mgrt-wordpress'), 'updated');
            $this->getViewManager()->sheduleNotice('valid_keys');
            $options['valid'] = true;
            $this->getDataManager()->clear();
        }

        return $options;
    }

    /**
     * Display view
     */
    public function display()
    {
        $this->getViewManager()->redirect('Start', 'header');
        ?>
            <form method="POST" action="options.php">
                <?php settings_fields($this->getViewKey());
                settings_errors($this->getViewKey());
                do_settings_sections($this->getViewKey());
                submit_button();
                ?>
            </form>
        <?php
    }

    /**
     * Display section header
     */
    public function sectionCallback()
    {
        echo '<p>'.__('form.keys.section.text', 'mgrt-wordpress').'</p>';
    }

    /**
     * Display input field
     *
     * @param string $key key type
     */
    public function inputFieldCallback($key)
    {
        echo '<input id="'.$key.'" name="'.MGRT__OPTION_KEY.'-keys['.$key.']" type="'.($key == 'mgrt_api_secret' ? 'password' : 'text' ).'" class="code" value="'.esc_attr($this->getDataManager()->getOption($key, MGRT__OPTION_KEY.'-keys')).'" />';
    }

    /**
     * Check if keys are valid
     *
     * @param string $api_key API Key
     * @param string $api_secret API Secret
     * @return bool
     */
    public function checkKeys($api_key, $api_secret)
    {
        try {
            $client = Client::factory(array(
                'public_key'    => $api_key,
                'private_key'   => $api_secret,
                'hostname'      => MGRT__API,
            ));

            return 'Hello World!' == $client->getHelloWorld();
        } catch (ClientErrorResponseException $e) {
            return false;
        } catch (\ErrorException $e) {
            return false;
        }
    }
}
