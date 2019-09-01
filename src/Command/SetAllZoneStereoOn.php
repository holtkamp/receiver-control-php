<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Psr7\JsonAwareResponse;
use function file_get_contents;
use function is_string;
use function sprintf;
use function urlencode;

final class SetAllZoneStereoOn
{
    use JsonAwareResponse;
    use ZoneNumberAware;

    private const ALL_ZONE_STEREO    = 'MN';
    private const ALL_ZONE_STEREO_ON = 'ZST ON';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getIndicatedZoneNumber($request);
        $response->getBody()->write($this->getResponseBody($zoneNumber)->getJSON());

        return $this->withJsonHeader($response);
    }

    private function getResponseBody(int $zoneNumber) : ResponseBody
    {
        $url  = $this->getUrlOn();
        $data = file_get_contents($url);

        return is_string($data)
            ? new ResponseBody(true, $zoneNumber, $data)
            : new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    private function getUrlOn() : string
    {
        return sprintf(
            'http://%s/goform/formiPhoneAppDirect.xml?%s%s',
            'denon',
            self::ALL_ZONE_STEREO,
            urlencode(self::ALL_ZONE_STEREO_ON)
        );
    }
}
