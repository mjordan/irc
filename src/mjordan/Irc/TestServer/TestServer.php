<?php

namespace mjordan\Irc\TestServer;

use Cocur\BackgroundProcess\BackgroundProcess;

/**
 * Islandora REST Client Object base class.
 */
class TestServer
{
    /**
     * Constructor.
     */
    public function __construct($port = '8001')
    {
        $this->process = new BackgroundProcess('php -S localhost:' . $port);
        $this->process->run();
        $this->processId = $this->process->getPid();
    }

    public function __destruct()
    {
        if ($this->process->isRunning()) {
            $this->process->stop();
        }
    }

    public function shutdown()
    {
        if ($this->process->isRunning()) {
            $this->process->stop();
        }
    }
}
