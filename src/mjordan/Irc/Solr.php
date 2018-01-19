<?php

namespace mjordan\Irc;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

/**
 * Islandora REST Client Solr base class.
 */
class Solr
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
     * @var int
     */
    public $numFound = null;

    /**
     * @var int
     */
    public $start = null;

    /**
     * @var array
     */
    public $docs = array();

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
     * @param string $query
     *    The Solr query.
     *
     * @return object
     *    The Guzzle response.
     */
    public function query($query)
    {
        try {
            $response = $this->client->get('solr/' . $query);
        } catch (RequestException $e) {
            $response = isset($response) ? $response : null;
            throw new IslandoraRestClientException($response, $e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() == 200) {
            $response_body = (string) $response->getBody();
            $response_body = json_decode($response_body);
            $this->numFound = $response_body->response->numFound;
            $this->start = $response_body->response->start;
            $this->docs = $response_body->response->docs;
        }

        return $response;
    }
}
