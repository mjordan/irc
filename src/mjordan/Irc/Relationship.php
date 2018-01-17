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

    /**
     * Creates a new datastream via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object to attach to datastream to.
     * @param array $params
     *    An associative array containing the relationship's
     *    uri, predicate, object, and type, as documented in
     *    the Islandora REST module's README.
     *
     * @return object
     *    The Guzzle response.
     */
    public function create($pid, $params)
    {
        // For some reason, the base_uri is not being set here automatically.
        // The headers are, however, and the base_uri is being set in the
        // object client.
        return $this->client->post($this->clientDefaults['base_uri'] . 'object/' . $pid . '/relationship', [
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
