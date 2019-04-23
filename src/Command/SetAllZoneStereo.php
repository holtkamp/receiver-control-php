<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use ReceiverControl\Command;
use function file_get_contents;
use function is_string;
use function sprintf;

class SetAllZoneStereo implements Command
{
    private const ALL_ZONE_STEREO = 'MN';

    public function invoke(int $zoneNumber) : Response
    {
        return $this->invokeHttpGet($zoneNumber);
    }

    private function invokeHttpGet(int $zoneNumber) : Response
    {
        $url  = sprintf(
            'http://%s/goform/formiPhoneAppDirect.xml?%s',
            'denon',
            self::ALL_ZONE_STEREO
        );
        $data = file_get_contents($url);
        if (is_string($data)) {
            return new Response(true, $zoneNumber, $data);
        }

        return new Response(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    private function getMicroseconds(float $seconds) : int
    {
        return (int) $seconds * 1000000;
    }
}
