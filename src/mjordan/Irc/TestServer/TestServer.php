<?php

namespace mjordan\Irc\TestServer;

use Cocur\BackgroundProcess\BackgroundProcess;

/**
 * Islandora REST Client TestServer class.
 */
class TestServer
{
    /**
     * Constructor.
     */
    public function __construct($port = '8001')
    {
        $this->process = new BackgroundProcess('php -S localhost:' . $port . ' ' . __DIR__ . '/index.php');
        $this->process->run();
        $this->processId = $this->process->getPid();
        sleep(5);
    }

    public function __destruct()
    {
        if ($this->process->isRunning()) {
            $this->process->stop();
        }
    }
}
