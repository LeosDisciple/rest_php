<?php

require_once('db.php');
require_once('../model/Response.php');

class Note {

  // Attribute definition
  // JSON uses camelCase for attributes https://stackoverflow.com/questions/5543490/json-naming-convention-snake-case-camelcase-or-pascalcase
  /*
    |Name|Type|Mandatory|
    |------|-----|-----|
    |id|int(25)|yes|
    |title|String(25)|yes|
    |content|String(250)|no|
    |isPublic|boolean|yes|
    |date|Date|no|
  */

  // Attributes
  private $_id;
  private $_title;
  private $_content;
  private $_is_public;
  private $_date;

  // Data consistency flag
  private $_dataIsConsistent = false;

  // DB connection
  private $_myDB = null;

  // Constructor - PHP magice method
  // Sets up DB connections
  // TODO: Maybe use singleton and static variable here
  public function __construct() {
    // Get DB connection
    try {
      $this->_myDB = DB::connectMyDB("notesdb");
      print "\nDB connection successful";
    }
    catch (Exception $ex) {
      error_log("Connection error - ".$ex, 0); // 0 = PHP error log file
      Response::sendResponse(500, false, "Error connecting to DB", null);
    }
  }

  //
  // These functions return the correct String for building the SQL statement (e.g. NULL or date in correct format)
  //
  private function getSqlStringContent() {
    return (($this->_content == null) ? "NULL" : "'".$this->_content."'");
  }

  private function getSqlStringDate() {
    return (($this->_date == null) ? "NULL" : "'".$this->_date."'");
  }

  // Checks consistency of data and returns error response to client if data is inconsistent
  public function injectAttributes($id, $title, $content, $is_public, $date) {

    // $id can be null (for e.g. for POST)
    if ($id == null) {
    }
    elseif (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807) {
      Response::sendResponse(400, false, "Wrong field value -> Note Id (out of range): ".$id, null);
    }
    $this->_id = $id;

    // Mandatory field
    if ($title == null) {
      Response::sendResponse(400, false, "Mandatory field cannot be empty -> Note Title", null);
    }
    if (strlen($title) < 0 || strlen($title) > 255) {
      Response::sendResponse(400, false, "Wrong field value -> Note Title (wrong length): ".$title, null);
    }
    $this->_title = $title;

    // $content can be null
    if ($id == null) {
    }
    elseif (strlen($content) < 0 || strlen($content) > 16777215) { // TODO: Can strlen be smaller than 0?
      Response::sendResponse(400, false, "Wrong field value -> Note Description (wrong length): ".$content, null);
    }
    $this->_content = $content;

    // Mandatory field
    if ($is_public == null) {
      Response::sendResponse(400, false, "Mandatory field cannot be empty -> isPublic", null);
    }
    else {
      $is_public = strtoupper($is_public);
      if ($is_public !== 'Y' && $is_public !== 'N') {
        Response::sendResponse(400, false, "Wrong field value -> Note isPublic (must be Y or N): ".$is_public, null);
      }
      $this->_is_public = $is_public;
    }

    /* TODO: Should check on proper format here, but code breaks...
    if (($deadline != null) && date_format(date_create_from_format('d/m/Y H:i', $deadline) ,'d/m/Y H:i') != $deadline) { // Formats: https://www.php.net/manual/en/datetime.createfromformat.php
      throw new TaskException("Task Deadline date time error");
    }
    */
    $this->_date = $date;

    // Attributes are filled with attributes in the correct form
    $this->_dataIsConsistent = true;
  }

  // Injects data from JSON object into object
  public function injectJSON($jsonData) {
    // Passing data from JSON object. If attribute is not set, pass 'null'
    $this->injectAttributes(
        (isset($jsonData->id) ? $jsonData->id : null),
        (isset($jsonData->title) ? $jsonData->title : null),
        (isset($jsonData->content) ? $jsonData->content : null),
        (isset($jsonData->isPublic) ? $jsonData->isPublic : null),
        (isset($jsonData->date) ? $jsonData->date : null)
    );
  }

