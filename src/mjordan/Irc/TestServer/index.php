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

if (preg_match('#/islandora/rest/v1/object/.*/relationship#', $request_uri)) {
}

if (preg_match('#/islandora/rest/v1/object/.*/datastream#', $request_uri)) {
}

if (preg_match('#/islandora/rest/v1/object/.*#', $request_uri)) {
    switch ($method) {
        case 'GET':
            object_read($request_uri);
            break;
        case 'POST':
        case 'PUT':
        case 'DELETE':
    }
}

if (preg_match('#/islandora/rest/v1/solr/.*#', $request_uri)) {
}

function object_read($request_uri)
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

