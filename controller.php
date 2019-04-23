<?php

declare(strict_types=1);

use ReceiverControl\Application;

require __DIR__ . '/vendor/autoload.php';

$application = new Application();
$application->run();