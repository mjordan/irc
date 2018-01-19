# Islandora REST Client

PHP library for building clients for Islandora's REST interfacei, applying a simple pattern of CRUD (Create, Read, Update, Delete) operations to Islandora 7.x objects, relationships, and datastreams. Its goal is to hide the details of intreacting with a REST interface while providing access to the full HTTP responses.

Still in early development. Not for use in production yet.

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

Or, use composer:

```
composer require mjordan/irc
```

## Examples

### Objects

```php

<?php

include 'vendor/autoload.php';

$client_defaults = array(
    'base_uri' => 'http://localhost:8000/islandora/rest/v1/',
    'headers' => array('X-Authorization-User' => 'admin:admin'),
);

$object = new mjordan\Irc\Object($client_defaults);

// Read an object.

// Create an object. When we create a new object, we can assign a content
// model and parent. Newly created objects have a pid property, e.g., $object->pid,
// which can be used to add relationships or datastreams.
$create_object_response = $object->create('rest', 'admin', "My new object", "islandora:sp_basic_image", "restingester:collection");
echo "Object created: " . $create_object_response->getStatusCode() . "\n";
echo $create_response_body = (string) $create_object_response->getBody();

// Update an object.
$response = $object->update('rest:1320', array('owner' => 'mark'));

// Delete an object.
$response = $object->delete('rest:1320');
var_dump($object->deleted);


$response_code = $response->getStatusCode();
var_dump($response_code);
$response_body = (string) $response->getBody();
var_dump($response_body);
```

### Relationships

```php
<?php

include 'vendor/autoload.php';

$client_defaults = array(
    'base_uri' => 'http://localhost:8000/islandora/rest/v1/',
    'headers' => array('X-Authorization-User' => 'admin:admin'),
);

$rel = new mjordan\Irc\Relationship($client_defaults);

// Read.
$response = $rel->read('rest:1321', array('predicate' => 'hasModel', 'uri' => 'info:fedora/fedora-system:def/model#'));

// Create.
// ?

// Delete.
$response = $rel->delete('rest:1321', array('predicate' => 'hasModel', 'uri' => 'info:fedora/fedora-system:def/model#'));

$response_code = $response->getStatusCode();
var_dump($response_code);
$response_body = (string) $response->getBody();
var_dump($response_body);
```

### Datastreams

```php
<?php

include 'vendor/autoload.php';

$client_defaults = array(
    'base_uri' => 'http://localhost:8000/islandora/rest/v1/',
    'headers' => array('X-Authorization-User' => 'admin:admin'),
);

$ds = new mjordan\Irc\Datastream($client_defaults);

// Read.
$response = $ds->read('rest:1322', 'MODS');

// Create
// ?

// Delete
$response = $ds->delete('rest:1321', 'MODS');

// Update
$response = $ds->update('rest:1322', 'MODS', '/tmp/MODSNEW.xml', array());
$response = $ds->update('rest:1322', 'MODS', null, array('label' => 'Let us try that again.'));

$response_code = $response->getStatusCode();
var_dump($response_code);
$response_body = (string) $response->getBody();
var_dump($ds->mimeType);
var_dump($response_body);
```

### Querying Solr

Doesn't use `->read()`, uses `->query()`. Also has `->numfound`, `->start`, and `->docs` properties.

```php
<?php

include 'vendor/autoload.php';

// New objects, datastreams, relationships, and Solr queries need
// to pass in Guzzle client defaults.
$client_defaults = array(
    'base_uri' => 'http://localhost:8000/islandora/rest/v1/',
    'headers' => array('X-Authorization-User' => 'admin:admin'),
);

// Query Solr.
$solr = new mjordan\Irc\Solr($client_defaults);
$solr_response = $solr->query('dc.title:testing?fl=PID');

echo "NumFound: " . $solr->numFound . "\n";
echo "Start: " . $solr->start . "\n";
echo "Docs: "; var_dump($solr->docs) . "\n";
```

## Maintainer

* [Mark Jordan](https://github.com/mjordan)

## Development and feedback

Still in very early development. Once it's past the proof of concept stage, I'd be happy to take PRs, etc.

## License

The Unlicense
