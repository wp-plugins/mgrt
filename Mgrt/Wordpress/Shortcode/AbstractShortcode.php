<?php

namespace Mgrt\Wordpress\Shortcode;

use Mgrt\Wordpress\AbstractBootstrapChild;
use Mgrt\Wordpress\Bootstrap;

abstract class AbstractShortcode extends AbstractBootstrapChild
{
    private $shortcode;
    private $tag = null;

    function __construct($shortcode)
    {
        parent::__construct(Bootstrap::getInstance());

        $this->shortcode = $shortcode;
        add_shortcode($shortcode, array($this, 'preparseShortCode'));
        add_action('wp_ajax_nopriv_'.$shortcode, array($this, 'ajaxCall'));
        add_action('wp_ajax_'.$shortcode, array($this, 'ajaxCall'));
    }

    public function getShortcodeTag()
    {
        return $this->shortcode;
    }

    public function preparseShortCode($attributes, $content = null)
    {
        if (empty($attributes['id'])) {
            $attributes['id'] = rand();
        }

        $id = $attributes['id'];
        unset($attributes['id']);

        return '<script type="text/javascript">var mgrt_ajax_url_'.$id.' = "'.admin_url( 'admin-ajax.php' ).'";</script>'.$this->handleShortCode($id, $attributes, $content);
    }

    public function ajaxCall()
    {
        if (!isset($_POST['id'])) {
            exit('MISSING ID');
        }

        $id = $_POST['id'];
        unset($_POST['id']);

        check_ajax_referer('_nonce_'.$this->shortcode.$id, 'security');
        echo json_encode($this->handleAjax($id, $_POST));
        exit;
    }

    protected function getNonce($id)
    {
        return wp_create_nonce('_nonce_'.$this->shortcode.$id);
    }

    protected function getNonceField($id)
    {
        return '<input type="hidden" name="security" value="'.$this->getNonce($id).'" />';
    }

    /**
     * Fired when displaying shortcode
     * @param string $id
     * @param array $attributes
     * @return string
     */
    abstract protected function handleShortCode($id, $attributes, $content = null);

    /**
     * Fired when an ajax call is made
     * @param array $data
     * @return string
     */
    abstract protected function handleAjax($id, $data);
}
