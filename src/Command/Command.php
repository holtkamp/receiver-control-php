<?php

declare(strict_types=1);

namespace ReceiverControl\Command;

use Assert\Assertion;
use Graze\TelnetClient\TelnetClient;
use Graze\TelnetClient\TelnetClientInterface;
use Graze\TelnetClient\TelnetResponse;
use Graze\TelnetClient\TelnetResponseInterface;

class Command
{
    /**
     * @var TelnetClientInterface
     */
    private $socket;

    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $prompt;

    /**
     * @var string
     */
    private $errorPrompt;

    /**
     * @var string
     */
    private $lineEnding;

    public function __construct($configFile = 'config.ini', TelnetClientInterface $socket = null)
    {
        Assertion::file($configFile);
        $this->socket = $socket ?? TelnetClient::factory();

        $settings = \parse_ini_file($configFile, true, INI_SCANNER_RAW);
        $this->dsn = $settings['dsn'];
        $this->prompt = \stripcslashes($settings['prompt']);
        $this->errorPrompt = \stripcslashes($settings['errorPrompt']);
        $this->lineEnding = \stripcslashes($settings['lineEnding']);
    }

    public function __destruct()
    {
        if ($this->socket instanceof TelnetClientInterface) {
            unset($this->socket);
        }
    }

    protected function executeCommand($command): TelnetResponseInterface
    {
        try {
            $this->socket->connect($this->dsn, $this->prompt, $this->errorPrompt, $this->lineEnding);
            $response = $this->socket->execute($command, $this->prompt);
            //$this->socket->getSocket()->close();

            return $response;
        } catch (\Exception $exception) {
            return new TelnetResponse(true, __FILE__.':'.$exception->getMessage().$exception->getTraceAsString(), []);
        }
    }

    protected function TMPexecuteCommand($command): TelnetResponseInterface
    {
        $fp = \fsockopen('192.168.88.143', 23, $errorNumber, $errorString, 5);
        if (!$fp) {
            //echo "$errorString ($errorNumber)<br />\n";
        } else {
            //echo "connected, will send Command $command";
            //\print_r(\socket_get_status($fp));

            \fwrite($fp, $command."\r");

            $response = '';
            do {
                $response .= \fread($fp, 1000);
                $socketStatus = \socket_get_status($fp);
            } while ($socketStatus['unread_bytes']);

            \print_r($response);
            \fclose($fp); //Will be closed later
            exit;

            return new TelnetResponse(false, $response, []);
        }

        return new TelnetResponse(true, '', []);
    }
}
