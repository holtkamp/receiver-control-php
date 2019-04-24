<?php

declare(strict_types=1);

namespace ReceiverControl;

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
use function error_log;
use function in_array;
use function is_array;
use function print_r;

class Application
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

    public function run() : void
    {
        $command = $this->getCommand($_POST ?? null);
        if ($command === null) {
            $response = new Response(false, 1, 'invalid command', print_r($_POST, true));

            echo $response->getJSON();
            return;
        }
        $response = $this->executeCommand($command);
        echo $response->getJSON();
    }

    private function getCommand(array $postData = null) : ?Command
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

    private function getCommandName(array $postData = null) : ?string
    {
        return is_array($postData)
            ? $postData['command'] ?? $postData['commandOnClick'] ?? $postData['commandOnChange'] ?? null
            : null;
    }

    private function executeCommand(Command $command) : Response
    {
        //TODO: how to support multiple parameters?
        if ($command instanceof SetVolumeCommand) {
            return $command->invoke($this->getZoneNumber($_POST ?? null), $this->getVolume($_POST ?? null));
        }
        if ($command instanceof SelectSourceCommand) {
            return $command->invoke($this->getZoneNumber($_POST), $this->getSourceInput($_POST));
        }

        return $command->invoke($this->getZoneNumber($_POST));
    }

    private function getZoneNumber(array $postData) : int
    {
        if (\array_key_exists('zoneNumber', $postData)) {
            return (int) $postData['zoneNumber'];
        }

        return 1;
    }

    private function getVolume(array $postData) : float
    {
        if (\array_key_exists('volume', $postData)) {
            return (float) $postData['volume'];
        }

        return 10.0;
    }

    private function getSourceInput(array $postData = null) : string
    {
        return $postData['sourceInput'];
    }
}