  public function setId($id) {
    $this->_id = $id;
    print "Id set: ".$this->_id;
  }

  public function returnDataAsArray() {
    $data = array();
    $data['id'] = $this->_id;
    $data['title'] = $this->_title;
    $data['content'] = $this->_content;
    $data['isPublic'] = $this->_is_public;
    $data['date'] = $this->_date;
    return $data;
  }

  // Write object to DB
  public function writeToDB() {
    // This should never happen
    if (!$this->_dataIsConsistent) {
      Response::sendResponse(500, false, "Internal error - tried to save inconsistent object to DB", null);
    }
    print "Writing to DB\n";

    $sql = "INSERT INTO tblnotes (title, content, is_public, date) VALUES ('".$this->_title."', ".$this->getSqlStringContent().", '".$this->_is_public."', ".$this->getSqlStringDate().")";
    print "<br><br>The SQL statement: ".$sql;

    $result = mysqli_query($this->_myDB, $sql); // https://www.php.net/manual/en/mysqli.query.php
    // Returns https://www.php.net/manual/en/class.mysqli-result.php
    print "Wrote to DB\n";
    if ($result) {
      print "Yep, DB write worked!";
      // Retrieve saved data & return to client
      $this->_id = mysqli_insert_id($this->_myDB);
      print "lastTaskID: ".$this->_id;

      // Return data
      $returnData = array();
      $returnData['rows_returned'] = 1;
      $returnData['tasks'] = $this->returnDataAsArray();

      // Build Response
      Response::sendResponse(201, true, "All good =D...", $returnData);
    }
    else {
      print "Nope, DB write didn't work!";
    }
  }

  // Delete from DB
  public function deleteFromDB() {
    print "Deleting from DB\n";
    if ($this->_id == null) {
      Response::sendResponse(500, false, "Tried to delete object, but Id is not set", null);
    }

    $sql = "DELETE FROM tblnotes WHERE id = '".$this->_id."'"; // https://www.php.net/manual/en/book.mysqli.php
    $result = mysqli_query($this->_myDB, $sql); // https://www.php.net/manual/en/mysqli.query.php
    //print_r("RESULT FROM DELETE: ".$result);
    //print("Affected rows: ".mysqli_affected_rows($writeDB));
    // Returns https://www.php.net/manual/en/class.mysqli-result.php

    /*
    Shit not working properly. Gotta wonder if this is the proper architecture though?
    If a delete didn't work, can't we use Exceptions to handle it?
    See also:
    - https://www.tutorialrepublic.com/php-tutorial/php-mysql-delete-query.php
    - https://www.w3schools.com/php/php_mysql_delete.asp

    Number of rows deleted:
    - https://www.php.net/manual/en/mysqli.affected-rows.php
    */

    // Make sure there's only one entry which means that the operation was successful
    //echo "COUNTER: ".count($result->fetch_all());
    $number_of_rows = mysqli_affected_rows($this->_myDB);

    // React to number of rows returned
    if ($number_of_rows === 1) {
      // We're good - one entry ready to be returned
      Response::sendResponse(200, true, "Note successfully deleted: ".$this->_id, null);
    }
    else if ($number_of_rows === 0) {
      // No such entry - return response
      Response::sendResponse(404, false, "Note ID does not exist: ".$this->_id, null);
    }
    else {
      // Other number we've got log an error.... TODO
    }
  }

