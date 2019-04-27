<?php

declare(strict_types=1);

namespace ReceiverControl\Psr7;

use Psr\Http\Message\ResponseInterface;

trait JsonAwareResponse
{
    public function withJsonHeader(ResponseInterface $response) : ResponseInterface
    {
        return $response->withHeader('Content-Type', 'application/json');
    }
}
