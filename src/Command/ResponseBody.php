<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use function get_object_vars;
use function json_encode;

class ResponseBody
{
    public bool $valid;

    /** @var mixed */
    public $message;

    public int $zoneNumber;

    public ?string $debugMessage;

    /**
     * @param mixed $message
     */
    public function __construct(bool $valid, int $zoneNumber, $message, string $debugMessage = null)
    {
        $this->valid        = $valid;
        $this->zoneNumber   = $zoneNumber;
        $this->message      = $message;
        $this->debugMessage = $debugMessage;
    }

    public function getJSON() : string
    {
        return json_encode(get_object_vars($this), \JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray() : array
    {
        return get_object_vars($this);
    }
}
