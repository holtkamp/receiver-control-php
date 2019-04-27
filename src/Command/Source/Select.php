<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Source;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use RuntimeException;
use function file_get_contents;
use function is_array;
use function is_string;
use function sprintf;

final class Select
{
    use ZoneNumberAware;

    private const SOURCE_BLU_RAY      = 'BD';
    private const SOURCE_MEDIA_PLAYER = 'MPLAY';
    private const SOURCE_TUNER        = 'TUNER';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber  = $this->getZoneNumber($request);
        $sourceInput = $this->getSourceInput($request);
        $response->getBody()->write($this->invoke($zoneNumber, $sourceInput)->getJSON());

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function getSourceInput(ServerRequestInterface $request) : string
    {
        $parameters = $request->getParsedBody();
        if (is_array($parameters)) {
            return $parameters['sourceInput'];
        }

        throw new RuntimeException('Expected an array of POSTed parameters');
    }

    private function invoke(int $zoneNumber, string $sourceInput = null) : ResponseBody
    {
        if (is_string($sourceInput)) {
            return $this->invokeHttpGet($zoneNumber, $sourceInput);
        }

        throw new InvalidArgumentException('Expected a SourceInput parameter of type string');
    }

    private function invokeHttpGet(int $zoneNumber, string $sourceInput) : ResponseBody
    {
        $url  = sprintf('http://%s/goform/formiPhoneAppDirect.xml?SI%s', 'denon', $sourceInput);
        $data = file_get_contents($url);
        if (is_string($data)) {
            return new ResponseBody(true, $zoneNumber, $data, $url);
        }

        return new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }
}
