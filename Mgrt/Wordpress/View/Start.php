<?php

namespace Mgrt\Wordpress\View;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Mgrt\Model\Webhook;
use Mgrt\Wordpress\AbstractView;
use Mgrt\Wordpress\Bootstrap;
use Mgrt\Wordpress\Manager\SyncManager;
use Mgrt\Wordpress\Manager\ViewManager;

/**
 * Main view
 */
class Start extends AbstractView
{
    private $custom_fields = array();

    /**
     * Initialize view
     */
    public function __construct(Bootstrap $bootstrap)
    {
        parent::__construct($bootstrap);

        if (! $this->getDataManager()->hasKeys()) {
            return;
        }

        try {
            $this->custom_fields = $this->getDataManager()->makeCachedApiCall('getCustomFields');
        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
            $this->ready = false;
            return;
        } catch (\ErrorException $e) {
            $this->ready = false;
            $this->getViewManager()->sheduleNotice('no_curl');
            return false;
        }

        add_settings_section(
            'mgrt_sync_section',
            __('form.sync.section', 'mgrt-wordpress'),
            function() {},
            $this->getViewKey()
        );
        add_settings_field(
            'enable_sync',
            __('form.sync.field.state', 'mgrt-wordpress'),
            array($this, 'enableAccountSync'),
            $this->getViewKey(),
            'mgrt_sync_section'
        );
        add_settings_field(
            'sync_direction',
            __('form.sync.field.direction', 'mgrt-wordpress'),
            array($this, 'selectAccountSyncDirection'),
            $this->getViewKey(),
            'mgrt_sync_section'
        );
        add_settings_field(
            'lists',
            __('form.sync.field.lists', 'mgrt-wordpress'),
            array($this, 'selectAccountSyncList'),
            $this->getViewKey(),
            'mgrt_sync_section'
        );
        add_settings_field(
            'custom_fields',
            __('form.sync.field.custom', 'mgrt-wordpress'),
            array($this, 'matchCustomFields'),
            $this->getViewKey(),
            'mgrt_sync_section'
        );
        add_settings_field(
            'custom_fields_additional',
            __('form.sync.field.custom.more', 'mgrt-wordpress'),
            array($this, 'addCustomFields'),
            $this->getViewKey(),
            'mgrt_sync_section'
        );

        register_setting($this->getViewKey(), $this->getViewKey(), array($this, 'validate_options'));
    }

    public function getViewKey()
    {
        return MGRT__OPTION_KEY;
    }

    public function getViewName()
    {
        return 'Start';
    }

    /**
     * Validate form values
     * @param array $options submitted form values
     * @return array
     */
    public function validate_options($options)
    {
        $options['enable_sync'] = isset($options['enable_sync']) && $options['enable_sync'] == 'on';
        if (!$options['enable_sync']) {
            return array(
                'enable_sync' => false
            );
        }

        if (!empty($options['lists'])) {
            foreach ($options['lists'] as &$list) {
                $list = intval($list);
            }
        }

        if (!empty($options['sync_direction'])) {
            if (!in_array($options['sync_direction'], array(
                'up',
                'down',
                'both'
            ))) {
               $options['sync_direction'] = 'up';
            }
        }

        if ($options['sync_direction'] == 'down' || $options['sync_direction'] == 'both') {
            if (!$this->makeWebhook()) {
                add_settings_error($this->getViewKey(), 'keys', __('Error').': '.$ex->getMessage());
                return array('enable_sync' => false);
            }
            $this->getDataManager()->setOption('webhook_failure', false, MGRT__OPTION_KEY.'-webhook');
        } else {
            $this->getDataManager()->setOption('webhook_secret_key', '', MGRT__OPTION_KEY.'-webhook');
        }

        add_settings_error($this->getViewKey(), 'keys', __('Settings saved.'), 'updated');
        return $options;
    }

