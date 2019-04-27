<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use DOMDocument;
use DOMXPath;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use RuntimeException;
use function sprintf;

final class Mute
{
    use ZoneNumberAware;

    private const PARAMETER_MUTE_ON = 'MuteOn';

    /** @var string */
    private $xPathQuery = '/item/Mute/value';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getZoneNumber($request);
        $response->getBody()->write($this->invoke($zoneNumber)->getJSON());

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function invoke(int $zoneNumber) : ResponseBody
    {
        return $this->invokeWithDomDocument($zoneNumber);
    }

    private function invokeWithDomDocument(int $zoneNumber) : ResponseBody
    {
        $url = sprintf('http://%s/goform/formiPhoneAppMute.xml?%d+%s', 'denon', $zoneNumber, self::PARAMETER_MUTE_ON);

        $dom    = new DOMDocument();
        $result = $dom->load($url);

        return $result === true
            ? new ResponseBody(true, $zoneNumber, $this->isMuted($this->getMuteStatusFromDom($dom)) ? 0 : null)
            : new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
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