  public function updateOnDB() {
    print "\nUpdate Id: ".$this->_id;
    if ($this->_id == null) {
      Response::sendResponse(500, false, "Tried to update object, but Id is not set", null);
    }

    // Check if there is object with this id
    $sql = "SELECT * FROM tblnotes WHERE id = '".$this->_id."'"; // https://www.php.net/manual/en
    $result = mysqli_query($this->_myDB, $sql);
    $row_cnt = mysqli_num_rows($result);
    print "\nNumber of rows for this id:".$row_cnt;

    if ($row_cnt == 0) {
      Response::sendResponse(404, false, "Object with Id does not exist: ".$this->_id, null);
    }

    print "\nUpdating on DB";

    // TODO: Inconsitency -> the "Get" methods return quotes (because none are needed when NULL is returned), but when you access the private variables you need to add the quotes. Improvement: make getters for all
    $sql = "UPDATE tblnotes SET title = '".$this->_title."', content = ".$this->getSqlStringContent().", is_public = '".$this->_is_public."', date = ".$this->getSqlStringDate()." WHERE id = '".$this->_id."'";

    print "\nThe SQL statement: \n".$sql;

    $result = mysqli_query($this->_myDB, $sql); // https://www.php.net/manual/en/mysqli.query.php
    // Returns https://www.php.net/manual/en/class.mysqli-result.php
    print "\nUpdated on DB";
    if ($result) {
      print "\nYep, DB update worked!";

      // Return data
      $returnData = array();
      $returnData['rows_returned'] = 1;
      $returnData['tasks'] = $this->returnDataAsArray();

      // Build Response
      Response::sendResponse(201, true, "All good =D...", $returnData);
    }
    else {
      print "\nNope, DB update didn't work!";
    }
  }

  // Is specific to data structure
  private function createArrayFromRow($row) {
    $myArray = array();
    $myArray['id'] = $row['id'];
    $myArray['title'] = $row['title'];
    $myArray['content'] = $row['content'];
    $myArray['is_public'] = $row['is_public'] ;
    $myArray['date'] = $row['date'];
    return $myArray;
  }

  // Read from DB
  public function readFromDB() {
    print "Reading from DB\n";
    if ($this->_id == null) {
      Response::sendResponse(500, false, "Tried to read object, but Id is not set", null);
    }

    $sql = "SELECT * FROM tblnotes WHERE id = '".$this->_id."'"; // https://www.php.net/manual/en/book.mysqli.php
    print "\nSQL: ".$sql;
    $result = mysqli_query($this->_myDB, $sql); // https://www.php.net/manual/en/mysqli.query.php
    //print_r("RESULT FROM DELETE: ".$result);
    //print("Affected rows: ".mysqli_affected_rows($writeDB));
    // Returns https://www.php.net/manual/en/class.mysqli-result.php

    /*
    Shit not working properly. Gotta wonder if this is the proper architecture though?
    If a delete didn't work, can't we use Exceptions to handle it?
    See also:
    - https://www.tutorialrepublic.com/php-tutorial/php-mysql-delete-query.php
    - https://www.w3schools.com/php/php_mysql_delete.asp

    Number of rows deleted:
    - https://www.php.net/manual/en/mysqli.affected-rows.php
    */


    // Make sure there's only one entry
    //echo "COUNTER: ".count($result->fetch_all());
    $da_rows = $result->fetch_all(MYSQLI_ASSOC); // https://www.php.net/manual/en/mysqli-result.fetch-all.php
    //print_r($da_rows);
    $number_of_rows = count($da_rows);

    // React to number of rows returned
    if ($number_of_rows === 1) {

      // TODO: Refactor this one like the other READs (Put data into array and return - don't put them into the object - maybe you can even delete 'returnDataAsArray()')
      // We're good - add values into object
      $this->_id = $da_rows[0]['id'];
      $this->_title = $da_rows[0]['title'];
      $this->_content = $da_rows[0]['content'];
      $this->_is_public = $da_rows[0]['is_public'];
      $this->_date = $da_rows[0]['date'];

      // Store Task in array
      $noteArray[] = $this->returnDataAsArray();

      // Return data
      $returnData = array();
      $returnData['rows_returned'] = $number_of_rows;
      $returnData['notes'] = $noteArray;

      // Build Response
      Response::sendResponse(200, true, "All good =D...", $returnData);
    }
    else if ($number_of_rows === 0) {
      // No such entry - return response
      Response::sendResponse(404, false, "Object with Id does not exist: ".$this->_id, null);
    }
    else {
      // Other number we've got log an error.... TODO
    }
  }

