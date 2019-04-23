<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Device;

use ReceiverControl\Command;
use ReceiverControl\Command\Response;
use function file_get_contents;
use function is_string;
use function sprintf;

class Info implements Command
{

    public function invoke(int $zoneNumber) : Response
    {
        return $this->invokeHttpGet($zoneNumber);
    }

    private function invokeHttpGet(int $zoneNumber) : Response
    {
        $url  = sprintf('http://%s/goform/Deviceinfo.xml', 'denon');
        $data = file_get_contents($url);
        if (is_string($data)) {
            return new Response(true, $zoneNumber, $data, $url);
        }

        return new Response(true, $zoneNumber, 'Failed to invoke ' . $url);
    }
}
