<?php

namespace mjordan\irc;

use mjordan\Irc\TestServer\TestServer;

class SolrTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->server = new TestServer();
        $this->client_defaults = array(
            'base_uri' => 'http://127.0.0.1:8001/islandora/rest/v1/',
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
