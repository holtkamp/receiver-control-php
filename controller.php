<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use ReceiverControl\Command;
use ReceiverControl\Command\Power\Off as PowerOffCommand;
use ReceiverControl\Command\Power\On as PowerOnCommand;
use ReceiverControl\Command\Response;
use ReceiverControl\Command\Volume\Down as VolumeDownCommand;
use ReceiverControl\Command\Volume\Get as GetVolumeCommand;
use ReceiverControl\Command\Volume\Mute as MuteVolumeCommand;
use ReceiverControl\Command\Volume\Set as SetVolumeCommand;
use ReceiverControl\Command\Volume\Up as VolumeUpCommand;

function getZoneNumber(array $postData = null): int
{
    return \is_array($postData) ? (int) ($postData['zoneNumber'] ?? 1) : 1;
}

function getCommandName(array $postData = null): ?string
{
    return \is_array($postData)
        ? $postData['command'] ?? $postData['commandOnClick'] ?? $postData['commandOnChange'] ?? null
        : null;
}

function getCommand(array $postData = null): ?Command
{
    $supportedCommands = [
        PowerOnCommand::class,
        PowerOffCommand::class,
        VolumeUpCommand::class,
        VolumeDownCommand::class,
        MuteVolumeCommand::class,
        SetVolumeCommand::class,
        GetVolumeCommand::class,
    ];

    if ($commandName = getCommandName($postData)) {
        if (\in_array($commandName, $supportedCommands, true)) {
            return new $commandName();
        }
    } else {
        \error_log('Unable to determine command name from posted data: '.\print_r($postData, true));
    }

    return null;
}

function getVolume(array $postData = null): float
{
    return \is_array($postData) ? (int) ($postData['volume'] ?? 10.0) : 10.0;
}

if ($command = getCommand($_POST ?? null)) {
    //TODO: how to support multiple parameters?
    if ($command instanceof SetVolumeCommand) {
        $response = $command->invoke(getZoneNumber($_POST ?? null), getVolume($_POST ?? null));
    } else {
        $response = $command->invoke(getZoneNumber($_POST ?? null));
    }
    echo $response->getJSON();

    return;
}

$response = new Response(false, 1, 'invalid command', \print_r($_POST, true));

echo $response->getJSON();
