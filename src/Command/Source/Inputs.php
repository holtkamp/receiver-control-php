<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Source;

class Inputs
{
    /** @var array */
    private static $sourceInputs = [
        'BD' => 'Blu-Ray',
        'MPLAY' => 'Media Player',
        'TUNER' => 'Tuner',
    ];

    public static function getSourceInputs() : array
    {
        return self::$sourceInputs;
    }
}
