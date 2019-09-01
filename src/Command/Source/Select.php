<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Source;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use ReceiverControl\Psr7\JsonAwareResponse;
use RuntimeException;
use function file_get_contents;
use function is_array;
use function is_string;
use function sprintf;

final class Select
{
    use JsonAwareResponse;
    use ZoneNumberAware;

    private const SOURCE_BLU_RAY      = 'BD';
    private const SOURCE_MEDIA_PLAYER = 'MPLAY';
    private const SOURCE_TUNER        = 'TUNER';

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber  = $this->getIndicatedZoneNumber($request);
        $sourceInput = $this->getSourceInput($request);
        $response->getBody()->write($this->getResponseBody($zoneNumber, $sourceInput)->getJSON());

        return $this->withJsonHeader($response);
    }

    private function getSourceInput(ServerRequestInterface $request) : string
    {
        $parameters = $request->getParsedBody();
        if (is_array($parameters)) {
            return $parameters['sourceInput'];
        }

        throw new RuntimeException('Expected an array of POSTed parameters');
    }

    private function getResponseBody(int $zoneNumber, string $sourceInput) : ResponseBody
    {
        $url  = sprintf('http://%s/goform/formiPhoneAppDirect.xml?SI%s', 'denon', $sourceInput);
        $data = file_get_contents($url);

        return is_string($data)
            ? new ResponseBody(true, $zoneNumber, $data, $url)
            : new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }
}
