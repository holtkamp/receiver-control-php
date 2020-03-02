<?php

declare(strict_types=1);

namespace ReceiverControl\Command\Source;

class Inputs
{
    /** @var array<string, string> */
    private static array $sourceInputs = [
        'BD' => 'Chromecast',
        'MPLAY' => 'MacMini',
        'TUNER' => 'Tuner',
        'TV' => 'TV',
    ];

    /**
     * @return array<string, string>
     */
    public static function getSourceInputs() : array
    {
        return self::$sourceInputs;
    }
}
