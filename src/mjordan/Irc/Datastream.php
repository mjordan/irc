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

        // For some reason, the base_uri is not being set here automatically.
        // The headers are, however, and the base_uri is being set in the
        // object client.
        return $this->client->post($this->clientDefaults['base_uri'] . 'object/' . $pid . '/datastream', [
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
    }
}
