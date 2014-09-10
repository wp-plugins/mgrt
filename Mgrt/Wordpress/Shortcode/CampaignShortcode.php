<?php

namespace Mgrt\Wordpress\Shortcode;

use Mgrt\Model\MailingList;
use Mgrt\Wordpress\Bootstrap;

class CampaignShortcode extends AbstractShortcode
{
    const NAME = 'campaign';

    function __construct()
    {
        parent::__construct(self::NAME);
    }

    /**
     * {@inerhitDoc}
     */
    protected function handleShortCode($id, $attributes, $content = null)
    {
        if (!$this->getDataManager()->hasKeys()) {
            return '';
        }

        $limit = !empty($attributes['count']) && is_numeric($attributes['count']) ? (int) $attributes['count'] : 5;
        if ($limit < 1 || $limit > 15) {
            $limit = 5;
        }

        try {
            $campaigns = $this->getDataManager()->makeCachedApiCall('getCampaigns', array(
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
        $output = '';
        if (!empty($content)) {
            $output .= '<h3>'.$content.'</h3>';
        }
        $count = min($limit, $campaigns->getTotal());
        if ($count == 0) {
            $output .= '<p class="description">Pas de campagnes envoyés</p>';
        } elseif ($count == 1) {
            $output .= '<h4>La dernière campagne</h4>';
        } else {
            $output .= '<h4>Les ' . $count . ' dernières campagnes</h4>';
        }

        foreach ($campaigns as $campaign) {
            $output .= '<div class="campaign-item"><h6>'.$campaign->getName().'<a href="'.$campaign->getShareUrl().'" target="_blank">&rarr;</a></h6>';
            $output .= '<p class="campaign-subject">'.$campaign->getSubject().'</p>';
            $output .= '</div>';
        }

        return '<div class="mgrt-widget-box campaigns">'.$output.'</div>';
    }

    /**
     * {@inerhitDoc}
     */
    protected function handleAjax($id, $data)
    {
        return '';
    }
}
