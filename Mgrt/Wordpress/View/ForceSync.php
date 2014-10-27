<?php

namespace Mgrt\Wordpress\View;

use Mgrt\Model\MailingList;
use Mgrt\Wordpress\AbstractView;
use Mgrt\Wordpress\Bootstrap;
use Mgrt\Wordpress\Manager\SyncManager;
use Mgrt\Wordpress\Manager\ViewManager;

/**
 * Force sync view
 */
class ForceSync extends AbstractView
{
    /**
     * Display view
     */
    public function display()
    {
        $this->getViewManager()->redirect('Start', 'header');
        if (!$this->getDataManager()->getOption('enable_sync')):
        ?>
        <h2><?php _e('plugin.sync.disabled', 'mgrt-wordpress') ?></h2>
        <a href="<?php echo $this->getViewManager()->url() ?>"><?php _e('btn.back.config', 'mgrt-wordpress') ?></a>
        <?php
        else:
            try {
                $account = $this->getDataManager()->makeCachedApiCall('getAccount');
            } catch (ClientErrorResponseException $e) {
                echo 'Error: '.$e->getMessage();

                return;
            } catch (\ErrorException $e) {
                $this->ready = false;
                $this->getViewManager()->sheduleNotice('no_curl');
                return false;
            }

            $sync_direction = $this->getDataManager()->getOption('sync_direction');
        ?>
        <div class="alert alert-warning"><strong><?php _e('Warning!') ?></strong> <?php _e('sync.force.warning', 'mgrt-wordpress') ?></div>
        <?php if (!SyncManager::$hook_success): ?>
        <div class="alert alert-danger"><strong><?php _e('Warning!') ?></strong> <?php _e('plugin.no.mailhook', 'mgrt-wordpress') ?></div>
        <?php endif; ?>
        <div class="sync-wizard">
            <div id="sync_start">
                <a href="#" id="sync_start_go" class="button button-primary sync-wizard-button"><?php _e('sync.force.start', 'mgrt-wordpress') ?></a>
            <?php if ($sync_direction == 'both'): ?>
                <div id="sync_priority">
                    <label><input type="radio" name="sync.force.priority" value="0" checked="checked" /><?php _e('sync.force.priority.website', 'mgrt-wordpress') ?></label>
                    <label class="is-last"><input type="radio" name="sync.force.priority" value="1" /><?php _e('sync.force.priority.platform', 'mgrt-wordpress') ?></label>
                </div>
            <?php endif; ?>
            </div>
            <div id="sync_counter" style="display: none">
                <div id="first-pass" class="counter counter-lg">
                    <span data-from="1">0</span>
                    <?php _e('sync.force.'.($sync_direction == 'both' ? 'first' : 'step'), 'mgrt-wordpress') ?>
                    <div class="progressbar">
                        <div class="progress" style="background-color: #<?php echo $account->getHeaderBackgroundColor() ?>"></div>
                    </div>
                </div>
            <?php if ($sync_direction == 'both'): ?>
                <div id="second-pass" class="counter counter-lg is-disabled">
                    <span data-from="1">0</span>
                    <?php _e('sync.force.second', 'mgrt-wordpress') ?>
                    <div class="progressbar">
                        <div class="progress" style="background-color: #<?php echo $account->getHeaderBackgroundColor() ?>"></div>
                    </div>
                </div>
            <?php endif; ?>
                <div class="sync-wizard-message">
                    <p><?php _e('sync.force.work.line1', 'mgrt-wordpress') ?></p>
                    <p><?php _e('sync.force.work.line2', 'mgrt-wordpress') ?></p>
                </div>
            </div>
            <div id="sync_done" style="display: none">
                <div class="counter counter-lg">
                    <?php _e('sync.force.work.done.count', 'mgrt-wordpress') ?>
                    <span><strong id="done_counter_count"></strong></span>
                </div>
                <div class="sync-wizard-message">
                    <p><?php _e('sync.force.work.done.text', 'mgrt-wordpress') ?></p>
                </div>
            </div>
        </div>
        <div class="sync-error alert alert-danger" style="display: none">
            <h2 class="text-warning"><?php _e('sync.force.failure', 'mgrt-wordpress') ?></h2>
            <p class="sync-error-message"><?php _e('sync.force.failure.msg', 'mgrt-wordpress') ?></p>
            <pre class="sync-error-detail"></pre>
        </div>
        <script type="text/javascript">
            var _end_sync_trans = "<?php _e('sync.force.leave.warning', 'mgrt-wordpress') ?>";
            var _error_sync_trans = "<?php _e('sync.force.failure', 'mgrt-wordpress') ?>";
        </script>
        <?php
        endif;
    }

    public function getViewKey()
    {
        return MGRT__OPTION_KEY.'-force-sync';
    }

    public function getViewName()
    {
        return 'ForceSync';
    }


}
