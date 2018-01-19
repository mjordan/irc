<?php

namespace mjordan\Irc;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

/**
 * Islandora REST Client Relationship base class.
 */
class Relationship
{
    /**
     * @var array
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
        $client_defaults['http_errors'] = false;
        $this->clientDefaults = $client_defaults;
        try {
            $this->client = new GuzzleClient($client_defaults);
        } catch (RequestException $e) {
            $response = isset($response) ? $response : null;
            throw new IslandoraRestClientException($response, $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Retrieves a relationship via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object.
     * @param array $params
     *    An associative array containing the relationship's
     *    uri, predicate, object, and type, as documented in
     *    the Islandora REST module's README:
     *    -predicate (optional)
     *    -uri (required if predicate is present)
     *    -object (optional)
     *    -literal (true/false, optional)
     *
     * @return object
     *    The Guzzle response.
     */
    public function read($pid, $params = array())
    {
        $get_params = '';
        if (array_key_exists('predicate', $params) && array_key_exists('uri', $params)) {
            $get_params .= '?'. $params['predicate'] . '/' . $params['uri'];
        }
        if (array_key_exists('object', $params)) {
            $get_params .= $params['object'];
        }
        if (array_key_exists('literal', $params)) {
            $get_params .= $params['literal'];
        }

        try {
            $response = $this->client->get($this->clientDefaults['base_uri'] . 'object/' .
                $pid . '/relationship' . $get_params);
        } catch (RequestException $e) {
            $response = isset($response) ? $response : null;
            throw new IslandoraRestClientException($response, $e->getMessage(), $e->getCode(), $e);
        }

        return $response;
    }

    /**
     * Creates a new relationship via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object to attach to datastream to.
     * @param array $params
     *    An associative array containing the relationship's
     *    uri, predicate, object, and type, as documented in
     *    the Islandora REST module's README:
     *    -predicate: The predicate URI for the given predicate.
     *    -uri: The predicate of the relationship.
     *    -object: Object of the relationship.
     *    -literal: The type of the relationship object. Can be either 'uri',
     *        'string', 'int', 'date', 'none'. Defaults to 'uri'.
     *
     * @return object
     *    The Guzzle response.
     */
    public function create($pid, $params)
    {
        try {
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
        } catch (RequestException $e) {
            $response = isset($response) ? $response : null;
            throw new IslandoraRestClientException($response, $e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() == 201) {
            $this->created = true;
        }

        return $response;
    }

   /**
     * Deletes a relationship via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object to attach to datastream to.
     * @param array $params
     *    An associative array containing the relationship's
     *    uri, predicate, object, and type, as documented in
     *    the Islandora REST module's README:
     *    -predicate (optional)
     *    -uri (required if predicate is present)
     *    -object (optional)
     *    -literal (true/false, optional)
     *
     * @return object
     *    The Guzzle response.
     */
    public function delete($pid, $params)
    {
        try {
            // The base_uri is not being set here automatically as a Guzzle
            // default. The headers are, however, and the base_uri is being
            // set in the object client.
            $response = $this->client->delete($this->clientDefaults['base_uri'] . 'object/' . $pid . '/relationship', [
                'form_params' => $params,
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
        } catch (RequestException $e) {
            $response = isset($response) ? $response : null;
            throw new IslandoraRestClientException($response, $e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() == 200) {
            $this->deleted = true;
        }

        return $response;
    }
}
