<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use Graze\TelnetClient\TelnetResponseInterface;

class VolumeCommand extends Command
{
    private const MUTE_STATUS = 'MU?';
    private const MUTE_OFF = 'MUOFF';
    private const MUTE_ON = 'MUON';

    private const MASTER_VOLUME_STATUS = 'MV?';
    private const MASTER_VOLUME_UP = 'MVUP';
    private const MASTER_VOLUME_DOWN = 'MVDOWN';

    public function volumeUp(): Response
    {
        $response = $this->executeCommand(self::MASTER_VOLUME_UP);

        return $this->parseVolume($response);
    }

    public function volumeStatus(): Response
    {
        if ($this->muteStatus()) {
            return new Response(true, 'Mute');
        }

        $response = $this->executeCommand(self::MASTER_VOLUME_STATUS);

        return $this->parseVolume($response);
    }

    public function volumeDown(): Response
    {
        $response = $this->executeCommand(self::MASTER_VOLUME_DOWN);

        return $this->parseVolume($response);
    }

    public function volumeMute(): Response
    {
        $this->executeCommand(self::MUTE_ON);

        return $this->volumeStatus();
    }

    public function volumeSet($volume): Response
    {
        //if ($volume === null || (float)$volume < -80.0 || (float)$volume > 12.0) {
        //    $volume = -30.0;
        //}
        //$rawVolume = (int)(($volume + 80.5) * 2);

        //$commandString = str_pad($rawVolume, 3, '0', STR_PAD_LEFT) . 'VL';
        $commandString = \sprintf('MV%d', $volume);
        $this->executeCommand($commandString);

        return $this->volumeStatus();
    }

    protected function parseVolume(TelnetResponseInterface $response): Response
    {
        $responseText = $response->getResponseText();
        if (\is_numeric(\mb_substr($response->getResponseText(), 2))) {
            $rawVolume = \mb_substr($response->getResponseText(), 2);
            if (\mb_strlen($rawVolume) === 3) {
                $rawVolume = (int) $rawVolume / 10;
            }

            return new Response(true, (float) $rawVolume);
        }
        \error_log(__METHOD__.': '.$responseText);

        return new Response(false, 0);
    }

    private function muteStatus(): bool
    {
        $response = $this->executeCommand(self::MUTE_STATUS);

        \error_log(__METHOD__.': '.$response->getResponseText());

        return $response->getResponseText() === self::MUTE_OFF;
    }
}
