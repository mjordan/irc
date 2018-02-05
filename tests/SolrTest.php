<?php

namespace mjordan\irc;

use mjordan\Irc\TestServer\TestServer;

class SolrTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        global $test_server_port;
        $this->server = new TestServer($test_server_port);
        $this->client_defaults = array(
            'base_uri' => 'http://localhost:8001/islandora/rest/v1/',
        );
    }

    public function testQuery()
    {
        $solr = new \mjordan\Irc\Solr($this->client_defaults);
        $response = $solr->query('xxxx');

        $this->assertEquals(4, $solr->numFound);
        $this->assertEquals(0, $solr->start);
        $this->assertEquals(4, count($solr->docs));
    }
}
