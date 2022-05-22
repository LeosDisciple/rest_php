<?php

require_once('db.php');
require_once('../model/Response.php');
require_once('../model/Note.php');

/* TODO:
 - Define what to do with "date" - is it the last Creation / update? Can it be set manually?
 - Implement the date structure string operations
*/

// Checks content-type is set to JSON
// Return error message to client and exits if faulty
function checkContentTypeJson() {

  if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
    Response::sendResponse(400, false, "Content type header not set to JSON", null);
    exit;
  }
}

print "Welcome to NOTES / ";

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  print "GET / ";

  // Identify Use Case

  // 1. Get Id
  if (array_key_exists("noteid", $_GET)) {
    print "Get Id\n";
    $noteid = $_GET['noteid'];
    print "Id: ".$noteid;
    // 1. Control Parameters
    // No control needed as $noteid has the proper structure. This was ensured by .htaccess

    // 2. Execute DB access
    $note = new Note();
    // TODO: Optimize by passing this parameter to the "readFromDB()" method
    $note->setId($noteid);

    // 3. Execute DB access
    $note->readFromDB();
  }

  // 2. Get All Paginated
  elseif (array_key_exists("page", $_GET)) {
    // 1. Control Parameters
    // No control needed as $noteid has the proper structure. This was ensured by .htaccess

    // 2. Execute DB access
    $note = new Note();

    // 3. Execute DB access
    $note->readPage($_GET['page']);
  }

  // 3. Get All Public / Private
  elseif (array_key_exists("is_public", $_GET)) {
    print "Get All Public / Public_Private\n";
    print "is_public=".$_GET['is_public'];

    // OPTIMIZE: Is this code redundant as it's covering something that should not happen?
    if ($_GET['is_public']!=='Y' && $_GET['is_public']!=='N') {
      print "Should never arrive here. If so, then .htaccess was not configured correctly";
      Response::sendResponse(500, false, "Internal server error", null);
    }
    // 1. Execute DB access
    $note = new Note();
    $note->readAllPublicPrivate($_GET['is_public']);
  }

  // 4. Get All
  else {
    print "Get All\n";
    // 1. Execute DB access
    $note = new Note();
    $note->readAllFromDB();
  }

}

// POST
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
  print "POST\n";
  // 1. Control Parameters
  // 1.1. Check content type JSON
  checkContentTypeJson();
  // 1.2. Check structure is JSON
  if (!$jsonData = json_decode(file_get_contents('php://input'))) {
    Response::sendResponse(400, false, "Request body is not valid JSON", null);
  }

  // 2. Create Data Object and inject JSON object (including all data checks)
  // --> Error handling: In case of inconsistent data an Error message is returned directly to the client
  $note = new Note();
  $note->injectJson($jsonData);

  // 3. Execute DB access
  $note->writeToDB();
}

// PUT
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
  print "PUT\n";
  // Check request structure
  if (array_key_exists("noteid", $_GET)) {
    $noteid = $_GET['noteid'];
    print "Id: ".$noteid;
    // 1. Control Parameters
    // 1.1. Check content type JSON
    checkContentTypeJson();
    // 1.2. Check structure is JSON
    if (!$jsonData = json_decode(file_get_contents('php://input'))) {
      Response::sendResponse(400, false, "Request body is not valid JSON", null);
    }

    // 2. Create Data Object and inject JSON object (including all data checks)
    // --> Error handling: In case of inconsistent data an Error message is returned directly to the client
    $note = new Note();
    $note->injectJson($jsonData);
    // TODO: Optimize by passing this parameter to the "readFromDB()" method
    $note->setId($noteid);

    print "\nReady to update object";

    // 3. Execute DB access
    $note->updateOnDB();
  }
  else {
    // No Id defined by client
    // The code never arrives here as the server returns a "method not allowed" when the parameter is not as defined in the .htaccess file (namely a integer number)
    // -> # Puts the id into a parameter called "noteid"
    // -> RewriteRule ^notes/([0-9]+)$ controller/notes.php?noteid=$1 [L]
    print "Should never arrive here. If so, then .htaccess was not configured correctly"; // TODO: Never arriving here??? Is the web server interferring? Michael does it better!
    Response::sendResponse(500, false, "Internal server error", null);
  }
}

// DELETE
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  print "DELETE / ";
  // Check request structure
  if (array_key_exists("noteid", $_GET)) {
    $noteid = $_GET['noteid'];
    print "Id: ".$noteid;
    // 1. Control Parameters
    // No control needed as $noteid has the proper structure. This was ensured by .htaccess

    // 2. Execute DB access
    $note = new Note();
    // TODO: Optimize by passing this parameter to the "readFromDB()" method
    $note->setId($noteid);

    // 3. Execute DB access
    $note->deleteFromDB();
  }
  else {
    // No Id defined by client
    // The code never arrives here as the server returns a "method not allowed" when the parameter is not as defined in the .htaccess file (namely a integer number)
    // -> # Puts the id into a parameter called "noteid"
    // -> RewriteRule ^notes/([0-9]+)$ controller/notes.php?noteid=$1 [L]
    print "Should never arrive here. If so, then .htaccess was not configured correctly"; // TODO: Never arriving here??? Is the web server interferring? Michael does it better!
    Response::sendResponse(500, false, "Internal server error", null);
  }
}

// UNSUPPORTED METHODS
else {
  print "REQUEST METHOD NOT ALLOWED\n";
  Response::sendResponse(405, false, "Request method not allowed: ".$_SERVER['REQUEST_METHOD'], null);
  exit;
}

?>
