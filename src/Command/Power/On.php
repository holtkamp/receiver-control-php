<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Power;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use function file_get_contents;
use function is_string;
use function sprintf;

final class On
{
    use ZoneNumberAware;

    private const PARAMETER_POWER_ON = 'PowerOn';

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

    /**
     * TODO: XML is returned, we could/should parse it <item><Power><value>ON</value></Power></item>.
     */
    private function invokeHttpGet(int $zoneNumber) : ResponseBody
    {
        $url  = sprintf('http://%s/goform/formiPhoneAppPower.xml?%d+%s', 'denon', $zoneNumber, self::PARAMETER_POWER_ON);
        $data = file_get_contents($url);
        if (is_string($data)) {
            return new ResponseBody(true, $zoneNumber, $data, $url);
        }

        return new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }
}
