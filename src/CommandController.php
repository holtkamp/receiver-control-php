<?php

declare(strict_types=1);

namespace ReceiverControl;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReceiverControl\Command\Device\Info as DeviceInfoCommand;
use ReceiverControl\Command\Power\Off as PowerOffCommand;
use ReceiverControl\Command\Power\On as PowerOnCommand;
use ReceiverControl\Command\Response;
use ReceiverControl\Command\SetAllZoneStereo as SetAllZoneStereoCommand;
use ReceiverControl\Command\Source\Select as SelectSourceCommand;
use ReceiverControl\Command\Volume\Down as VolumeDownCommand;
use ReceiverControl\Command\Volume\Get as GetVolumeCommand;
use ReceiverControl\Command\Volume\Mute as MuteVolumeCommand;
use ReceiverControl\Command\Volume\Set as SetVolumeCommand;
use ReceiverControl\Command\Volume\Up as VolumeUpCommand;
use function array_key_exists;
use function error_log;
use function in_array;
use function print_r;

class CommandController
{
    /** @var array */
    private $supportedCommands = [
        DeviceInfoCommand::class,
        PowerOnCommand::class,
        PowerOffCommand::class,
        VolumeUpCommand::class,
        VolumeDownCommand::class,
        MuteVolumeCommand::class,
        SetVolumeCommand::class,
        GetVolumeCommand::class,
        SetAllZoneStereoCommand::class,
        SelectSourceCommand::class,
    ];

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $postData            = $_POST ?? [];
        $applicationResponse = $this->invokeCommand($this->getCommand($postData), $postData);

        $response->getBody()->write($applicationResponse->getJSON());

        return $response;
    }

    private function invokeCommand(?Command $command, array $postData) : Response
    {
        return $command === null
            ? new Response(false, 1, 'invalid command', print_r($postData, true))
            : $this->executeCommand($command, $postData);
    }

    private function getCommand(array $postData) : ?Command
    {
        $commandName = $this->getCommandName($postData);

        if ($commandName === null) {
            error_log('Unable to determine command name from posted data: ' . print_r($postData, true));
            return null;
        }
        if (in_array($commandName, $this->supportedCommands, true)) {
            return new $commandName();
        }

        error_log('Determined command name seems not supported: ' . $commandName);

        return null;
    }

    private function getCommandName(array $postData) : ?string
    {
        return $postData['command']
            ?? $postData['commandOnClick']
            ?? $postData['commandOnChange']
            ?? null;
    }

    private function executeCommand(Command $command, array $postData) : Response
    {
        //TODO: how to support multiple parameters?
        if ($command instanceof SetVolumeCommand) {
            return $command->invoke($this->getZoneNumber($postData), $this->getVolume($postData));
        }
        if ($command instanceof SelectSourceCommand) {
            return $command->invoke($this->getZoneNumber($postData), $this->getSourceInput($postData));
        }

        return $command->invoke($this->getZoneNumber($_POST));
    }

    private function getZoneNumber(array $postData) : int
    {
        if (array_key_exists('zoneNumber', $postData)) {
            return (int) $postData['zoneNumber'];
        }

        return 1;
    }

    private function getVolume(array $postData) : float
    {
        if (array_key_exists('volume', $postData)) {
            return (float) $postData['volume'];
        }

        return 10.0;
    }

    private function getSourceInput(array $postData) : string
    {
        return $postData['sourceInput'];
    }
}
