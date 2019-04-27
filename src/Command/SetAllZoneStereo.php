<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function file_get_contents;
use function is_string;
use function sprintf;

final class SetAllZoneStereo
{
    use ZoneNumberAware;

    private const ALL_ZONE_STEREO = 'MN';

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
            self::ALL_ZONE_STEREO
        );
        $data = file_get_contents($url);
        if (is_string($data)) {
            return new ResponseBody(true, $zoneNumber, $data);
        }

        return new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }
}
