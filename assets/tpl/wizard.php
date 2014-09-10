<?php
    define('MGRT__IN_WIZARD', true);
    if (empty($_GET['base'])) {
        die('UNAUTHORIZED');
    }
    $base = $_GET['base'];
    require_once $base.'wp-admin/admin.php';
    require_once '../../mgrt.php';

    $type = 'newsletter';
    if (!empty($_GET['type'])) {
        $type = str_replace('.', '', $_GET['type']);
    }

    $Bootstrap = \Mgrt\Wordpress\Bootstrap::getInstance();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php _e('wizard.title', 'mgrt-wordpress') ?></title>
    <?php
        wp_register_style('mgrt.css', MGRT__PLUGIN_URL.'assets/css/mgrt.css', array('dashicons','admin-bar','buttons','media-views','wp-admin','wp-auth-check'), MGRT_VERSION);
        wp_enqueue_style('mgrt.css');
        do_action( 'admin_print_styles' );
        do_action( 'admin_print_scripts' );
    ?>
    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="<?php echo get_site_url() . '/' . WPINC ?>/js/tinymce/tiny_mce_popup.js"></script>
</head>
<body id="wizard-inner" class="wp-core-ui">
<div class="body-wrapper">
<?php
try {
    $account = $Bootstrap->getDataManager()->makeCachedApiCall('getAccount');
    $lists = $Bootstrap->getDataManager()->makeCachedApiCall('getMailingLists');
    $_customFields = $Bootstrap->getDataManager()->makeCachedApiCall('getCustomFields');
    $customFields = array();
    foreach ($_customFields as $cf) {
        $customFields[$cf->getId()] = $cf;
    }
} catch (ClientErrorResponseException $e) {
    echo 'Error: '.$e->getMessage();

    return;
}
?>
    <img class="plugin-header-logo" src="<?php echo $account->getLogoUrl() ?>" alt="<?php echo $account->getPlatformName() ?>" style="background-color: #<?php echo $account->getHeaderBackgroundColor() ?>" />

    <h4><?php _e('wizard.type', 'mgrt-wordpress') ?></h4>
    <div id="selected-code">
        <label><input type="radio" name="code" value="newsletter" class="toggle-ctrl" data-toggle=".type-newsletter" data-other=".type-campaign" data-hide="true" checked="checked" /><?php _e('wizard.type.newsletter', 'mgrt-wordpress') ?></label>
        <label><input type="radio" name="code" value="campaign" class="toggle-ctrl" data-toggle=".type-campaign" data-other=".type-newsletter" data-hide="true" /><?php _e('wizard.type.campaign', 'mgrt-wordpress') ?></label>
    </div>
    <h4><?php _e('wizard.text', 'mgrt-wordpress') ?></h4>
    <div>
        <input id="shortcode-text" type="text" name="text" placeholder="<?php _e('wizard.text.help', 'mgrt-wordpress') ?>" />
    </div>
    <div class="type-newsletter">
        <h4><?php _e('wizard.newsletter.lists', 'mgrt-wordpress') ?></h4>
        <div class="checkbox-list" id="selected-lists">
        <?php foreach ($lists as $list): ?>
            <div>
                <label><input type="checkbox" value="<?php echo $list->getId() ?>" /><?php echo $list->getName() ?></label>
            </div>
        <?php endforeach; ?>
        </div>
        <p class="description"><?php _e('wizard.newsletter.lists.help', 'mgrt-wordpress') ?></p>

    <?php if (!empty($customFields)): ?>
        <div class="checkbox-list" id="selected-fields">
        <?php foreach ($customFields as $cf): ?>
            <div>
                <label><input type="checkbox" value="<?php echo $cf->getId() ?>" /><?php echo $cf->getName() ?></label>
            </div>
        <?php endforeach; ?>
        </div>
        <p class="description"><?php _e('wizard.newsletter.lists.help', 'mgrt-wordpress') ?></p>
    <?php endif; ?>
    </div>
    <div class="type-campaign" style="display: none">
        <h4><?php _e('wizard.campaign.count', 'mgrt-wordpress') ?></h4>
        <div>
            <input id="campaign-count" type="number" name="text" min="1" max="15" value="5" />
        </div>
        <p class="description"><?php _e('wizard.campaign.count.help', 'mgrt-wordpress') ?></p>
    </div>
</div>
<div class="footer">
    <div class="footer-wrapper">
        <hr />
        <a href="#" id="submit-all" class="button button-primary">Valider</a> &nbsp;
        <a href="#" id="cancel-all" class="button">Annuler</a>
    </div>
</div>

<script type="text/javascript" src="<?php echo MGRT__PLUGIN_URL ?>assets/js/mgrt_wizard.js"></script>
<script type="text/javascript" src="<?php echo MGRT__PLUGIN_URL ?>assets/js/mgrt.js"></script>
</body>
</html>
