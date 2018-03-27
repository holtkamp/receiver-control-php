<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use ReceiverControl\Command\Response;
use ReceiverControl\Command\VolumeCommand;

class Set extends VolumeCommand
{
    public const ALIAS = 'volumeSet';

    private const MASTER_VOLUME_SET = 'MV';

    public function invoke(float $volume): Response
    {
        return $this->invokeHttpGet($volume);
    }

    private function invokeHttpGet(float $volume): Response
    {
        $flattenedVolume = $this->flattenVolume($volume);
        $url = \sprintf('http://%s/goform/formiPhoneAppDirect.xml?%s%s', 'denon', self::MASTER_VOLUME_SET, $flattenedVolume);

        $data = \file_get_contents($url);
        if (\is_string($data)) {
            if (\mb_strlen($data) === 0) {
                return new Response(true, $volume); //We 'assume' the volume has been set, we could consider requesting the status, but this would involve another HTTP request
            }

            return new Response(true, $data);
        }

        return new Response(true, 'Failed to invoke '.$url);
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
     *
     * @return string
     */
    private function flattenVolume(float $volume): string
    {
        $flattenedVolume = (string) $volume;
        if ($volume < 10) {
            $flattenedVolume = '0'.$flattenedVolume;
        }

        return \str_replace('.', '', $flattenedVolume);
    }
}
