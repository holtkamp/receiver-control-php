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
$application->post('/', \ReceiverControl\CommandController::class);

$application->run();
