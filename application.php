<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReceiverControl\Container;
use Slim\Factory\AppFactory;

AppFactory::setContainer(new Container());
$application = AppFactory::create();
$application->get('/', function (Request $request, Response $response, $args): Response {
    $content = require __DIR__ . '/index.phtml';
    $response->getBody()->write($content);

    return $response;
});

$postCommands = [
    \ReceiverControl\Command\SetAllZoneStereo::class,
    \ReceiverControl\Command\Device\Info::class,
    \ReceiverControl\Command\Power\Off::class,
    \ReceiverControl\Command\Power\On::class,
    \ReceiverControl\Command\Source\Select::class,
    \ReceiverControl\Command\Volume\Down::class,
    \ReceiverControl\Command\Volume\Get::class,
    \ReceiverControl\Command\Volume\Mute::class,
    \ReceiverControl\Command\Volume\Set::class,
    \ReceiverControl\Command\Volume\Up::class,
];

foreach($postCommands as $postCommand){
    $path =  str_replace('\\', '/', $postCommand);
    $application->post('/' .$path, $postCommand);
}

$application->run();
