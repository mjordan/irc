<?php

namespace mjordan\irc;

use mjordan\Irc\TestServer\TestServer;

class ObjectTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp() {
        $this->server = new TestServer();
        $this->client_defaults = array(
            'base_uri' => 'http://localhost:8001/islandora/rest/v1/',
        );
    }

    public function testRead() {
        $islandora_object = new \mjordan\Irc\Object($this->client_defaults);
        $response = $islandora_object->read('test:pid');
    }
}
