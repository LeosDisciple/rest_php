<?php

require_once('../model/Response.php');

class DB {

  // DB Parameters
  private static $servername = "localhost"; // TODO: If put in wrong host, the Exception is not caught properly
  private static $username = "root";
  private static $password = "root";
  private static $database = "tasksdb";

  public static function connectMyDB($database) {
    try {
      $dbConnection = new mysqli(self::$servername, self::$username, self::$password, $database);
      if (!$dbConnection) {
        Response::sendResponse(500, false, "Error connecting to DB", null);
      }
      return $dbConnection;
    }
    catch (Exception $ex) {
      error_log("Connection error - ".$ex, 0); // 0 = PHP error log file
      Response::sendResponse(500, false, "Error connecting to DB", null);
    }
  }

}

?>
