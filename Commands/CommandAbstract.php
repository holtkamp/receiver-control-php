<?php

namespace Commands;

use Graze\TelnetClient\TelnetClient;
use Graze\TelnetClient\TelnetClientInterface;

abstract class CommandAbstract
{
    protected $socket;
    protected $dsn;
    protected $prompt;
    protected $errorPrompt;
    protected $lineEnding;

    public function __construct($configFile = 'config.ini', TelnetClientInterface $socket = null)
    {
        if ($socket === null) {
            $socket = TelnetClient::factory();
        }
        $this->socket = $socket;
        if (!is_file($configFile)) {
            throw new \Exception('config file missing');
        }
        $settings = parse_ini_file($configFile, true, INI_SCANNER_RAW);
        $this->dsn = $settings['dsn'];
        $this->prompt = stripcslashes($settings['prompt']);
        $this->errorPrompt = stripcslashes($settings['errorPrompt']);
        $this->lineEnding = stripcslashes($settings['lineEnding']);
    }

    public function __destruct()
    {
        unset($this->socket);
    }

    protected function executeCommand($command)
    {
        $this->socket->connect($this->dsn, $this->prompt, $this->errorPrompt, $this->lineEnding);
        $response = $this->socket->execute($command, $this->prompt);
        $this->socket->getSocket()->close();

        return $response;
    }
}