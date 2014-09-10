<?php

namespace Mgrt\Response;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;
use Mgrt\Response\Response;

class PlainTextResponse extends Response implements ResponseClassInterface
{
    /**
     * {@inheritDoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        return json_decode($command->getResponse()->getBody(), true);
    }
}
