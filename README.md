# Islandora REST Client [![Build Status](https://travis-ci.org/mjordan/irc.svg?branch=master)](https://travis-ci.org/mjordan/irc)

PHP library for building clients for Islandora's REST interface, applying a simple pattern of CRUD (Create, Read, Update, Delete) operations to Islandora 7.x objects, relationships, and datastreams. Also provides access to the Islandora REST module's Solr endpoint. The library's goal is to hide the details of intreacting with a REST interface while providing access to the full HTTP responses.

## Requirements

* On the system where REST client applications/scripts are run
  * PHP 5.5.0 or higher. Tested with PHP 5.5, 5.6, 7.0, 7.1.
  * [Composer](https://getcomposer.org)
* On the Islandora instance
  * [Islandora REST](https://github.com/discoverygarden/islandora_rest)
  * Optionally, [Islandora REST Authen](https://github.com/mjordan/islandora_rest_authen)
  * Optionally, [Islandora REST Extras](https://github.com/mjordan/islandora_rest_extras) (see "Generating DC XML" below for more information).

## Installation

1. `git https://github.com/mjordan/irc.git`
1. `cd irc`
1. `php composer.phar install` (or equivalent on your system, e.g., `./composer install`)

Or, use composer:

```
composer require mjordan/irc
```

and within a composer.json file:

```javascript
    "require": {
        "mjordan/irc": "dev-master"
    }
```

## Usage

Islandora objects, relationships, and datastreams are instantiated using a set of client defaults. In general, you only need to provide a default `base_uri` and any headers you need, e.g. for authentication. For most requests, the appropriate `Content-Type` headers are provided for you. Guzzle's `http_errors` request optin is always set to `false`.

```php
<?php
include 'vendor/autoload.php';

$client_defaults = array(
    'base_uri' => 'http://localhost:8000/islandora/rest/v1/',
    'headers' => array('X-Authorization-User' => 'admin:admin'),
);

$islandora_object = new mjordan\Irc\Object($client_defaults);
$relationship = new mjordan\Irc\Relationship($client_defaults);
$object = new mjordan\Irc\Datastream($client_defaults);
```

Islandora object and datastreams provide `read()`, `create()`, `delete()`, and `update()` methods; relationships provide `read()`, `create()`, and `delete()` methods. In all cases, these methods returns a Guzzle response object. However, object, relationship, and datastream objects provide convenience propteries that you can use to check the success of the various methods (e.g., `->created`, `->deleted`) are illustrated in the examples below.

## Authentication

Since you can pass in arbitrary request headers, you can use any authentication method that is possible through the use of headers. The [Islandora REST Authen](https://github.com/mjordan/islandora_rest_authen) module provides a convenient way to use a single `'X-Authorization-User` request header to authenticate REST requests. The examples below use this header.

## Examples

### Islandora objects

```php
<?php

include 'vendor/autoload.php';

$client_defaults = array(
    'base_uri' => 'ihttp://localhost:8000/islandora/rest/v1/',
    'headers' => array('X-Authorization-User' => 'admin:admin'),
);

$object = new mjordan\Irc\Object($client_defaults);

// Read an object. CRUD methods on objects return a Guzzle response object.
$response = $object->read('islandora:100');

// Create an object. When we create a new object, we can optionally assign a content
// model and parent. 
$response = $object->create('islandora', 'admin', "My new object", "islandora:sp_basic_image", "islandora:testcollection");
// True if successfully created, false if not.
var_dump($object->created);
// You can also access the new object's PID.
var_dump($object->pid);

// Update an object.
$response = $object->update('islandora:150', array('owner' => 'mark'));
// True if successfully updated, false if not.
var_dump($object->updated);

// Delete an object.
$response = $object->delete('islandora:200');
// True if successfully deleted, false if not.
var_dump($object->deleted);

// For read(), create(), update(), and delete().
$response_code = $response->getStatusCode();
var_dump($response_code);
$response_body = (string) $response->getBody();
var_dump($response_body);
```

### Relationships

Note that there is no `update()` method for relationships. In order to change a relationship, you need to delete it first, then add a replacement relationship.

```php
<?php

include 'vendor/autoload.php';

$client_defaults = array(
    'base_uri' => 'http://localhost:8000/islandora/rest/v1/',
    'headers' => array('X-Authorization-User' => 'admin:admin'),
);

$rel = new mjordan\Irc\Relationship($client_defaults);

$response = $rel->read('islandora:123', array('predicate' => 'hasModel', 'uri' => 'info:fedora/fedora-system:def/model#'));

// Create a relationship.
$params = array(
    'uri' => 'info:fedora/fedora-system:def/relations-external#',
    'predicate' => 'isMemberOfCollection',
    'object' => $parent_pid,
    'type' => 'uri',
);

$rel = new Relationship($this->clientDefaults);
$response = $rel->create('islandora:456', $params);
// True if successfully created, false if not.
var_dump($rel->created);

$response = $rel->delete('islandora:789', array('predicate' => 'hasModel', 'uri' => 'info:fedora/fedora-system:def/model#'));
// True if successfully deleted, false if not.
var_dump($rel->deleted);

// For read(), create(), and delete().
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

$response = $ds->read('islandora:100', 'MODS');

$response = $ds->create(
	$object->pid,
	'MODS',
	'/tmp/MODS.xml',
	array('label' => 'I am a new MODS document')
);
// True if successfully created, false if not.
var_dump($ds->created);

$response = $ds->delete('islandora:100', 'MODS');
var_dump($ds->deleted);
// True if successfully deleted, false if not.
var_dump($ds->deleted);

$response = $ds->update('rest:1322', 'MODS', '/tmp/MODSNEW.xml', array('label' => 'Let us try that again.'));
// These two properties are true if successfully updated, false if not.
var_dump($ds->propertiesUpdated);
var_dump($ds->contentUpdated);

// For read(), create(), update(), and delete().
$response_code = $response->getStatusCode();
var_dump($response_code);
$response_body = (string) $response->getBody();
var_dump($response_body);
```

### Querying Solr

Solr objects don't provide a `->read()` method, they provide`->query()`, which should contain a raw Solr query string. They also provide convenience properties`->numfound`, `->start`, and `->docs`, which provide direct access to those parts of the raw Solr response.

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

## Exceptions

Guzzle `RequestException` and missing datastream file errors are rethrown as `IslandoraRestClientException` exceptions.

## Maintainer

* [Mark Jordan](https://github.com/mjordan)

## Development and feedback

* Bug reports, feature requests, and documentation improvements are welcome.
* If you want to open a pull request, please open an issue first.
* Run tests with `composer tests`, and run PSR2 style checks with `composer style`.

## License

The Unlicense
