<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use Psr\Http\Message\ServerRequestInterface;
use function array_key_exists;

trait ZoneNumberAware
{
    private function getIndicatedZoneNumber(ServerRequestInterface $request) : int
    {
        $parameters = $request->getParsedBody();

        return is_array($parameters) && array_key_exists('zoneNumber', $parameters)
            ? (int) $parameters['zoneNumber']
            : 1;
    }
}
