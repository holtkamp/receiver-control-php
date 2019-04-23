<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use DOMDocument;
use DOMXPath;
use ReceiverControl\Command;
use ReceiverControl\Command\Response;
use RuntimeException;
use function sprintf;

class Mute implements Command
{
    private const PARAMETER_MUTE_ON = 'MuteOn';

    /** @var string */
    private $xPathQuery = '/item/Mute/value';

    public function invoke(int $zoneNumber) : Response
    {
        return $this->invokeWithDomDocument($zoneNumber);
    }

    private function invokeWithDomDocument(int $zoneNumber) : Response
    {
        $url = sprintf('http://%s/goform/formiPhoneAppMute.xml?%d+%s', 'denon', $zoneNumber, self::PARAMETER_MUTE_ON);

        $dom    = new DOMDocument();
        $result = $dom->load($url);

        return $result === true
            ? new Response(true, $zoneNumber, $this->isMuted($this->getMuteStatusFromDom($dom)) ? 0 : null)
            : new Response(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    private function isMuted(string $muteState) : bool
    {
        return $muteState === 'on';
    }

    private function getMuteStatusFromDom(DOMDocument $dom) : string
    {
        $xpath = new DOMXPath($dom);
        $node  = $xpath->query($this->xPathQuery)->item(0);

        if ($node === null) {
            throw new RuntimeException('No node found');
        }
        return $node->nodeValue;
    }
}
