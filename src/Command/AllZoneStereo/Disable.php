<?php

declare(strict_types=1);

namespace ReceiverControl\Command\AllZoneStereo;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use ReceiverControl\Psr7\JsonAwareResponse;
use function file_get_contents;
use function is_string;
use function sprintf;
use function urlencode;

final class Disable
{
    use JsonAwareResponse;
    use ZoneNumberAware;

    private const ALL_ZONE_STEREO     = 'MN';
    private const ALL_ZONE_STEREO_OFF = 'ZST OFF';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getIndicatedZoneNumber($request);
        $response->getBody()->write($this->getResponseBody($zoneNumber)->getJSON());

        return $this->withJsonHeader($response);
    }

    private function getResponseBody(int $zoneNumber) : ResponseBody
    {
        $url  = $this->getUrlOff();
        $data = file_get_contents($url);

        return is_string($data)
            ? new ResponseBody(true, $zoneNumber, $url)
            : new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    private function getUrlOff() : string
    {
        return sprintf(
            'http://%s/goform/formiPhoneAppDirect.xml?%s%s',
            'denon',
            self::ALL_ZONE_STEREO,
            urlencode(self::ALL_ZONE_STEREO_OFF)
        );
    }
}
