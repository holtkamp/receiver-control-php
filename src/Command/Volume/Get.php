<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use DOMDocument;
use DOMXPath;
use ReceiverControl\Command;
use ReceiverControl\Command\Response;
use RuntimeException;
use function sprintf;

class Get implements Command
{
    /**
     * @see https://denon.custhelp.com/app/answers/detail/a_id/136/~/relative-and-absolute-volume-ranges
     *
     * @var float
     */
    private $referenceVolume = 80;

    /** @var string */
    private $xPathQuery = '/item/MasterVolume/value';

    public function invoke(int $zoneNumber = 1) : Response
    {
        return $this->invokeWithDomDocument($zoneNumber);
    }

    private function invokeWithDomDocument(int $zoneNumber) : Response
    {
        $url = sprintf(
            'http://%s/goform/form%sXmlStatusLite.xml',
            'denon',
            $zoneNumber === 1 ? 'MainZone_MainZone' : 'Zone2_Zone2'
        );

        $dom    = new DOMDocument();
        $result = $dom->load($url);

        return $result === true
            ? new Response(true, $zoneNumber, $this->convertDecibelToRawVolume($this->getVolumeFromDOM($dom)))
            : new Response(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    private function convertDecibelToRawVolume(float $volume) : float
    {
        return $volume + $this->referenceVolume;
    }

    private function getVolumeFromDOM(DOMDocument $dom) : float
    {
        $xpath = new DOMXPath($dom);
        $node  = $xpath->query($this->xPathQuery)->item(0);

        if ($node === null) {
            throw new RuntimeException('No node found to determine the volume');
        }

        return (float) $node->nodeValue;
    }
}
