<?php

namespace Mgrt\Wordpress\Manager;

use Mgrt\Wordpress\AbstractView;
use Mgrt\Wordpress\Bootstrap;
/**
 * Manage views and actions
 */
class ViewManager
{
    private $bootstrap;

    private $included = array();
    private $loaded = array();
    private $routes = array();

    private $current;

    function __construct(Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;

        $this->routes = array(
            MGRT__OPTION_KEY                => 'Start',
            MGRT__OPTION_KEY.'-keys'        => 'ApiKeys',
            MGRT__OPTION_KEY.'-force-sync'  => 'ForceSync'
        );

        $this->includeAllViews();
    }

    /**
     * Include a view
     * @param string $view
     */
    public function includeView($views)
    {
        if (is_string($views)) {
            $views = array($views);
        }

        foreach ($views as $view) {
            if (in_array($view, $this->included)) {
                return;
            }

            $path = MGRT__PLUGIN_DIR . 'Mgrt/Wordpress/View/'.$view;

            if (!file_exists($path.'.php')) {
                return;
            }

            include $path.'.php';

            $this->included[] = $view;
        }
    }

    /**
     * Include all views files
     */
    public function includeAllViews()
    {
        $this->includeView($this->listViews());
    }

    /**
     * List all views
     */
    public function listViews()
    {
        $path = MGRT__PLUGIN_DIR . 'Mgrt/Wordpress/View/';
        $views = array();
        foreach (scandir($path) as $view) {
            if (substr($view, -4) == '.php') {
                $views[] = substr($view, 0, strlen($view)-4);
            }
        }
        return $views;
    }

    /**
     * Initialize a view
     * @param string $view View name
     */
    public function initView($views)
    {
        if (is_string($views)) {
            $views = array($views);
        }

        foreach ($views as $view) {

            if (!in_array($view, $this->included)) {
                $this->includeView($view);
            }

            if (isset($this->loaded[$view])) {
                return;
            }

            $class = '\\Mgrt\Wordpress\\View\\'.$view;

            $object = new $class($this->bootstrap);


            // if (!is_subclass_of($object, 'Mgrt\Wordpress\AbstractView', false)) {
            //     return;
            // }

            $this->loaded[$view] = $object;
            $this->routes[$object->getViewKey()] = $object->getViewName();
        }
    }

    /**
     * Initialize all views
     */
    public function initAllViews()
    {
        $this->initView($this->listViews());
    }

    public function getViewKey($view)
    {
        $this->initView($view);
        if (isset($this->loaded[$view])) {
            return $this->loaded[$view]->getViewKey();
        }
    }

    public function getViewName($view)
    {
        $this->initView($view);
        if (isset($this->loaded[$view])) {
            return $this->loaded[$view]->getViewName();
        }
    }

    /**
     * Display a specific view/action
     * @param string $view View name
     * @param string $action action name
     */
    public function display($view = 'Start', $action = 'display', $redirected = false)
    {
        $this->initView($view);

        if (!$this->loaded[$view]->isReady()) {
            $this->display('Notice', 'loadError');
            return;
        }

        if (!$redirected) {
            echo '<div class="wrap">';
        }

        $callback = array(
            $this->loaded[$view],
            $action
        );

        method_exists($callback[0], $callback[1]) && is_callable($callback) && call_user_func($callback);
        if (!$redirected) {
            echo '</div>';
        }
    }

    public function redirect($view = 'Start', $action = 'display')
    {
        $this->display($view, $action, true);
    }

    /**
     * Build an url for a view
     * @param string $view View name
     * @param string $action action name
     */
    public function url($view = 'Start', $action = '')
    {
        if (!$this->bootstrap->getDataManager()->hasKeys() && $view != 'ApiKeys') {
            $view = 'ApiKeys';
            $action = '';
        }
        $page = MGRT__OPTION_KEY;
        foreach ($this->routes as $key => $value) {
            if ($value == $view) {
                $page = $key;
                break;
            }
        }

        $args = array('page' => $page);

        if (!empty($action)) {
            $args['action'] = $action;
        }

        return add_query_arg($args, admin_url('admin.php'));
    }

    /**
     * Route input from Wordpress
     */
    public function route()
    {
        if (isset($_GET['view'])) {
            $view = $_GET['view'];
        } elseif (isset($_GET['page']) && isset($this->routes[$_GET['page']])) {
            $view = $this->routes[$_GET['page']];
        }

        $action = isset($_GET['action']) ? $_GET['action'] : 'display';
        $this->display($view, $action);
    }

    public function sheduleNotice($key)
    {
        $opt = MGRT__OPTION_KEY.'-notice';
        if (!isset($_SESSION[$opt])) {
            $_SESSION[$opt] = array();
        }
        if (!in_array($key, $_SESSION[$opt])) {
            $_SESSION[$opt][] = $key;
        }
    }

    public function hasNotices()
    {
        $opt = MGRT__OPTION_KEY.'-notice';
        return !empty($_SESSION[$opt]);
    }

    public function displayNotices()
    {
        $opt = MGRT__OPTION_KEY.'-notice';
        if (empty($_SESSION[$opt])) {
            return;
        }

        foreach ($_SESSION[$opt] as $value) {
            $this->display('Notice', $value);
        }

        $_SESSION[$opt] = array();
    }
}
