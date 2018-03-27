<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use ReceiverControl\Command;
use ReceiverControl\Command\FunctionCommand;
use ReceiverControl\Command\Power\Off as PowerOffCommand;
use ReceiverControl\Command\Power\On as PowerOnCommand;
use ReceiverControl\Command\Response;
use ReceiverControl\Command\Volume\Down as VolumeDownCommand;
use ReceiverControl\Command\Volume\Get as GetVolumeCommand;
use ReceiverControl\Command\Volume\Mute as MuteVolumeCommand;
use ReceiverControl\Command\Volume\Set as SetVolumeCommand;
use ReceiverControl\Command\Volume\Up as VolumeUpCommand;

$denonUrl = 'denon';

function getZoneNumber(array $postData = null): int
{
    return \is_array($postData) ? (int) ($postData['zoneNumber'] ?? 1) : 1;
}

function getCommandName(array $postData = null): ?string
{
    return \is_array($postData) ? $postData['command'] ?? null : null;
}

function getCommand(array $postData = null): ?Command
{
    $supportedCommands = [
        PowerOnCommand::class,
        PowerOffCommand::class,
        VolumeUpCommand::class,
        VolumeDownCommand::class,
        MuteVolumeCommand::class,
    ];

    if ($commandName = getCommandName($postData)) {
        if (\in_array($commandName, $supportedCommands, true)) {
            return new $commandName();
        }
    }

    return null;
}

if ($command = getCommand($_POST ?? null)) {
    $response = $command->invoke(getZoneNumber($_POST ?? null));

    echo $response->getJSON();

    return;
}

switch ($_POST['command']) {
    case 'volumeStatus':
    case GetVolumeCommand::ALIAS:
        $command = new GetVolumeCommand();
        $response = $command->invoke();
        break;
    case SetVolumeCommand::ALIAS:
        $command = new SetVolumeCommand();
        $response = $command->invoke(1, (float) $_POST['data']);
        break;
    case 'functionStatus':
        $model = new FunctionCommand();
        $response = $model->functionStatus();
        break;
    case 'functionUp':
        $model = new FunctionCommand();
        $response = $model->functionUp();
        break;
    case 'functionDown':
        $model = new FunctionCommand();
        $response = $model->functionDown();
        break;
    case 'functionSet':
        $model = new FunctionCommand();
        $response = $model->functionSet($_POST['data']);
        break;
    default:
        $response = new Response(false, 'invalid command', \print_r($_POST, true));
        break;
}

/*
 * http://<AV IP-Adresse>/goform/formMainZone_MainZoneXml.xml
 * http://<AV IP-Adresse>/goform/formMainZone_MainZoneXmlStatus.xml
 * http://<AV IP-Adresse>/goform/formMainZone_MainZoneXmlStatusLite.xml
 * http://<AV IP-Adresse>/NetAudio/art.asp-jpg
 * http://<AV IP-Adresse>/img/album%20art_S.png
 *
 * http://denon/goform/formMainZone_MainZoneXml.xml
 * http://denon/goform/formMainZone_MainZoneXmlStatus.xml
 * http://denon/goform/formMainZone_MainZoneXmlStatusLite.xml
 * http://denon/NetAudio/art.asp-jpg
 * http://denon/img/album%20art_S.png
 */

echo $response->getJSON();
