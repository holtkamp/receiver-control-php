<?php

namespace Commands;

class PowerCommand extends CommandAbstract
{
    public function powerOn()
    {
        return $this->onOff('PO', 'PWR0');
    }

    private function onOff($command, $status)
    {
        $response = $this->getPowerStatus();

        while (strpos($response->getResponseText(), $status) === false) {
            $commandString = $command;
            $response = $this->executeCommand($commandString);
        }

        return new Response(true, $response->getResponseText());
    }

    private function getPowerStatus()
    {
        $delay = 0;
        do {
            sleep($delay);
            $commandString = '?P';
            $response = $this->executeCommand($commandString);
            $delay = 1;
        } while (strpos($response->getResponseText(), 'PWR') === false);

        return $response;
    }

    public function powerOff()
    {
        return $this->onOff('PF', 'PWR1');
    }

    public function powerStatus()
    {
        $response = $this->getPowerStatus();

        return new Response(true, $response->getResponseText());
    }
}