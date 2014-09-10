<?php

namespace Mgrt\Response;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;
use Mgrt\Response\Response;

class ResultDeletedResponse extends Response implements ResponseClassInterface
{
    /**
     * {@inheritDoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        return self::checkStatusCode($command->getResponse(), 204);
    }
}
