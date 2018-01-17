<?php

include 'vendor/autoload.php';

$client_defaults = array(
    'base_uri' => 'http://localhost:8000/islandora/rest/v1/',
    'headers' => array('X-Authorization-User' => 'admin:admin'),
);

// New objects, datastreams, and relationships need to pass in
// Guzzle client defaults.
$object = new mjordan\Irc\Object($client_defaults);

// CRUD methods on objects return a Guzzle response object
$read_object_response = $object->read('restingester:collection');

$read_response_code = $read_object_response->getStatusCode();
$read_response_body = (string) $read_object_response->getBody();

echo "The REST API replied with a repsonse of $read_response_code:\n";
echo $read_response_body . "\n";

$create_object_response = $object->create('rest', 'admin', "My new object", array("islandora:sp_basic_image"));
echo "Object created: " . $create_object_response->getStatusCode() . "\n";
echo $create_response_body = (string) $create_object_response->getBody();
