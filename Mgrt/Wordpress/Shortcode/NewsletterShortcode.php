<?php

namespace Mgrt\Wordpress\Shortcode;

use Mgrt\Model\CustomField;
use Mgrt\Model\MailingList;
use Mgrt\Wordpress\Bootstrap;
use Mgrt\Wordpress\View\Profile;

class NewsletterShortcode extends AbstractShortcode
{
    const NAME = 'newsletter';

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

        $selectedFields = $this->sanitize($attributes['fields']);

        try {
            $customFields = $this->getDataManager()->makeCachedApiCall('getCustomFields');
        } catch (ClientErrorResponseException $e) {
            echo 'Error: '.$e->getMessage();

            return;
        }

        $output  = '<form method="post" class="shortcode-newsletter" id="'.$id.'">';
        $output .= '<input type="hidden" name="action" value="'.$this->getShortcodeTag().'" />';
        $output .= '<input type="hidden" name="id" value="'.$id .'" />';
        $output .= '<input type="hidden" name="targets" value="'.$attributes['targets'] .'" />';
        $output .= '<input type="hidden" name="fields" value="'.$attributes['fields'] .'" />';
        $output .= $this->getNonceField($id);
        $output .= '<h3>'.(!empty($content) ? $content : __('shortcode.newsletter.help', 'mgrt-wordpress')).'</h3>';
        if (!empty($selectedFields)) {
            $output .= '<label>'.__('E-mail');
        }
        $output .= '<input type="email" name="email" value="" placeholder="'.__('E-mail').'" required="required" />';
        if (!empty($selectedFields)) {
            $output .= '</label>';
        }

        if (!empty($selectedFields)) {
            foreach ($customFields as $key => $value) {
                if (in_array($value->getId(), $selectedFields)) {
                    $output .= '<label>'.$value->getName();
                    $output .= Profile::buildProfileFields(array(
                        'fieldKey' => 'field_value',
                        'key' => $value->getId(),
                        'field' => $value
                    ));
                    $output .= '</label>';
                }
            }
        }

        $output .= '<button type="submit">'.__('shortcode.newsletter.submit', 'mgrt-wordpress').'</button>';
        $output .= '</form>';

        $output .= '<script type="text/javascript">';
        $output .= '
jQuery(function() {
    jQuery("#'.$id.'").submit(function(e) {
    var $this = jQuery(this);
        e.preventDefault();
        jQuery.post(mgrt_ajax_url_'.$id.', $this.serialize(), function(x) {
            x = JSON.parse(x);
            console.log(x);
            if (x.success) {
                $this.slideUp().parent().append(x.message);
            } else {
                alert(x.message)
            }
        })
    })
})
        ';
        $output .= '</script>';

        return '<div class="mgrt-widget-box mgrt-widget-register">'.$output.'</div>';
    }

    /**
     * {@inerhitDoc}
     */
    protected function handleAjax($id, $data)
    {
        if (!$this->getDataManager()->hasKeys()) {
            return array('success' => false);
        }

        if (empty($data['targets']) || empty($data['field_value']) || empty($data['email'])) {
            return array('success' => false, 'message' => 'nofields');
        }

        $result = $this->registerEmail($data['email'], $this->sanitize($data['targets']), $data['field_value']);
        return array(
            'success' => $result,
            'message' => $result ? '<p>'.__('shortcode.newsletter.registration.success', 'mgrt-wordpress').'</p>' : '<p>'.__('shortcode.newsletter.registration.failure', 'mgrt-wordpress').'</p>'
        );
    }

    private function sanitize($ids)
    {
        // sanitize ids
        $targets = array();
        if (!empty($ids)) {
            $raw_targets = explode(',', $ids);
            foreach ($raw_targets as $value) {
                if (is_numeric($value)) {
                    $targets[] = (int) $value;
                }
            }
        }

        return $targets;
    }

    private function registerEmail($email, $targets, $fields)
    {
        $mailingLists = array();
        foreach ($targets as $targetId) {
            $ml = new MailingList();
            $ml->setId($targetId);
            $mailingLists[] = $ml;
        }

        $customFields = array();
        foreach ($fields as $fieldId => $fieldValue) {
            $cf = new CustomField();
            $cf
                ->setId($fieldId)
                ->setValue($fieldValue);
            $customFields[] = $cf;
        }

        return $this->getSyncManager()->getExportExecutor()->submitEmail($email, $mailingLists, $customFields, true);
    }
}