    private function makeWebhook()
    {
        $webhooks = $this->getDataManager()->makeApiCall('getWebhooks');
        $seek_url = get_home_url() . '/?webhook';
        $require_events = $this->getSyncManager()->getListenedEvents();
        $require_sources = $this->getSyncManager()->getListenedSources();
        $found = $need_update = false;
        $site_webhook = null;
        foreach ($webhooks as $webhook) {
            if ($webhook->getCallbackUrl() == $seek_url) {
                if (!(
                    array_intersect($require_events, $webhook->getListenedEvents()) == $require_events &&
                    array_intersect($require_sources, $webhook->getListenedSources()) == $require_sources &&
                    $webhook->getEnabled()
                )) {
                    $need_update = true;
                }
                $found = true;
                $site_webhook = $webhook;
                break;
            }
        }
        if (!$found || $need_update) {
            $site_webhook = ($found ? $site_webhook : new Webhook());
            $site_webhook
                ->setCallbackUrl($seek_url)
                ->setListenedEvents($require_events)
                ->setListenedSources($require_sources)
                ->setName(MGRT__WEBHOOK_NAME);

            $func = $found ? 'updateWebhook' : 'createWebhook';
            try {
                $func_result = $this->getDataManager()->makeApiCall($func, array($site_webhook));
                if ($found && !$func_result) {
                    return false;
                }

                $site_webhook = $found ? $site_webhook : $func_result;
            } catch (Exception $ex) {
                return false;
            }
        }

        // enable webhook if needed
        if (!$site_webhook->getEnabled()) {
            $this->getDataManager()->makeApiCall('enableWebhook', array($site_webhook));
        }

        $this->getDataManager()->setOption('webhook_secret_key', $site_webhook->getSecretKey(), MGRT__OPTION_KEY.'-webhook');
        return true;
    }



    public function enableAccountSync()
    {
        ?>
        <label>
            <input type="checkbox" class="toggle-ctrl" data-toggle=".syncstate" name="<?php echo $this->getViewKey().'[enable_sync]' ?>"<?php echo $this->getDataManager()->getOption('enable_sync') ? ' checked="checked"' : '' ?> />
            <?php _e('form.sync.field.state.text', 'mgrt-wordpress') ?>
        </label>
        <?php
    }

    public function selectAccountSyncDirection()
    {
        $selected_direction = $this->getDataManager()->getOption('sync_direction');
        try {
            $account = $this->getDataManager()->makeCachedApiCall('getAccount');
        } catch (ClientErrorResponseException $e) {
            echo 'Error: '.$e->getMessage();

            return;
        }
        ?>
        <select class="syncstate" name="<?php echo $this->getViewKey().'[sync_direction]' ?>"<?php echo !$this->getDataManager()->getOption('enable_sync') ? ' disabled="disabled"' : '' ?>>
            <option value="up"<?php echo $selected_direction == 'up' ? ' selected="selected"' : '' ?>><?php echo sprintf(__('form.sync.field.direction.upward.2p', 'mgrt-wordpress'), 'Wordpress "'.get_option('blogname').'"', $account->getPlatformName()) ?></option>
            <option value="down"<?php echo $selected_direction == 'down' ? ' selected="selected"' : '' ?>><?php echo sprintf(__('form.sync.field.direction.downward.2p', 'mgrt-wordpress'), 'Wordpress "'.get_option('blogname').'"', $account->getPlatformName()) ?></option>
            <option value="both"<?php echo $selected_direction == 'both' ? ' selected="selected"' : '' ?>><?php echo sprintf(__('form.sync.field.direction.both.2p', 'mgrt-wordpress'), 'Wordpress "'.get_option('blogname').'"', $account->getPlatformName()) ?></option>
        </select>
        <?php if ($selected_direction == 'down' || $selected_direction == 'both'): ?>
            <a href="<?php echo esc_url( $this->getViewManager()->url('Start', 'regen_webhook') ); ?>" class="button button-secondary"><?php _e('webhook.regenerate', 'mgrt-wordpress') ?></a>
        <?php endif;
    }

    public function selectAccountSyncList()
    {
        try {
            $lists = $this->getDataManager()->makeCachedApiCall('getMailingLists');
        } catch (ClientErrorResponseException $e) {
            echo 'Error: '.$e->getMessage();

            return;
        }

        $enabled_lists = $this->getDataManager()->getOption('lists');
        $enabled_lists === false && $enabled_lists = array();
        ?>
        <fieldset class="checkbox-list syncstate"<?php echo !$this->getDataManager()->getOption('enable_sync') ? ' disabled="disabled"' : '' ?>>
            <?php foreach ($lists as $list): ?>
                <div>
                    <label>
                        <input type="checkbox" name="<?php echo $this->getViewKey().'[lists][]' ?>" value="<?php echo $list->getId() ?>"<?php echo in_array($list->getId(), $enabled_lists) ? ' checked="checked"' : '' ?> />
                        <?php echo $list->getName() ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </fieldset>
        <p class="description"><?php _e('form.sync.field.lists.help', 'mgrt-wordpress') ?></p>
        <?php
    }

