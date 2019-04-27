<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Device;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReceiverControl\Command\ResponseBody;
use ReceiverControl\Command\ZoneNumberAware;
use ReceiverControl\Psr7\JsonAwareResponse;
use function file_get_contents;
use function is_string;
use function sprintf;

final class Info
{
    use JsonAwareResponse;
    use ZoneNumberAware;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $zoneNumber = $this->getIndicatedZoneNumber($request);
        $response->getBody()->write($this->getResponseBody($zoneNumber)->getJSON());

         return $this->withJsonHeader($response);
    }

    private function getResponseBody(int $zoneNumber) : ResponseBody
    {
        $url  = sprintf('http://%s/goform/Deviceinfo.xml', 'denon');
        $data = file_get_contents($url);
        if (is_string($data)) {
            return new ResponseBody(true, $zoneNumber, $data, $url);
        }

        return new ResponseBody(true, $zoneNumber, 'Failed to invoke ' . $url);
    }
}
