<?php

namespace mjordan\Irc;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Islandora REST Client Datastream base class.
 */
class Datastream
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
    public $updated = false;

    /**
     * @var bool
     */
    public $deleted = false;

    /**
     * Constructor.
     */
    public function __construct($client_defaults)
    {
        $this->clientDefaults = $client_defaults;
        $this->client = new GuzzleClient($client_defaults);
    }

    /**
     * Retrieves a datastream via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object.
     * @param string $dsid
     *    The DSID of the datastream.
     * @param bool $content
     *    Whether or not to include the datastream content
     *    in the response body.
     * @param string $version
     *    The version of the datastream to return, identified
     *    by its created date of the datastream in ISO 8601
     *    format yyyy-MM-ddTHH:mm:ssZ
     *
     * @return object
     *    The Guzzle response.
     */
    public function read($pid, $dsid, $content = false, $version = null)
    {
        return $this->client->get('object/' . $pid . '/datastream/' . $dsid);
    }

    /**
     * Creates a new datastream via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object to attach to datastream to.
     * @param string $dsid
     *    The DSID of the datastream.
     * @param string $path
     *    The full path to the file to use as the datastream's content.
     * @param string $checksum_type
     *    The checksum type, e.g. SHA-1.
     *
     * @return object
     *    The Guzzle response.
     */
    public function create($pid, $dsid, $path, $checksum_type = 'DISABLED')
    {
        $pathinfo = pathinfo($path);

        // The base_uri is not being set here automatically as a Guzzle
        // default. The headers are, however, and the base_uri is being
        // set in the object client.
        $response = $this->client->post($this->clientDefaults['base_uri'] . 'object/' . $pid . '/datastream', [
            'multipart' => array(
                [
                    'name' => 'file',
                    'filename' => $pathinfo['basename'],
                    'contents' => fopen($path, 'r'),
                ],
                [
                    'name' => 'dsid',
                    'contents' => $dsid,
                ],
                [
                    'name' => 'checksumType',
                    'contents' => $checksum_type,
                ],
            ),
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
     * Deletes a datastream via Islandora's REST interface.
     *
     * @param string $pid
     *    The PID of the object.
     * @param string $dsid
     *    The DSID of the datastream.
     *
     * @return object
     *    The Guzzle response.
     */
    public function delete($pid, $dsid)
    {
        // Delete it.
        // if ($response->getStatusCode() == 200) {
            // $this->deleted = true;
        // }
    }

    /**
     * Updates a datastream via Islandora's REST interface.
     *
     * @param string $namespace
     *    The namespace to use for the new object.
     * @param string $dsid
     *    The DSID of the datastream.
     * @param array $properties
     *    An associative array of updated properties.
     *
     * @return object
     *    The Guzzle response.
     */
    public function update($pid, $dsid, $properties)
    {
        // Update it.
        // if ($response->getStatusCode() == 200) {
            // $this->updated = true;
        // }
    }
}
