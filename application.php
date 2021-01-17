<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$container   = include __DIR__ . '/container.php';
$application = AppFactory::create(null, $container);
$application->get('/',
    fn(Request $request, Response $response, $args): Response => $container->renderer->render($response, 'index.phtml', ['title' => 'Denon Controller'])
);

$postCommands = include __DIR__ . '/commands.php';
foreach ($postCommands as $postCommand) {
    $path = str_replace('\\', '/', $postCommand);
    $application->post('/' . $path, $postCommand);
}

$application->run();
