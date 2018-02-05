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
if (preg_match('#/islandora/rest/v1/object/.*/relationship.*#', $request_uri)) {
    switch ($method) {
        case 'GET':
            relationship_read();
            exit;
        case 'POST':
            relationship_create();
            exit;
        case 'PUT':
            relationship_update();
            exit;
        case 'DELETE':
            relationship_delete();
            exit;
    }
}

// Request is for datastreams.
if (preg_match('#/islandora/rest/v1/object/.*/datastream.*#', $request_uri)) {
    switch ($method) {
        case 'GET':
            datastream_read();
            exit;
        case 'POST':
            datastream_post($post_data);
            exit;
        case 'PUT':
            datastream_update();
            exit;
        case 'DELETE':
            datastream_delete();
            exit;
    }
}

// Request is for objects.
if (preg_match('#/islandora/rest/v1/object.*#', $request_uri)) {
    switch ($method) {
        case 'GET':
            object_read();
            exit;
        case 'POST':
            object_create();
            exit;
        case 'PUT':
            object_update();
            exit;
        case 'DELETE':
            object_delete();
            exit;
    }
}

// Request is for Solr endpoint.
if (preg_match('#/islandora/rest/v1/solr/.*#', $request_uri)) {
    solr_query();
}

/**
 * HTTP response for GET /islandora/rest/v1/object/[PID].
 */
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

/**
 * HTTP response for POST /islandora/rest/v1/object.
 */
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

/**
 * HTTP response for DELETE /islandora/rest/v1/object/[PID].
 */
function object_delete()
{
    ob_start();
    http_response_code(200);
    flush();
}

/**
 * HTTP response for PUT /islandora/rest/v1/object/[PID].
 */
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

/**
 * HTTP response for GET /islandora/rest/v1/solr.
 */
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

/**
 * HTTP response for GET /islandora/rest/v1/object/[PID]/datastream/[DISD].
 */
function datastream_read()
{
    header("Content-Type: text/xml");

print <<< END
<mods xmlns="http://www.loc.gov/mods/v3" xmlns:mods="http://www.loc.gov/mods/v3" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <titleInfo>
    <title>Test MODS document.</title>
  </titleInfo>
</mods>
END;
}

/**
 * HTTP response for POST /islandora/rest/v1/object/[PID]/datastream/[DISD].
 */
function datastream_post($post_data)
{
    if (array_key_exists('method', $post_data) && $post_data['method'] == 'PUT') {
        http_response_code(200);
    } else {
        http_response_code(201);
        header("Content-Type: application/json");
    }
}

/**
 * HTTP response for DELETE /islandora/rest/v1/object/[PID]/datastream/[DISD].
 */
function datastream_delete()
{
    http_response_code(200);
}

/**
 * HTTP response for PUT /islandora/rest/v1/object/[PID]/datastream/[DISD].
 */
function datastream_update()
{
    header("Content-Type: application/json");

print <<< END
{
 "label": "New MODS DS label"
}
END;
}

/**
 * HTTP response for GET /islandora/rest/v1/object/[PID]/relationship.
 */
function relationship_read()
{
    header("Content-Type: application/json");

print <<< END
[
   {
      "predicate":{
         "value":"hasModel",
         "alias":"fedora-model",
         "namespace":"info:fedora\/fedora-system:def\/model#"
      },
      "object":{
         "literal":false,
         "value":"islandora:sp_basic_image"
      }
   }
]
END;
}

/**
 * HTTP response for POST /islandora/rest/v1/object/[PID]/relationship.
 */
function relationship_create()
{
    http_response_code(201);
    header("Content-Type: application/json");
}

/**
 * HTTP response for DELETE /islandora/rest/v1/object/[PID]/relationship.
 */
function relationship_delete()
{
    http_response_code(200);
}

/**
 * Utility function to write a variable's value to a file.
 *
 * For use during development only.
 */
function dump($variable, $label = null, $destination = null)
{
    if (is_null($destination)) {
        $destination = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "irc_test_server_log.txt";
    }
    $value = var_export($variable, true) . "\n";
    if (!is_null($label)) {
        $value = $label . ":" . PHP_EOL . $value;
    }
    file_put_contents($destination, var_export($value, true), FILE_APPEND);
}

