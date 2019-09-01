<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use DOMDocument;
use DOMXPath;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use ReceiverControl\Psr7\JsonAwareResponse;
use RuntimeException;
use function sprintf;

final class Get
{
    use JsonAwareResponse;
    use ZoneNumberAware;

    /**
     * @see https://denon.custhelp.com/app/answers/detail/a_id/136/~/relative-and-absolute-volume-ranges
     *
     * @var float
     */
    private $referenceVolume = 80;

    /** @var string */
    private $xPathQuery = '/item/MasterVolume/value';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getIndicatedZoneNumber($request);
        $response->getBody()->write($this->getResponseBody($zoneNumber)->getJSON());

        return $this->withJsonHeader($response);
    }

    public function getResponseBody(int $zoneNumber) : ResponseBody
    {
        $url    = $this->getUrl($zoneNumber);
        $dom    = new DOMDocument();
        $result = $dom->load($url);

        return $result === true
            ? new ResponseBody(true, $zoneNumber, $this->convertDecibelToRawVolume($this->getVolumeFromDOM($dom)))
            : new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }

    private function getUrl(int $zoneNumber) : string
    {
        return sprintf(
            'http://%s/goform/form%sXmlStatusLite.xml',
            'denon',
            $zoneNumber === 1 ? 'MainZone_MainZone' : 'Zone2_Zone2'
        );
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
