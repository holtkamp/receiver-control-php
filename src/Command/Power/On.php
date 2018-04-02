<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Power;

use ReceiverControl\Command;
use ReceiverControl\Command\Response;

class On implements Command
{
    private const PARAMETER_POWER_ON = 'PowerOn';

    public function invoke(int $zoneNumber): Response
    {
        return $this->invokeHttpGet($zoneNumber);
    }

    /**
     * TODO: XML is returned, we could/should parse it <item><Power><value>ON</value></Power></item>.
     *
     * @param int $zoneNumber
     *
     * @return Response
     */
    private function invokeHttpGet(int $zoneNumber): Response
    {
        $url = \sprintf('http://%s/goform/formiPhoneAppPower.xml?%d+%s', 'denon', $zoneNumber, self::PARAMETER_POWER_ON);
        $data = \file_get_contents($url);
        if (\is_string($data)) {
            return new Response(true, $zoneNumber, $data, $url);
        }

        return new Response(true, $zoneNumber, 'Failed to invoke '.$url);
    }
}
