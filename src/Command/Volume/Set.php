<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use InvalidArgumentException;
use ReceiverControl\Command;
use ReceiverControl\Command\Response;
use function file_get_contents;
use function is_float;
use function is_string;
use function sprintf;
use function str_replace;

class Set implements Command
{
    private const MASTER_VOLUME_SET = 'MV';
    private const ZONE2_VOLUME_SET  = 'Z2';

    public function invoke(int $zoneNumber, float $volume = null) : Response
    {
        if (is_float($volume)) {
            return $this->invokeHttpGet($zoneNumber, $volume);
        }

        throw new InvalidArgumentException('Expected a volume parameter of type float');
    }


    private function invokeHttpGet(int $zoneNumber, float $volume) : Response
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
                return new Response(true, $zoneNumber, $volume); //We 'assume' the volume has been set, we could consider requesting the status, but this would involve another HTTP request
            }

            return new Response(true, $zoneNumber, $data);
        }

        return new Response(true, $zoneNumber, 'Failed to invoke ' . $url);
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
     *
     * @param float $volume
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
