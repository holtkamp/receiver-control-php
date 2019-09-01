<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReceiverControl\Container;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

$container = new Container();
$renderer = new PhpRenderer(__DIR__ . '/src/templates');
$renderer->setLayout('layout.phtml');
$container->set('renderer', $renderer);

AppFactory::setContainer($container);
$application = AppFactory::create();
$application->get('/', function (Request $request, Response $response, $args): Response {
    return $this->renderer->render($response, 'index.phtml', ['title' => 'Denon Controller']);
});

$postCommands = [
    \ReceiverControl\Command\SetAllZoneStereoOn::class,
    \ReceiverControl\Command\SetAllZoneStereoOff::class,
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
