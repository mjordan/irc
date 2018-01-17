# Islandora REST Client

PHP library for building clients for Islandora's REST interfacei, using a simple pattern of applying CRUD (Create, Read, Update, Delete) operations to Islandora 7.x objects, relationships, and datastreams. Its goal is to hide the details of intreacting with a REST interface.

Still in early development, please stay tuned.

## Requirements

* On the target Islandora instance
  * [Islandora REST](https://github.com/discoverygarden/islandora_rest)
  * [Islandora REST Authen](https://github.com/mjordan/islandora_rest_authen)
  * Optionally, [Islandora REST Extras](https://github.com/mjordan/islandora_rest_extras) (see "Generating DC XML" below for more information).
* On the system where the script is run
  * PHP 5.5.0 or higher.
  * [Composer](https://getcomposer.org)

## Installation

1. `git https://github.com/mjordan/irc.git`
1. `cd irc`
1. `php composer.phar install` (or equivalent on your system, e.g., `./composer install`)

## Example

```php
<?php

include 'vendor/autoload.php';

// New objects, datastreams, and relationships need to pass in
// Guzzle client defaults.
$client_defaults = array(
    'base_uri' => 'http://localhost:8000/islandora/rest/v1/',
    'headers' => array('X-Authorization-User' => 'admin:admin'),
);

$object = new mjordan\Irc\Object($client_defaults);

// Read an object. CRUD methods on objects return a Guzzle response object
$read_object_response = $object->read('restingester:collection');
$read_response_code = $read_object_response->getStatusCode();
$read_response_body = (string) $read_object_response->getBody();

echo "The REST API replied with a repsonse of $read_response_code:\n";
echo $read_response_body . "\n";

// Create an object. When we create a new object, we can assign a content
// model and parent.
$create_object_response = $object->create('rest', 'admin', "My new object", "islandora:sp_basic_image", "restingester:collection");
echo "Object created: " . $create_object_response->getStatusCode() . "\n";
echo $create_response_body = (string) $create_object_response->getBody();

// Get the new object's PID.
$response_body = json_decode($create_response_body);
$pid = $response_body->pid;

// If we wanted to create new relationships for the object, we could do so here.
// CRUD methods on relationships return a Guzzle response object.

// Create a datastream. CRUD methods on datastreams return a Guzzle response object.
$datastream = new mjordan\Irc\Datastream($client_defaults);
$create_datastream_response = $datastream->create($pid, 'MODS', '/tmp/MODS.xml');
echo "Datastream created: " . $create_datastream_response->getStatusCode() . "\n";
echo $create_datastream_response_body = (string) $create_datastream_response->getBody();
```

## Maintainer

* [Mark Jordan](https://github.com/mjordan)

## Development and feedback

Still in very early development. Once it's past the proof of concept stage, I'd be happy to take PRs, etc.

## License

The Unlicense
