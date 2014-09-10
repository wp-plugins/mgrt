<?php

namespace Mgrt\Response;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;
use Mgrt\Response\Response;

class ResultCollectionResponse extends Response implements ResponseClassInterface
{
    /**
     * {@inheritDoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        return self::parseResultCollection($command->getResponse()->getBody());
    }
}
