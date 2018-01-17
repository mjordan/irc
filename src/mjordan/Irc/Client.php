<?php

namespace mjordan\Irc;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Islandora REST Client base class.
 */
class Client
{
    /**
     * Constructor
     */
    public function __construct($defaults = array())
    {
        $defaults['http_errors'] = false;
        $this->guzzle = new GuzzleClient($defaults);
    }

    public function get($uri)
    {
        return $this->guzzle->request('GET', $uri);
    }
}
