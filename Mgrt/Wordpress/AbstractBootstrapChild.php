<?php

namespace Mgrt\Wordpress;

use Mgrt\Wordpress\Bootstrap;

abstract class AbstractBootstrapChild
{
    private $bootstrap;

    function __construct(Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    protected function getViewManager()
    {
        return $this->bootstrap->getViewManager();
    }

    protected function getDataManager()
    {
        return $this->bootstrap->getDataManager();
    }

    protected function getSyncManager()
    {
        return $this->bootstrap->getSyncManager();
    }
}
