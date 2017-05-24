<?php

namespace Commands;

class FunctionCommand extends CommandAbstract
{
    public function functionUp()
    {
        $commandString = 'FU';
        $this->executeCommand($commandString);

        return $this->functionStatus();
    }

    public function functionStatus()
    {
        $number = $this->currentFunctionNumber();

        return new Response(true, $number . ' - ' . $this->translateFunctionNumber($number));
    }

    private function currentFunctionNumber()
    {
        $commandString = '?F';
        $response = $this->executeCommand($commandString);

        return substr($response->getResponseText(), 2);
    }

    private function translateFunctionNumber($functionNumber)
    {
        $commandString = '?RGB' . $functionNumber;
        $response = $this->executeCommand($commandString);

        return substr($response->getResponseText(), 6);
    }

    public function functionDown()
    {
        $commandString = 'FD';
        $this->executeCommand($commandString);

        return $this->functionStatus();
    }

    public function functionSet($functionNumber)
    {
        $commandString = $functionNumber . 'FN';
        $this->executeCommand($commandString);

        return $this->functionStatus();
    }
}