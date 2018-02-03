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

    public function testCreate()
    {
        $mods_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'TestMODSDocument.xml';
        $ds = new \mjordan\Irc\Datastream($this->client_defaults);
        $response = $ds->create('test:pid', 'MODS', $mods_path, array('label' => 'I am a new MODS document'));

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($ds->created);
    }

    public function testCreateMissingDsFile()
    {
        $this->setExpectedException(\mjordan\Irc\IslandoraRestClientException::class);
        $ds = new \mjordan\Irc\Datastream($this->client_defaults);
        $response = $ds->create('test:pid', 'MODS', '/foo/bar', array('xxx'));
    }

    public function testDelete()
    {
        $ds = new \mjordan\Irc\Datastream($this->client_defaults);
        $response = $ds->delete('test:pid', 'MODS');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($ds->deleted);
    }

    public function testUpdateProperties()
    {
        $ds = new \mjordan\Irc\Datastream($this->client_defaults);
        $response = $ds->update('test:pid', 'MODS', null, array('label' => 'xxx'));
        $response_body = (string) $response->getBody();
        $response_body = json_decode($response_body, true);

        $this->assertEquals('New MODS DS label', $response_body['label']);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($ds->propertiesUpdated);
    }

    public function _testUpdateContent()
    {
        $mods_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'TestMODSDocument.xml';
        $ds = new \mjordan\Irc\Datastream($this->client_defaults);
        $response = $ds->update('test:pid', 'MODS', $mods_path, array());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($ds->contentUpdated);
    }
}
