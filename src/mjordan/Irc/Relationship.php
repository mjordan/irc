<?php

namespace mjordan\Irc;

use GuzzleHttp\Client as GuzzleClient;


/**
 * Islandora REST Client Relationship base class.
 */
class Relationship
{
    /**
     * Constructor
     */
    public function __construct($client_defaults)
    {
        $this->clientDefaults = $client_defaults;
        $this->client = new GuzzleClient($client_defaults);
    }

    public function create($pid, $params)
    {
        return $this->client->post('object/' . $pid . '/relationship', [
            'form_params' => [
                'uri' => $params['uri'],
                'predicate' => $params['predicate'],
                'object' => $params['object'],
                'type' => $params['type'],
            ],
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
    }
}
