<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use Psr\Http\Message\ServerRequestInterface;
use function array_key_exists;

trait ZoneNumberAware
{
    private function getZoneNumber(ServerRequestInterface $request) : int
    {
        $parameters = $_POST; //TODO: why not available in $request->getParsedBody() ?
        if (array_key_exists('zoneNumber', $parameters)) {
            return (int) $parameters['zoneNumber'];
        }

        return 1;
    }
}
