<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\Volume\Get as GetVolumeCommand;
use ReceiverControl\Command\ZoneNumberAware;
use ReceiverControl\Psr7\JsonAwareResponse;
use function file_get_contents;
use function is_string;
use function sprintf;
use function usleep;

final class Up
{
    use JsonAwareResponse;
    use ZoneNumberAware;

    private const MASTER_VOLUME_UP = 'MVUP';
    private const ZONE2_VOLUME_UP  = 'Z2UP';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getIndicatedZoneNumber($request);
        $response->getBody()->write($this->getResponseBody($zoneNumber)->getJSON());

        return $this->withJsonHeader($response);
    }

    private function getResponseBody(int $zoneNumber) : ResponseBody
    {
        $url  = sprintf(
            'http://%s/goform/formiPhoneAppDirect.xml?%s',
            'denon',
            $zoneNumber === 1 ? self::MASTER_VOLUME_UP : self::ZONE2_VOLUME_UP
        );
        $data = file_get_contents($url);
        if (is_string($data)) {
            if ($data === '') {
                usleep($this->getMicroseconds(0.1)); //Introduce a minor delay, otherwise the set volume will not be returned by the Get command
                $command = new GetVolumeCommand();

                return $command->getResponseBody($zoneNumber);
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
