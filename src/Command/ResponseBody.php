<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use function get_object_vars;
use function is_string;
use function json_encode;

class ResponseBody
{
    /** @var bool */
    public $valid;

    /** @var mixed */
    public $message;

    /** @var int */
    public $zoneNumber;

    /** @var ?string */
    public $debugMessage;

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
        $result = json_encode(get_object_vars($this));

        return is_string($result) ? $result : '';
    }

    public function toArray() : array
    {
        return get_object_vars($this);
    }
}
