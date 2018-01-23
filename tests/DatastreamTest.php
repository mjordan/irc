<?php

namespace mjordan\irc;

use mjordan\Irc\TestServer\TestServer;

class DatastreamTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->server = new TestServer();
        $this->client_defaults = array(
            'base_uri' => 'http://localhost:8001/islandora/rest/v1/',
        );
    }

    public function testRead()
    {
        $ds = new \mjordan\Irc\Datastream($this->client_defaults);
        $response = $ds->read('test:pid', 'MODS', true);
        $response_body = (string) $response->getBody();

        $this->assertRegExp('/Test/', $response_body);
    }

    public function _testCreate()
    {
    }

    public function _testDelete()
    {
    }

    public function _testUpdate()
    {
    }
}