    public function matchCustomFields()
    {
        $wp_fields = SyncManager::getFields();

        if (!($enabled_custom_fields = $this->getDataManager()->getOption('custom_fields'))) {
            $enabled_custom_fields = array();
        }
        ?>
        <fieldset class="dropdown-list syncstate"<?php echo !$this->getDataManager()->getOption('enable_sync') ? ' disabled="disabled"' : '' ?>>
            <?php foreach ($wp_fields as $wp_field_name => $wp_field_text): ?>
                <label>
                    <span class="label">
                        <?php echo $wp_field_text ?>
                    </span>
                    <select name="<?php echo $this->getViewKey().'[custom_fields]['.$wp_field_name.']' ?>">
                        <option value="-1"<?php echo (isset($enabled_custom_fields[$wp_field_name]) && (-1 == $enabled_custom_fields[$wp_field_name]) ? ' selected="selected"' : '') ?>><?php _e('form.sync.field.custom.ignore', 'mgrt-wordpress') ?></option>
                    <?php foreach ($this->custom_fields as $custom_field): ?>
                        <option value="<?php echo $custom_field->getId() ?>"<?php echo (isset($enabled_custom_fields[$wp_field_name]) && ($custom_field->getId() == $enabled_custom_fields[$wp_field_name]) ? ' selected="selected"' : '') ?>><?php echo $custom_field->getName() ?></option>
                    <?php endforeach; ?>
                    </select>
                </label>
            <?php endforeach; ?>
        </fieldset>
        <p class="description"><?php _e('form.sync.field.custom.help', 'mgrt-wordpress') ?></p>
        <?php
    }

    public function addCustomFields()
    {
    /*$enabled_lists = $this->getDataManager()->getOption('lists');
        $enabled_lists === false && $enabled_lists = array();*/
        if (!($enabled_custom_fields = $this->getDataManager()->getOption('custom_fields'))) {
            $enabled_custom_fields = array();
        }
        if (!($enabled_profile_fields = $this->getDataManager()->getOption('custom_fields_additional'))) {
            $enabled_profile_fields = array();
        }
        ?>
        <fieldset data-rm-target="#custom-fields .custom-field-select option" id="profile-fields" class="checkbox-list rm-ctrl syncstate"<?php echo !$this->getDataManager()->getOption('enable_sync') ? ' disabled="disabled"' : '' ?>>
            <?php foreach ($this->custom_fields as $custom_field): ?>
                <div>
                    <label>
                        <input class="profile-field-checkbox" type="checkbox" name="<?php echo $this->getViewKey().'[custom_fields_additional][]' ?>" value="<?php echo $custom_field->getId() ?>"<?php echo in_array($custom_field->getId(), $enabled_profile_fields) ? ' checked="checked"' : '' ?> />
                        <?php echo $custom_field->getName() ?> (<?php _e('customfield.type.'.$custom_field->getFieldType(), 'mgrt-wordpress') ?>)
                    </label>
                </div>
            <?php endforeach; ?>
        </fieldset>
        <p class="description"><?php _e('form.sync.field.custom.more.help', 'mgrt-wordpress') ?></p>
        <?php
    }

    /**
     * Display view
     */
    public function display()
    {
        if (!$this->getDataManager()->hasKeys()) {
            return $this->getViewManager()->redirect('ApiKeys');
        }
        $this->getViewManager()->redirect('Start', 'header');
        ?>
        <?php if (!SyncManager::$hook_success): ?>
        <div class="alert alert-danger"><strong><?php _e('Warning!') ?></strong> <?php _e('plugin.no.mailhook', 'mgrt-wordpress') ?></div>
        <?php endif; ?>
            <form method="POST" action="options.php">
                <?php
                    settings_fields($this->getViewKey());
                    do_settings_sections($this->getViewKey());
                ?>
                <p class="submit">
                <?php
                    submit_button(__('Save Changes'), 'primary', 'submit', false);
                ?>
                    <a class="button button-secondary<?php echo !$this->getDataManager()->getOption('enable_sync') ? ' disabled' : '' ?>" href="<?php echo !$this->getDataManager()->getOption('enable_sync') ? '#' : $this->getViewManager()->url('ForceSync') ?>"><?php echo !$this->getDataManager()->getOption('enable_sync') ? __('form.sync.btn.force.disabled', 'mgrt-wordpress') : __('form.sync.btn.force.enabled', 'mgrt-wordpress') ?></a>
                </p>
            </form>
        <?php
    }