  // Read All from DB
  public function readAllFromDB() {
    print "Reading All from DB\n";

    $sql = "SELECT * FROM tblnotes"; // https://www.php.net/manual/en/book.mysqli.php
    print "\nSQL: ".$sql;
    $result = mysqli_query($this->_myDB, $sql); // https://www.php.net/manual/en/mysqli.query.php

    while ($row = mysqli_fetch_array($result)) {
        // Store Note in array
        $notesArray[] = $this->createArrayFromRow($row);
    }

    // Return data
    $returnData = array();
    $returnData['rows_returned'] = count($notesArray);
    $returnData['notes'] = $notesArray;

    Response::sendResponse(200, true, "All good =D...", $returnData);

    // TODO: Exception handling like in initial code
  }

  // Read from Public/Private from DB
  public function readAllPublicPrivate($is_public) {
    print "Reading Public_Private from DB\n";

    $sql = "SELECT * FROM tblnotes WHERE is_public = '".$is_public."'"; // https://www.php.net/manual/en/book.mysqli.php
    print "\nSQL: ".$sql;
    $result = mysqli_query($this->_myDB, $sql); // https://www.php.net/manual/en/mysqli.query.php

    while ($row = mysqli_fetch_array($result)) {
      // Store Note in array
      $notesArray[] = $this->createArrayFromRow($row);
    }

    // Return data
    $returnData = array();
    $returnData['rows_returned'] = count($notesArray);
    $returnData['notes'] = $notesArray;

    Response::sendResponse(200, true, "All good =D...", $returnData);

    // TODO: Exception handling like in initial code
  }

  // Read Paginated from DB
  public function readPage($page) {
    print "\nReading Page from DB: ".$page;

    // TODO: Rename this variable to 'pageSize'
    $limitPerPage = 2;

    // Let's do it
    $sql = "SELECT * FROM tblnotes";
    $result = mysqli_query($this->_myDB, $sql);
    $row_cnt = $result->num_rows;
    //echo "ROWCNT: ".$row_cnt;
    $numOfPages = ceil($row_cnt/$limitPerPage); // rounds up
    if ($numOfPages == 0) {
      $numOfPages = 1; // to display empty page
    }

    // Make sure the requested page number is in range (Page numbers start at 1)
    if ($page > $numOfPages || $page == 0) {
      Response::sendResponse(404, fasle, "Page not found", null);
    }

    // Now retrieve the right rows
    $offset = ($page == 1 ? 0 : ($limitPerPage*($page-1))); // Could also just do "$limitPerPage*($page-1))", right?
    $sql = "SELECT * FROM tblnotes LIMIT ".$offset.", ".$limitPerPage; // LIMIT: tells from which OFFSET and how MANY entries to retrieve
    $result = mysqli_query($this->_myDB, $sql); // https://www.php.net/manual/en/mysqli.query.php
    // Returns https://www.php.net/manual/en/class.mysqli-result.php

    while ($row = mysqli_fetch_array($result)) {
      // Store Note in array
      $notesArray[] = $this->createArrayFromRow($row);
    }

    // Return data
    $returnData = array();
    $returnData['rows_returned'] = count($notesArray);
    $returnData['total_rows'] = $row_cnt;
    $returnData['total_pages'] = $numOfPages;
    ($page < $numOfPages ? $returnData['has_next_page'] = true : $returnData['has_next_page'] = false);
    ($page > 1 ? $returnData['has_previous_page'] = true : $returnData['has_previous_page'] = false);
    $returnData['notes'] = $notesArray;

    Response::sendResponse(200, true, "All good =D...", $returnData);
  }

}

?>
