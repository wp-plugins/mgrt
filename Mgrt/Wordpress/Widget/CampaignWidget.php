<?php

namespace Mgrt\Wordpress\Widget;

use Mgrt\Wordpress\Bootstrap;

class CampaignWidget extends \WP_Widget
{
    private $bootstrap;

    public function __construct()
    {
        $this->bootstrap = Bootstrap::getInstance();

        parent::__construct(
            'mgrt_campaign',
            __('widget.campaign.title', 'mgrt-wordpress'),
            array(
                'description' => __('widget.campaign.description', 'mgrt-wordpress')
            )
        );
    }

    function widget($args, $instance)
    {
        if (!$this->bootstrap->getDataManager()->hasKeys()) {
            return;
        }

        $limit = $instance['count'];

        try {
            $campaigns = $this->bootstrap->getDataManager()->makeCachedApiCall('getCampaigns', array(
                array(
                    'status'    => 'sent',
                    'page'      => 1,
                    'limit'     => $limit,
                    'sort'      => 'sentAt',
                    'direction' => 'asc',
                    'public'    => 1
                )
            ));
        } catch (ClientErrorResponseException $e) {
            echo 'Error: '.$e->getMessage();

            return;
        }

        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        foreach ($campaigns as $campaign):
        ?>
        <div class="campaign-item">
            <h6><a href="<?php echo $campaign->getShareUrl() ?>" target="_blank"><?php echo $campaign->getName() ?></a></h6>
            <p class="campaign-subject"><?php echo $campaign->getSubject() ?></p>
        </div>
        <?php
        endforeach;

        echo $args['after_widget'];
    }

    function update($new_instance, $old_instance)
    {
        $count = 5;

        if (is_numeric($new_instance['count'])) {
            $count = $new_instance['count'] < 1 && $new_instance['count'] > 15 ? 5 : (int) $new_instance['count'];
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

        return compact('count', 'title', 'id');
    }

    function form($instance)
    {
        if (!$this->bootstrap->getDataManager()->hasKeys()) {
            echo '<p>'.__('plugin.keys.missing', 'mgrt-wordpress').'</p>';
            return;
        }

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('widget.campaign.title', 'mgrt-wordpress');
        }

        if (!isset($instance['count'])) {
            $instance['count'] = 5;
        }
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('wizard.campaign.count', 'mgrt-wordpress') ?></h4>
            <input class="widefat" type="number" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php echo isset($instance['count']) ? ' value="'.$instance['count'].'"' : '' ?> />
        </label>
        <p class="description"><?php _e('wizard.campaign.count.help', 'mgrt-wordpress') ?></p>
        <?php
    }
}
