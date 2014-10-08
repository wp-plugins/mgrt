<?php

namespace Mgrt\Wordpress;

use Mgrt\Wordpress\Manager\DataManager;
use Mgrt\Wordpress\Manager\SyncManager;
use Mgrt\Wordpress\Manager\ViewManager;
use Mgrt\Wordpress\Shortcode\NewsletterShortcode;
use Mgrt\Wordpress\Shortcode\CampaignShortcode;

/**
 * Administration panel class
 */
class Bootstrap
{
    private static $instance = null;

    private $dataManager;
    private $viewManager;
    private $syncManager;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
            self::$instance->init_hooks();
        }

        return self::$instance;
    }

    /**
     * Initialize admin panel
     */
    public function init()
    {
        self::getInstance();
    }

    public function getDataManager()
    {
        return $this->dataManager;
    }

    public function getViewManager()
    {
        return $this->viewManager;
    }

    public function getSyncManager()
    {
        return $this->syncManager;
    }

    /**
     * Initialize hooks for administration
     */
    public function init_hooks()
    {
        $this->dataManager = new DataManager($this);
        $this->viewManager = new ViewManager($this);
        $this->syncManager = new SyncManager($this);

        $this->newsletterShortcode  = new NewsletterShortcode();
        $this->campaignShortcode    = new CampaignShortcode();

        wp_register_style('mgrt_public.css', MGRT__PLUGIN_URL.'assets/css/mgrt_public.css', array(), MGRT_VERSION);
        wp_enqueue_style('mgrt_public.css');

        if (is_admin()) {
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('admin_head', function () {
                echo '<script type="text/javascript">var mgrt_wp_base="'.ABSPATH.'";</script>';
            });
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_notices', array($this, 'display_notice'));
            add_action('admin_enqueue_scripts', array($this, 'load_resources'));

            add_filter('plugin_action_links_'.plugin_basename( MGRT__PLUGIN_DIR.'mgrt.php'), array($this, 'admin_plugin_settings_link'));
        }
    }

    /**
     * Load assets
     */
    public function load_resources()
    {
        if ($this->checkHookSuffix()) {
            wp_register_style('mgrt.css', MGRT__PLUGIN_URL.'assets/css/mgrt.css', array('mgrt_public.css'), MGRT_VERSION);
            wp_enqueue_style('mgrt.css');

            wp_register_script('mgrt.js', MGRT__PLUGIN_URL.'assets/js/mgrt.js', array('jquery'), MGRT_VERSION);
            wp_enqueue_script('mgrt.js');
        }

        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        global $typenow;

        if (!in_array($typenow, array( 'post', 'page'))) {
            return;
        }

        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', function ($plugin_array)
            {
                $plugin_array['mgrt_tinymce_buttons'] = MGRT__PLUGIN_URL.'assets/js/mgrt_shortcode.js';
                return $plugin_array;
            });
            add_filter('mce_buttons', function ($buttons)
            {
                array_push($buttons, 'mgrt_tinymce_buttons');
                return $buttons;
            });
        }
    }

    /**
     * Display notice
     */
    public function display_notice()
    {
        if ($this->viewManager->hasNotices()) {
            $this->viewManager->displayNotices();
        } else {
            if ($this->checkHookSuffix() && !$this->dataManager->hasKeys()) {
                $this->viewManager->display('Notice', 'install');
            }
        }
    }

    /**
     * Check if hook_suffix is for us
     * @return bool
     */
    private function checkHookSuffix()
    {
        global $hook_suffix;

        return (in_array($hook_suffix, array(
            'index.php', # dashboard
            'plugins.php',
            'users.php',
            'profile.php'
        )) || strpos($hook_suffix, MGRT__OPTION_KEY) !== FALSE);
    }

    /**
     * Add admin menu link
     */
    public function admin_menu()
    {
        $platformName = 'Mgrt';
        if ($this->dataManager->hasKeys()) {
            try {
                $platformName = $this->dataManager->makeCachedApiCall('getAccount')->getPlatformName();
            } catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
                $platformName = 'Mgrt';
            } catch (\ErrorException $e) {
                $this->viewManager->sheduleNotice('no_curl');
                return;
            }

            add_menu_page(sprintf(__('plugin.menu.start.1p', 'mgrt-wordpress'), $platformName), $platformName, 'manage_options', MGRT__OPTION_KEY, array($this, 'display_page'), 'dashicons-email');
            add_submenu_page(MGRT__OPTION_KEY, __('plugin.api.key', 'mgrt-wordpress'), __('plugin.api.key', 'mgrt-wordpress'), 'manage_options', MGRT__OPTION_KEY.'-keys', array($this, 'display_page'));

            if ($this->dataManager->getOption('enable_sync')) {
                add_submenu_page(MGRT__OPTION_KEY, __('plugin.menu.sync', 'mgrt-wordpress'), __('plugin.menu.sync', 'mgrt-wordpress'), 'manage_options', MGRT__OPTION_KEY.'-force-sync', array($this, 'display_page'));
            }
        } else {
            add_menu_page(sprintf(__('plugin.menu.start.1p', 'mgrt-wordpress'), $platformName), $platformName, 'manage_options', MGRT__OPTION_KEY.'-keys', array($this, 'display_page'), 'dashicons-email');
        }
    }

    /**
     * Add plugin "Settings" link
     * @param array $links Array of existing links
     */
    public function admin_plugin_settings_link($links)
    {
        $settings_link = '<a href="'.esc_url($this->viewManager->url()).'">'.__('Settings').'</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    public function admin_init()
    {
        $this->viewManager->initAllViews();
    }

    /**
     * Route input urls
     */
    public function display_page()
    {
        $this->viewManager->route();
    }
}
