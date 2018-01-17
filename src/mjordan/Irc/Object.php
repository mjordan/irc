<?php

namespace mjordan\Irc;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Islandora REST Client Object base class.
 */
class Object
{
    /**
     * Constructor
     */
    public function __construct($client_defaults)
    {
        $this->clientDefaults = $client_defaults;
        $this->client = new GuzzleClient($client_defaults);
    }

    public function read($pid)
    {
        return $this->client->get('object/' . $pid);
    }

    /**
     * Creates a new datastream via Islandora's REST interface.
     *
     * @param string $namespace
     *    The namespace to use for the new object.
     * @param string $owner
     *    The object's owner ID.
     * @param string $label
     *    The label to assign to the object.
     * @param string $cmodel
     *    The object's content model. Additional content models need
     *    to be assigned as separate Relationships.
     * @param string $parent
     *    The object's parent. Additional parents need to be assigned
     *    as separate Relationships.
     *
     * @return object
     *    The Guzzle response.
     */
    public function create($namespace, $owner, $label, $cmodel = null, $parent = null)
    {
        $object_response = $this->client->post('object', [
            'form_params' => [
                'namespace' => $namespace,
                'owner' => $owner,
                'label' => $label,
            ],
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);

        if ($object_response->getStatusCode() == 201) {
            $object_response_array = json_decode((string) $object_response->getBody(), true);

            $this->pid = $object_response_array['pid'];

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

        return $object_response;
    }
}
