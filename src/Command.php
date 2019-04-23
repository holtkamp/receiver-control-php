<?php

declare(strict_types=1);

namespace ReceiverControl;

use ReceiverControl\Command\Response;

interface Command
{
    public function invoke(int $zoneNumber) : Response;
}
