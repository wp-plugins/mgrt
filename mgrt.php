<?php
/**
 * Plugin Name: Mgrt for Wordpress
 * Plugin URI: https://wordpress.org/plugins/mgrt/
 * Description: Link your Mgrt account with your Wordpress website.
 * Version: 1.1.2
 * Author: Mgrt
 * Author URI: https://profiles.wordpress.org/mgrt
 * License: MIT
 */
// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
define('MGRT_VERSION', '1.0');
define('MGRT__MINIMUM_WP_VERSION', '3.1');
define('MGRT__PLUGIN_URL', plugin_dir_url(__FILE__));
define('MGRT__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MGRT__PLUGIN_DIR_REL', dirname(plugin_basename(__FILE__)) . '/');
@define('MGRT__API', 'api.mgrt.net');
define('MGRT__OPTION_KEY', 'mgrt-settings');
define('MGRT__WEBHOOK_NAME', 'mgrt_webhook_wordpress');

require_once(MGRT__PLUGIN_DIR . 'vendor/autoload.php');
spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $fileName = MGRT__PLUGIN_DIR . $fileName;
    if (file_exists($fileName)) {
        require $fileName;
    }
});

if (defined('MGRT__IN_WIZARD')) {
    return;
}

if (!function_exists('wp_new_user_notification')):
    function wp_new_user_notification($user_id, $plaintext_pass = '') {
        $user = get_userdata($user_id);

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

        if (!Mgrt\Wordpress\Manager\SyncManager::$is_syncing && Mgrt\Wordpress\Manager\SyncManager::$notify_user_registration) {
            @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
        }

        if (empty($plaintext_pass)) {
            return;
        }

        $message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
        $message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
        $message .= wp_login_url() . "\r\n";

        if (Mgrt\Wordpress\Manager\SyncManager::$notify_user_registration) {
            wp_mail($user->user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);
        }

    }
    Mgrt\Wordpress\Manager\SyncManager::$hook_success = true;
endif;

add_action('init', function () {
    if (! session_id()) {
        @session_start();
    }
});


add_action('widgets_init', function(){
    register_widget('\Mgrt\Wordpress\Widget\NewsletterWidget');
    register_widget('\Mgrt\Wordpress\Widget\CampaignWidget');
});

$MgrtBootstrap = new \Mgrt\Wordpress\Bootstrap();
add_action('init', array($MgrtBootstrap, 'init'));

