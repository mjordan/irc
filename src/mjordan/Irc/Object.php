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

    public function create($namespace, $owner, $label, $cmodels = array(), $parent = null, $extra_relationships = array())
    {
        $object_response = $this->client->post('object' , [
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
            $pid = $object_response_array['pid'];

            // Convenience shortcut for assigning content models during object creation.
            if (count($cmodels)) {
                foreach ($cmodels as $cmodel_pid) {
                    $params = array(
                        'uri' => 'info:fedora/fedora-system:def/model#',
                        'predicate' => 'hasModel',
                        'object' => $cmodel_pid,
                        'type' => 'uri',
                    );

                    // Use the object's clientDefaults to instantiate the relationship.
                    $relationship = new Relationship($this->clientDefaults);
                    $relationship->create($pid, $params);  
                }
            }

            // @todo: Convenience shortcut for assigning parent relationship during object creation.
            // @todo: Convenience shortcut for assigning extra relationship during object creation.
        }

        return $object_response;
    }
}


