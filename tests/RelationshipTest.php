<?php

namespace mjordan\irc;

use mjordan\Irc\TestServer\TestServer;

class RelationshipTest extends \PHPUnit\Framework\TestCase
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
        $rel = new \mjordan\Irc\Relationship($this->client_defaults);
        $response = $rel->read('test:pid', array('predicate' => 'hasModel', 'uri' => 'info:fedora/fedora-system:def/model#'));
        $response_body = (string) $response->getBody();
        $response_body = json_decode($response_body, true);

        $this->assertEquals('islandora:sp_basic_image', $response_body[0]['object']['value']);
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
