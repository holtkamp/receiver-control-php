<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use DOMDocument;
use DOMXPath;
use ReceiverControl\Command;
use ReceiverControl\Command\Response;

class Get implements Command
{
    /**
     * @see https://denon.custhelp.com/app/answers/detail/a_id/136/~/relative-and-absolute-volume-ranges
     *
     * @var float
     */
    private $referenceVolume = 80;

    /**
     * @var string
     */
    private $xPathQuery = '/item/MasterVolume/value';

    public function invoke(int $zoneNumber = 1): Response
    {
        return $this->invokeWithDomDocument($zoneNumber);
    }

    private function invokeWithDomDocument(int $zoneNumber): Response
    {
        $url = \sprintf('http://%s/goform/form%sXmlStatusLite.xml', 'denon',
        $zoneNumber === 1 ? 'MainZone_MainZone' : 'Zone2_Zone2'
        );

        $dom = new DOMDocument();
        if ($dom->load($url)) {
            \error_log($dom->saveXML());
            $volume = $this->getVolumeFromDOM($dom);

            return new Response(true, $zoneNumber, $this->convertDecibelToRawVolume($volume));
        }

        return new Response(true, $zoneNumber, 'Failed to invoke '.$url);
    }

    private function getVolumeFromDOM(DOMDocument $dom): float
    {
        $xpath = new DOMXPath($dom);
        $volumeNode = $xpath->query($this->xPathQuery)->item(0);

        return (float) $volumeNode->nodeValue;
    }

    private function convertDecibelToRawVolume(float $volume): float
    {
        return $volume + $this->referenceVolume;
    }
}
