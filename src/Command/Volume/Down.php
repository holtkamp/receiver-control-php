<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use ReceiverControl\Command;
use ReceiverControl\Command\Response;
use ReceiverControl\Command\Volume\Get as GetVolumeCommand;
use function file_get_contents;
use function is_string;
use function sprintf;
use function usleep;

class Down implements Command
{
    private const MASTER_VOLUME_DOWN = 'MVDOWN';
    private const ZONE2_VOLUME_DOWN  = 'Z2DOWN';

    public function invoke(int $zoneNumber) : Response
    {
        return $this->invokeHttpGet($zoneNumber);
    }

    private function invokeHttpGet(int $zoneNumber) : Response
    {
        $url  = sprintf(
            'http://%s/goform/formiPhoneAppDirect.xml?%s',
            'denon',
            $zoneNumber === 1 ? self::MASTER_VOLUME_DOWN : self::ZONE2_VOLUME_DOWN
        );
        $data = file_get_contents($url);
        if (is_string($data)) {
            if ($data === '') {
                usleep($this->getMicroseconds(0.1)); //Introduce a minor delay, otherwise the set volume will not be returned by the Get command

                $command = new GetVolumeCommand();

                return $command->invoke($zoneNumber);
            }

            return new Response(true, $zoneNumber, $data);
        }

        return new Response(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    private function getMicroseconds(float $seconds) : int
    {
        return (int) $seconds * 1000000;
    }
}
