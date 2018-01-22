<?php

/**
 * @file
 * index.php script for PHP's built-in web server that returns responses used in PHPUnit
 * tests for the Islandora REST Client library. The server is invoked and shut down from
 * within the TestServer.php class.
 */

$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$post_data = $_POST;
$put_data = file_get_contents('php://input');

// Request is for relationships.
if (preg_match('#/islandora/rest/v1/object/.*/relationship#', $request_uri)) {
}

// Request is for datastreams.
if (preg_match('#/islandora/rest/v1/object/.*/datastream#', $request_uri)) {
}

// Request is for objects.
if (preg_match('#/islandora/rest/v1/object.*#', $request_uri)) {
    switch ($method) {
        case 'GET':
            object_read();
            break;
        case 'POST':
            object_create();
            break;
        case 'PUT':
            object_update();
            break;
        case 'DELETE':
            object_delete();
            break;
    }
}

// Request is for Solr endpoint.
if (preg_match('#/islandora/rest/v1/solr/.*#', $request_uri)) {
    solr_query();
}

function object_read()
{
    header("Content-Type: application/json");

print <<< END
{
 "pid": "test:pid",
 "label": "Mock Object",
 "owner": "fedoraAdmin",
 "models": ["islandora:collectionCModel"],
 "state": "A",
 "created": "2013-05-27T09:53:39.286Z",
 "modified": "2013-06-24T04:20:26.190Z",
 "datastreams": [{
   "dsid": "RELS-EXT",
   "label": "Fedora Object to Object Relationship Metadata.",
   "state": "A",
   "size": 1173,
   "mimeType": "application\/rdf+xml",
   "controlGroup": "X",
   "created": "2013-06-23T07:28:32.787Z",
   "versionable": true,
   "versions": []
 }]
}
END;
}

function object_create()
{
    // Necessary, along with flush(), when returning a response code.
    ob_start();
    http_response_code(201);
    header("Content-Type: application/json");

print <<< END
{
 "pid": "test:pid",
 "label": "Mock Object",
 "owner": "fedoraAdmin",
 "models": ["islandora:collectionCModel"],
 "state": "A",
 "created": "2013-05-27T09:53:39.286Z",
 "modified": "2013-06-24T04:20:26.190Z",
 "datastreams": [{
   "dsid": "RELS-EXT",
   "label": "Fedora Object to Object Relationship Metadata.",
   "state": "A",
   "size": 1173,
   "mimeType": "application\/rdf+xml",
   "controlGroup": "X",
   "created": "2013-06-23T07:28:32.787Z",
   "versionable": true,
   "versions": []
 }]
}
END;
    flush();
}

function object_delete()
{
    ob_start();
    http_response_code(200);
    flush();
}

function object_update()
{
    header("Content-Type: application/json");

print <<< END
{
 "pid": "test:pid",
 "label": "Mock Object",
 "owner": "fedoraAdmin",
 "state": "A",
 "modified": "2013-06-24T04:20:26.190Z"
}
END;
}

function solr_query()
{
    header("Content-Type: application/json");

print <<< END
{
  "responseHeader": {
    "status": 0,
    "QTime": 2,
    "params": {
      "q": "dc.title:testing",
      "json.nl": "map",
      "fl": "PID",
      "start": "0",
      "fq": "RELS_EXT_isViewableByUser_literal_ms:\\u0022admin\\u0022 OR RELS_EXT_isViewableByRole_literal_ms:\\u0022authenticated user\\u0022 OR RELS_EXT_isViewableByRole_literal_ms:\\u0022administrator\\u0022 OR ((*:* -RELS_EXT_isViewableByUser_literal_ms:[* TO *]) AND (*:* -RELS_EXT_isViewableByRole_literal_ms:[* TO *]))",
      "rows": "20",
      "version": "1.2",
      "wt": "json"
    }
  },
  "response": {
    "numFound": 4,
    "start": 0,
    "docs": [
      {
        "PID": "islandora:100"
      },
      {
        "PID": "islandora:20"
      },
      {
        "PID": "islandora:300"
      },
      {
        "PID": "islandora:400"
      }
    ]
  }
}
END;
}



// for use during development.
function dump($variable, $label = null, $destination = null)
    {
        if (is_null($destination)) {
            $destination = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "irc_test_server_log.txt";
        }
        $value = var_export($variable, true) . "\n";
        if (!is_null($label)) {
            $value = $label . ":" . PHP_EOL . $value;
        }
        file_put_contents($destination, $value, FILE_APPEND);
    }

