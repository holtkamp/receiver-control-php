<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Source;

use InvalidArgumentException;
use ReceiverControl\Command;
use ReceiverControl\Command\Response;
use function file_get_contents;
use function is_string;
use function sprintf;

class Select implements Command
{
    private const SOURCE_BLU_RAY      = 'BD';
    private const SOURCE_MEDIA_PLAYER = 'MPLAY';
    private const SOURCE_TUNER        = 'TUNER';

    public function invoke(int $zoneNumber, string $sourceInput = null) : Response
    {
        if (is_string($sourceInput)) {
            return $this->invokeHttpGet($zoneNumber, $sourceInput);
        }

        throw new InvalidArgumentException('Expected a SourceInput parameter of type string');
    }

    private function invokeHttpGet(int $zoneNumber, string $sourceInput) : Response
    {
        $url  = sprintf('http://%s/goform/formiPhoneAppDirect.xml?SI%s', 'denon', $sourceInput);
        $data = file_get_contents($url);
        if (is_string($data)) {
            return new Response(true, $zoneNumber, $data, $url);
        }

        return new Response(true, $zoneNumber, 'Failed to invoke ' . $url);
    }
}
