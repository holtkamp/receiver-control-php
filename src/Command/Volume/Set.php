<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use function file_get_contents;
use function is_string;
use function sprintf;
use function str_replace;

final class Set
{
    use ZoneNumberAware;

    private const MASTER_VOLUME_SET = 'MV';
    private const ZONE2_VOLUME_SET  = 'Z2';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getZoneNumber($request);
        $volume     = $this->getVolume($request);

        $response->getBody()->write($this->invoke($zoneNumber, $volume)->getJSON());

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function getVolume(ServerRequestInterface $request) : float
    {
        $parameters = $_POST; //TODO: why not available in $request->getParsedBody() ?
        if (array_key_exists('volume', $parameters)) {
            return (float) $parameters['volume'];
        }

        return 10.0;
    }

    private function invoke(int $zoneNumber, float $volume) : ResponseBody
    {
        return $this->invokeHttpGet($zoneNumber, $volume);
    }

    private function invokeHttpGet(int $zoneNumber, float $volume) : ResponseBody
    {
        $flattenedVolume = $this->flattenVolume($volume);
        $url             = sprintf(
            'http://%s/goform/formiPhoneAppDirect.xml?%s%s',
            'denon',
            $zoneNumber === 1 ? self::MASTER_VOLUME_SET : self::ZONE2_VOLUME_SET,
            $flattenedVolume
        );

        $data = file_get_contents($url);
        if (is_string($data)) {
            if ($data === '') {
                return new ResponseBody(true, $zoneNumber, $volume); //We 'assume' the volume has been set, we could consider requesting the status, but this would involve another HTTP request
            }

            return new ResponseBody(true, $zoneNumber, $data);
        }

        return new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    /**
     * The receiver has a 'strange' way of representing floats:.
     *
     * Examples:
     *  - 1.0   => 01
     *  - 1.5   => 015
     *  - 10    => 10
     *  - 10.5  => 105
     *  - 30    => 30
     *  - 30.5  => 305
     */
    private function flattenVolume(float $volume) : string
    {
        $flattenedVolume = (string) $volume;
        if ($volume < 10) {
            $flattenedVolume = '0' . $flattenedVolume;
        }

        return str_replace('.', '', $flattenedVolume);
    }
}
