<?php

namespace Mgrt\Wordpress;

use Mgrt\Wordpress\AbstractBootstrapChild;
use Mgrt\Wordpress\Bootstrap;

abstract class AbstractView extends AbstractBootstrapChild
{
    protected $ready = true;

    function __construct(Bootstrap $bootstrap)
    {
        parent::__construct($bootstrap);
    }

    public function isReady()
    {
        return $this->ready;
    }

    abstract public function getViewKey();

    abstract public function getViewName();
}
