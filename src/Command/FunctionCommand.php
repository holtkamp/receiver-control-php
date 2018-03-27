<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

class FunctionCommand extends Command
{
    public function functionUp(): Response
    {
        $commandString = 'FU';
        $this->executeCommand($commandString);

        return $this->functionStatus();
    }

    public function functionStatus(): Response
    {
        $number = $this->currentFunctionNumber();

        return new Response(true, $number.' - '.$this->translateFunctionNumber($number));
    }

    public function functionDown(): Response
    {
        $commandString = 'FD';
        $this->executeCommand($commandString);

        return $this->functionStatus();
    }

    public function functionSet($functionNumber): Response
    {
        $commandString = $functionNumber.'FN';
        $this->executeCommand($commandString);

        return $this->functionStatus();
    }

    private function currentFunctionNumber(): string
    {
        $commandString = '?F';
        $response = $this->executeCommand($commandString);

        return \mb_substr($response->getResponseText(), 2);
    }

    private function translateFunctionNumber($functionNumber): string
    {
        $commandString = '?RGB'.$functionNumber;
        $response = $this->executeCommand($commandString);

        return \mb_substr($response->getResponseText(), 6);
    }
}
