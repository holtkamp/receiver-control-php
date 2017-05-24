<?php

namespace Commands;

class VolumeCommand extends CommandAbstract
{
    public function volumeUp()
    {
        $commandString = 'VU';
        $this->executeCommand($commandString);

        return $this->volumeStatus();
    }

    public function volumeStatus()
    {
        if ($this->muteStatus()) {
            return new Response(true, 'Mute');
        }

        $commandString = '?V';
        $response = $this->executeCommand($commandString);

        $rawVolume = substr($response->getResponseText(), 3);
        $volume = ((int)$rawVolume / 2) - 80.5;

        return new Response(true, $volume);
    }

    private function muteStatus()
    {
        $commandString = '?M';
        $response = $this->executeCommand($commandString);

        if ($response->getResponseText() === 'MUT0') {
            return true;
        }

        return false;
    }

    public function volumeDown()
    {
        $commandString = 'VD';
        $this->executeCommand($commandString);

        return $this->volumeStatus();
    }

    public function volumeMute()
    {
        $commandString = 'MZ';
        $this->executeCommand($commandString);

        return $this->volumeStatus();
    }

    public function volumeSet($volume)
    {
        if ($volume === null || (float)$volume < -80.0 || (float)$volume > 12.0) {
            $volume = -30.0;
        }
        $rawVolume = (int)(($volume + 80.5) * 2);

        $commandString = str_pad($rawVolume, 3, '0', STR_PAD_LEFT) . "VL";
        $this->executeCommand($commandString);

        return $this->volumeStatus();
    }
}