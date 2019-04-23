<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use ReceiverControl\Command;
use ReceiverControl\Command\Response;
use ReceiverControl\Command\Volume\Get as GetVolumeCommand;
use function file_get_contents;
use function is_string;
use function mb_strlen;
use function sprintf;
use function usleep;

class Up implements Command
{
    private const MASTER_VOLUME_UP = 'MVUP';
    private const ZONE2_VOLUME_UP  = 'Z2UP';

    public function invoke(int $zoneNumber) : Response
    {
        return $this->invokeHttpGet($zoneNumber);
    }

    private function invokeHttpGet(int $zoneNumber) : Response
    {
        $url  = sprintf(
            'http://%s/goform/formiPhoneAppDirect.xml?%s',
            'denon',
            $zoneNumber === 1 ? self::MASTER_VOLUME_UP : self::ZONE2_VOLUME_UP
        );
        $data = file_get_contents($url);
        if (is_string($data)) {
            if (mb_strlen($data) === 0) {
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

    /**
     * curl http://denon/goform/AppCommand.xml -d '<?xml version="1.0" encoding="utf-8"?> <tx><cmd id="1">GetSourceStatus</cmd> </tx>'
     * curl http://denon/goform/AppCommand.xml -d '<?xml version="1.0" encoding="utf-8"?> <tx><cmd id="1">GetVolumeLevel</cmd> </tx>'
     * curl http://denon/goform/AppCommand.xml -d '<?xml version="1.0" encoding="utf-8"?> <tx><cmd id="1">GetMuteStatus</cmd> </tx>'.
     *
     * @return Response
     */
    private function invokeHttpPost() : Response
    {
        //not implemented, seems only be used to "read" status, not to set
    }
}
