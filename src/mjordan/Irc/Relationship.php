<?php

namespace mjordan\Irc;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Islandora REST Client Relationship base class.
 */
class Relationship
{
    /**
     * @var GuzzleClient
     */
    private $clientDefaults;

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * @var bool
     */
    public $created = false;

    /**
     * @var bool
     */
    public $delete = false;

    /**
     * Constructor.
     */
    public function __construct($client_defaults)
    {
        $this->clientDefaults = $client_defaults;
        $this->client = new GuzzleClient($client_defaults);
    }

    /**
     * Retrieves a relationship via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object.
     * @param array $params
     *    An associative array containing the relationship's
     *    uri, predicate, object, and type, as documented in
     *    the Islandora REST module's README.
     *
     * @return object
     *    The Guzzle response.
     */
    public function read($pid, $params)
    {
        return $this->client->get('object/' . $pid . '/relationship/?');
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
        // The base_uri is not being set here automatically as a Guzzle
        // default. The headers are, however, and the base_uri is being
        // set in the object client.
        $response = $this->client->post($this->clientDefaults['base_uri'] . 'object/' . $pid . '/relationship', [
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

        if ($response->getStatusCode() == 201) {
            $this->created = true;
        }

        return $response;
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
    public function delete($pid, $params)
    {
        // The base_uri is not being set here automatically as a Guzzle
        // default. The headers are, however, and the base_uri is being
        // set in the object client.
        $response = $this->client->delete($this->clientDefaults['base_uri'] . 'object/' . $pid . '/relationship', [
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

        if ($response->getStatusCode() == 200) {
            $this->deleted = true;
        }

        return $response;
    }
}