    /**
     * Clear config view
     */
    public function clear()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET'):
        ?>
        <h2><?php _e('param.clear.title', 'mgrt-wordpress') ?></h2>
        <p><?php _e('param.clear.warning', 'mgrt-wordpress') ?></p>
        <form method="post" action="<?php echo $this->getViewManager()->url('Start', 'clear') ?>">
            <?php wp_nonce_field() ?>
            <button type="submit" class="button button-primary"><?php _e('param.clear.confirm', 'mgrt-wordpress') ?></button> <a class="button button-secondary" href="<?php echo $this->getViewManager()->url() ?>"><?php _e('Cancel') ?></a>
        </form>
        <?php
        elseif (wp_verify_nonce($_POST['_wpnonce'])):
            $this->getDataManager()->clear();
        ?>
        <h2><?php _e('param.clear.after', 'mgrt-wordpress') ?></h2>
        <a href="<?php echo $this->getViewManager()->url() ?>"><?php _e('btn.back.config', 'mgrt-wordpress') ?></a>
        <?php
        endif;
    }

    /**
     * Clear cache view
     */
    public function clear_cached()
    {
        $deleted = $this->getDataManager()->clearCachedApiCalls();
        ?>
        <h2><?php echo sprintf(__('cache.clear.after.1p', 'mgrt-wordpress'), $deleted); ?></h2>
        <a href="<?php echo $this->getViewManager()->url() ?>"><?php _e('btn.back.config', 'mgrt-wordpress') ?></a>
        <?php
    }

    public function regen_webhook()
    {
        if (!$this->getDataManager()->hasKeys()) {
            return $this->getViewManager()->redirect('ApiKeys');
        }
        $selected_direction = $this->getDataManager()->getOption('sync_direction');
        if (!($selected_direction == 'down' || $selected_direction == 'both')) {
            return $this->getViewManager()->redirect('Start');
        }

        $this->makeWebhook();
        $this->getDataManager()->setOption('webhook_failure', false, MGRT__OPTION_KEY.'-webhook');
        ?>
        <h2><?php _e('webhook.regenerated', 'mgrt-wordpress'); ?></h2>
        <a href="<?php echo $this->getViewManager()->url() ?>"><?php _e('btn.back.config', 'mgrt-wordpress') ?></a>
        <?php
    }

    /**
     * Print header
     */
    public function header()
    {
        if (!$this->getDataManager()->hasKeys()) {
            return 'NOKEYS';
        }
        try {
            $account = $this->getDataManager()->makeCachedApiCall('getAccount');
        } catch (ClientErrorResponseException $e) {
            echo 'Error: '.$e->getMessage();

            return;
        }
        ?>
            <div class="plugin-header" style="background-color: #<?php echo $account->getHeaderBackgroundColor() ?>; color: #<?php echo $account->getHeaderTextColor() ?>;">
                <div class="plugin-header-content">
                    <a class="right button button-secondary delete is-last" href="<?php echo esc_url( $this->getViewManager()->url('Start', 'clear') ); ?>"><?php _e('btn.reset', 'mgrt-wordpress') ?></a>
                    <a class="right button button-secondary" href="<?php echo esc_url( $this->getViewManager()->url('Start', 'clear_cached') ); ?>"><?php _e('btn.clear.cache', 'mgrt-wordpress') ?></a>
                    <a href="<?php echo $account->getLoginUrl() ?>" target="_blank">
                        <img class="plugin-header-logo" src="<?php echo $account->getLogoUrl() ?>" alt="<?php echo $account->getPlatformName() ?>" />
                    </a>
                    <div class="plugin-header-title"><?php echo $account->getPlatformName() ?></div>
                </div>
            </div>
        <?php
    }

}
