<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use Graze\TelnetClient\TelnetResponseInterface;

class PowerCommand extends Command
{
    private const POWER_PREFIX = 'PW';
    private const POWER_ON = 'PWON';
    private const POWER_STANDBY = 'PWSTANDBY';
    private const POWER_STATUS = 'PW?';

    public function powerOn(): Response
    {
        return $this->onOff(self::POWER_ON, self::POWER_ON);
    }

    public function powerOff(): Response
    {
        return $this->onOff(self::POWER_STANDBY, self::POWER_STANDBY);
    }

    public function powerStatus(): Response
    {
        $response = $this->getPowerStatus();

        return new Response(true, $response->getResponseText());
    }

    private function onOff($command, $status): Response
    {
        $response = $this->getPowerStatus();

        while (\mb_strpos($response->getResponseText(), $status) === false) {
            $response = $this->executeCommand($command);
        }

        return new Response(true, $response->getResponseText());
    }

    private function getPowerStatus(): TelnetResponseInterface
    {
        $response = $this->executeCommand(self::POWER_STATUS);
        while (\mb_strpos($response->getResponseText(), self::POWER_PREFIX) === false) {
            \sleep(1);
            $response = $this->executeCommand(self::POWER_STATUS);
        }

        return $response;
    }
}
