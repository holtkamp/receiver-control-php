<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

class Response
{
    public $valid;
    public $message;

    /**
     * @var int
     */
    public $zoneNumber;

    public $debugMessage;

    public function __construct(bool $valid, int $zoneNumber, $message, $debugMessage = null)
    {
        $this->valid = $valid;
        $this->zoneNumber = $zoneNumber;
        $this->message = $message;
        $this->debugMessage = $debugMessage;
    }

    public function getJSON(): string
    {
        return \json_encode(\get_object_vars($this));
    }
}
