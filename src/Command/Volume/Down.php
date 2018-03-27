<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use ReceiverControl\Command\Response;
use ReceiverControl\Command\Volume\Get as GetVolumeCommand;
use ReceiverControl\Command\VolumeCommand;

class Down extends VolumeCommand
{
    public const ALIAS = 'volumeDown';
    private const MASTER_VOLUME_DOWN = 'MVDOWN';
    private const ZONE2_VOLUME_DOWN = 'Z2DOWN';

    public function invoke(int $zoneNumber): Response
    {
        return $this->invokeHttpGet($zoneNumber);
    }

    private function invokeHttpGet(int $zoneNumber): Response
    {
        $url = \sprintf('http://%s/goform/formiPhoneAppDirect.xml?%s',
            'denon',
            $zoneNumber === 1 ? self::MASTER_VOLUME_DOWN : self::ZONE2_VOLUME_DOWN
        );
        $data = \file_get_contents($url);
        if (\is_string($data)) {
            if (\mb_strlen($data) === 0) {
                $command = new GetVolumeCommand();

                return $command->invoke($zoneNumber);
            }

            return new Response(true, $data);
        }

        return new Response(true, 'Failed to invoke '.$url);
    }
}
