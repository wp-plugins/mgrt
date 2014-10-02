<?php

namespace Mgrt\Wordpress\Widget;

use Mgrt\Model\MailingList;
use Mgrt\Wordpress\Bootstrap;

class NewsletterWidget extends \WP_Widget
{
    private $bootstrap;

    public function __construct()
    {
        $this->bootstrap = Bootstrap::getInstance();

        parent::__construct(
            'mgrt_newsletter',
            __('widget.newsletter.title', 'mgrt-wordpress'),
            array(
                'description' => __('widget.newsletter.description', 'mgrt-wordpress')
            )
        );
    }

    function widget($args, $instance)
    {
        if (!$this->bootstrap->getDataManager()->hasKeys()) {
            return;
        }

        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        if (isset($_POST[$args['widget_id'].'-id']) && $_POST[$args['widget_id'].'-id'] == $instance['id'] && !empty($_POST[$args['widget_id'].'-email'])) {
            echo $this->registerEmail($_POST[$args['widget_id'].'-email'], $instance['lists']);
        } else {
        ?>
        <form method="post" class="mgrt-widget-register">
            <input type="hidden" name="<?php echo $args['widget_id'] ?>-id" value="<?php echo $instance['id'] ?>" />
            <input type="email" name="<?php echo $args['widget_id'] ?>-email" placeholder="<?php _e('E-mail') ?>" />
            <button type="submit"><?php _e('shortcode.newsletter.submit', 'mgrt-wordpress') ?></button>
        </form>
        <?php
        }

        echo $args['after_widget'];
    }

    private function registerEmail($email, $targets)
    {
        $mailingLists = array();
        foreach ($targets as $targetId) {
            $ml = new MailingList();
            $ml->setId($targetId);
            $mailingLists[] = $ml;
        }

        $result = $this->bootstrap->getSyncManager()->getExportExecutor()->submitEmail($email, $mailingLists);
        if ($result) {
            return '<p>' . __('shortcode.newsletter.registration.success', 'mgrt-wordpress') . '</p>';
        } else {
            return '<p>' . __('shortcode.newsletter.registration.failure', 'mgrt-wordpress') . '</p>';
        }
    }

    function update($new_instance, $old_instance)
    {
        $lists = array();

        foreach ($new_instance['lists'] as $key => $value) {
            if (is_numeric($value) && $value > 0) {
                $lists[] = (int) $value;
            }
        }


        if (isset($new_instance['title'])) {
            $title = $new_instance['title'];
        } else {
            $title = __('widget.newsletter.title', 'mgrt-wordpress');
        }

        if (!isset($old_instance['id'])) {
            $id = uniqid();
        } else {
            $id = $old_instance['id'];
        }

        return compact('lists', 'title', 'id');
    }

    function form($instance)
    {
        if (!$this->bootstrap->getDataManager()->hasKeys()) {
            echo '<p>'.__('plugin.keys.missing', 'mgrt-wordpress').'</p>';
            return;
        }
        try {
            $lists = $this->bootstrap->getDataManager()->makeCachedApiCall('getMailingLists');
        } catch (ClientErrorResponseException $e) {
            echo 'Error: '.$e->getMessage();

            return;
        }

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('widget.newsletter.title', 'mgrt-wordpress');
        }

        if (!isset($instance['lists'])) {
            $instance['lists'] = array();
        }
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <h4><?php _e('wizard.newsletter.lists', 'mgrt-wordpress') ?></h4>
        <div class="checkbox-list" id="selected-lists">
        <?php foreach ($lists as $list): ?>
            <div>
                <label><input name="<?php echo $this->get_field_name('lists'); ?>[]" type="checkbox" value="<?php echo $list->getId() ?>"<?php echo in_array($list->getId(), $instance['lists']) ? ' checked="checked"' : '' ?> /><?php echo $list->getName() ?></label>
            </div>
        <?php endforeach; ?>
        </div>
        <p class="description"><?php _e('wizard.newsletter.lists.help', 'mgrt-wordpress') ?></p>
        <?php
    }
}
