<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use Psr\Http\Message\ServerRequestInterface;
use function array_key_exists;

trait ZoneNumberAware
{
    private function getZoneNumber(ServerRequestInterface $request) : int
    {
        $parameters = $request->getParsedBody();
        if (is_array($parameters) && array_key_exists('zoneNumber', $parameters)) {
            return (int) $parameters['zoneNumber'];
        }

        return 1;
    }
}
