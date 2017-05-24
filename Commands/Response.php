<?php

namespace Commands;

class Response
{
    public $valid;
    public $message;

    public function __construct($valid, $message)
    {
        $this->valid = $valid;
        $this->message = $message;
    }

    public function getJSON()
    {
        return json_encode([
            "valid"   => $this->valid,
            "message" => $this->message,
        ]);
    }

}