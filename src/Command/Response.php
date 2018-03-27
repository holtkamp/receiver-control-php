<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

class Response
{
    public $valid;
    public $message;
    private $debugMessage;

    public function __construct(bool $valid, $message, $debugMessage = null)
    {
        $this->valid = $valid;
        $this->message = $message;
        $this->debugMessage = $debugMessage;
    }

    public function getJSON(): string
    {
        return \json_encode([
            'valid' => $this->valid,
            'message' => $this->message,
            'debugMessage' => $this->debugMessage,
        ]);
    }
}
