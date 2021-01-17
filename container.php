<?php
declare(strict_types=1);

use ReceiverControl\Container;
use Slim\Views\PhpRenderer;

$renderer = new PhpRenderer(__DIR__ . '/src/templates');
$renderer->setLayout('layout.phtml');

return new Container([
    'renderer' => $renderer,
]);
