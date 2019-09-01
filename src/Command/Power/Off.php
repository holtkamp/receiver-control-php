<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Power;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use ReceiverControl\Psr7\JsonAwareResponse;
use function file_get_contents;
use function is_string;
use function sprintf;

final class Off
{
    use JsonAwareResponse;
    use ZoneNumberAware;

    private const PARAMETER_POWER_OFF = 'PowerStandby';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getIndicatedZoneNumber($request);
        $response->getBody()->write($this->getResponseBody($zoneNumber)->getJSON());

        return $this->withJsonHeader($response);
    }

    /**
     * TODO: XML is returned, we could/should parse it <item><Power><value>OFF</value></Power></item>.
     */
    private function getResponseBody(int $zoneNumber) : ResponseBody
    {
        $url  = $this->getUrl($zoneNumber);
        $data = file_get_contents($url);

        return is_string($data)
            ? new ResponseBody(true, $zoneNumber, $data, $url)
            : new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    private function getUrl(int $zoneNumber) : string
    {
        return sprintf(
            'http://%s/goform/formiPhoneAppPower.xml?%d+%s',
            'denon',
            $zoneNumber,
            self::PARAMETER_POWER_OFF
        );
    }
}
