<?php

namespace mjordan\Irc;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

/**
 * Islandora REST Client Object base class.
 */
class IslandoraObject
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
    public $deleted = false;

    /**
     * @var bool
     */
    public $updated = false;

    /**
     * @var string
     */
    public $pid;

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
     * Retrieves an object via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object.
     *
     * @return object
     *    The Guzzle response.
     */
    public function read($pid)
    {
        try {
            $response = $this->client->get($this->clientDefaults['base_uri'] . 'object/' . $pid);
        } catch (RequestException $e) {
            $response = isset($response) ? $response : null;
            throw new IslandoraRestClientException($response, $e->getMessage(), $e->getCode(), $e);
        }

        return $response;
    }

    /**
     * Creates a new object via Islandora's REST interface.
     *
     * @param string $namespace
     *    The namespace to use for the new object.
     * @param string $owner
     *    The object's owner ID.
     * @param string $label
     *    The label to assign to the object.
     * @param string $cmodel
     *    The object's content model. Additional content models can
     *    to be assigned as separate Relationships.
     * @param string $parent
     *    The object's parent. Additional parents can to be assigned
     *    as separate Relationships.
     *
     * @return object
     *    The Guzzle response.
     */
    public function create($namespace, $owner, $label, $cmodel = null, $parent = null)
    {
        try {
            $response = $this->client->post('object', [
                'form_params' => [
                    'namespace' => $namespace,
                    'owner' => $owner,
                    'label' => $label,
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
            $response_body = json_decode((string) $response->getBody());

            $this->pid = $response_body->pid;
            $this->created = true;

            // Assign a content model during object creation.
            if (!is_null($cmodel)) {
                $params = array(
                    'uri' => 'info:fedora/fedora-system:def/model#',
                    'predicate' => 'hasModel',
                    'object' => $cmodel,
                    'type' => 'uri',
                );

                // Use the object's clientDefaults to instantiate the relationship.
                $relationship = new Relationship($this->clientDefaults);
                $relationship->create($this->pid, $params);
            }

            // Assign a parent relationship during object creation.
            if (!is_null($parent)) {
                $params = array(
                    'uri' => 'info:fedora/fedora-system:def/relations-external#',
                    'predicate' => 'isMemberOfCollection',
                    'object' => $parent,
                    'type' => 'uri',
                );

                // Use the object's clientDefaults to instantiate the relationship.
                $relationship = new Relationship($this->clientDefaults);
                $relationship->create($this->pid, $params);
            }
        }

        return $response;
    }

    /**
     * Deletes an object via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object.
     *
     * @return object
     *    The Guzzle response.
     */
    public function delete($pid)
    {
        try {
            $response = $this->client->delete($this->clientDefaults['base_uri'] . 'object/' . $pid);
        } catch (RequestException $e) {
            $response = isset($response) ? $response : null;
            throw new IslandoraRestClientException($response, $e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() == 200) {
            $this->deleted = true;
        }

        return $response;
    }

    /**
     * Updates an object via Islandora's REST interface.
     *
     * @param string $namespace
     *    The namespace to use for the new object.
     * @param array $properties
     *    An associative array of updated properties:
     *    -label
     *    -owner
     *    -state
     *
     * @return object
     *    The Guzzle response.
     */
    public function update($pid, $properties)
    {
        try {
            $response = $this->client->put($this->clientDefaults['base_uri'] . 'object/' . $pid, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => $properties,
            ]);
        } catch (RequestException $e) {
            $response = isset($response) ? $response : null;
            throw new IslandoraRestClientException($response, $e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() == 200) {
            $this->updated = true;
        }

        return $response;
    }
}
