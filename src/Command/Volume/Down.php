<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\Volume\Get as GetVolumeCommand;
use ReceiverControl\Command\ZoneNumberAware;
use function file_get_contents;
use function is_string;
use function sprintf;
use function usleep;

final class Down
{
    use ZoneNumberAware;

    private const MASTER_VOLUME_DOWN = 'MVDOWN';
    private const ZONE2_VOLUME_DOWN  = 'Z2DOWN';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getZoneNumber($request);
        $response->getBody()->write($this->invoke($zoneNumber)->getJSON());

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function invoke(int $zoneNumber) : ResponseBody
    {
        return $this->invokeHttpGet($zoneNumber);
    }

    private function invokeHttpGet(int $zoneNumber) : ResponseBody
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

            return new ResponseBody(true, $zoneNumber, $data);
        }

        return new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    private function getMicroseconds(float $seconds) : int
    {
        return (int) $seconds * 1000000;
    }
}
