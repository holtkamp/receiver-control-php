<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Volume;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use ReceiverControl\Psr7\JsonAwareResponse;
use RuntimeException;
use function sprintf;

final class Mute
{
    use JsonAwareResponse;
    use ZoneNumberAware;

    private const PARAMETER_MUTE_ON = 'MuteOn';

    /** @var string */
    private $xPathQuery = '/item/Mute/value';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getIndicatedZoneNumber($request);
        $response->getBody()->write($this->getResponseBody($zoneNumber)->getJSON());

        return $this->withJsonHeader($response);
    }

    private function getResponseBody(int $zoneNumber) : ResponseBody
    {
        $url    = $this->getUrl($zoneNumber);
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

    private function getUrl(int $zoneNumber) : string
    {
        return sprintf('http://%s/goform/formiPhoneAppMute.xml?%d+%s', 'denon', $zoneNumber, self::PARAMETER_MUTE_ON);
    }

    private function getMuteStatusFromDom(DOMDocument $dom) : string
    {
        $xpath  = new DOMXPath($dom);
        $result = $xpath->query($this->xPathQuery);
        assert($result instanceof DOMNodeList);
        $node = $result->item(0);

        if ($node === null) {
            throw new RuntimeException('No node found');
        }

        return $node->nodeValue;
    }
}
