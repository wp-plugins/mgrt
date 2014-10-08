<?php

namespace Mgrt\Wordpress\View;

use Mgrt\Wordpress\AbstractView;
use Mgrt\Wordpress\Bootstrap;
use Mgrt\Wordpress\Manager\SyncManager;
use Mgrt\Wordpress\Manager\ViewManager;

class Notice extends AbstractView
{

    public function getViewKey()
    {
        return MGRT__OPTION_KEY.'-notice';
    }

    public function getViewName()
    {
        return 'Notice';
    }
    /**
     * No keys
     */
    public function install()
    {
        ?>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
        <div class="alert alert-info">
            <span class="alert-bigbutton"><a class="button button-primary" href="<?php echo esc_url( $this->getViewManager()->url('ApiKeys') ); ?>"><?php _e('notice.install.btn', 'mgrt-wordpress') ?></a></span>
            <span class="alert-righttext"><strong><?php _e('notice.install.strong', 'mgrt-wordpress') ?></strong> - <?php _e('notice.install.lore', 'mgrt-wordpress') ?></span>
        </div>
</div>
        <?php
    }

    /**
     * Valid keys
     */
    public function valid_keys()
    {
        ?>
<script type="text/javascript">
    setTimeout(function() {
        window.location.replace('<?php echo $this->getViewManager()->url() ?>')
    }, 1000);
</script>
        <?php
    }

    /**
     * Load Error
     */
    public function loadError()
    {
        ?>
        <div class="alert alert-error">
            <strong><?php _e('notice.error.strong', 'mgrt-wordpress') ?></strong> - <?php _e('notice.error.lore', 'mgrt-wordpress') ?>
        </div>
        <?php
    }

    /**
     * No Curl Extension
     */
    public function no_curl()
    {
        ?>
        <div class="alert alert-error">
            <strong><?php _e('notice.curl.strong', 'mgrt-wordpress') ?></strong> - <?php _e('notice.curl.lore', 'mgrt-wordpress') ?>
        </div>
        <?php
    }
}
