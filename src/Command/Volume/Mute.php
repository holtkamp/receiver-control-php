<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use DOMDocument;
use DOMXPath;
use ReceiverControl\Command;
use ReceiverControl\Command\Response;

class Mute implements Command
{
    private const PARAMETER_MUTE_ON = 'MuteOn';

    private $xPathQuery = '/item/Mute/value';

    public function invoke(int $zoneNumber): Response
    {
        return $this->invokeWithDomDocument($zoneNumber);
    }

    private function invokeWithDomDocument(int $zoneNumber): Response
    {
        $url = \sprintf('http://%s/goform/formiPhoneAppMute.xml?%d+%s', 'denon', $zoneNumber, self::PARAMETER_MUTE_ON);

        $dom = new DOMDocument();
        if ($dom->load($url)) {
            $muteStatus = $this->getMuteStatusFromDom($dom);

            return new Response(true, $zoneNumber, $this->isMuted($muteStatus) ? 0 : null);
        }

        return new Response(true, $zoneNumber, 'Failed to invoke '.$url);
    }

    private function isMuted(string $muteState): bool
    {
        return $muteState === 'on';
    }

    private function getMuteStatusFromDom(DOMDocument $dom): string
    {
        $xpath = new DOMXPath($dom);
        $node = $xpath->query($this->xPathQuery)->item(0);

        return (string) $node->nodeValue;
    }
}
