<?php

namespace mjordan\irc;

use mjordan\Irc\TestServer\TestServer;

class ObjectTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->server = new TestServer();
        $this->client_defaults = array(
            'base_uri' => 'http://127.0.0.1:8001/islandora/rest/v1/',
        );
    }

    public function testRead()
    {
        $islandora_object = new \mjordan\Irc\Object($this->client_defaults);
        $response = $islandora_object->read('test:pid');
        $response_body = (string) $response->getBody();
        $response_body = json_decode($response_body, true);

        $this->assertEquals('fedoraAdmin', $response_body['owner']);
    }

    public function testCreate()
    {
        $islandora_object = new \mjordan\Irc\Object($this->client_defaults);
        $response = $islandora_object->create('test', 'admin', 'A label');

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($islandora_object->created);
    }

    public function testDelete()
    {
        $islandora_object = new \mjordan\Irc\Object($this->client_defaults);
        $response = $islandora_object->delete('islandora:test');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($islandora_object->deleted);
    }

    public function testUpdate()
    {
        $islandora_object = new \mjordan\Irc\Object($this->client_defaults);
        $response = $islandora_object->update('islandora:test', array());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($islandora_object->updated);
    }
}
