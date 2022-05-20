<?php

  class DB {

    // DB Parameters
    private static $servername = "localhost"; // TODO: If put in wrong host, the Exception is not caught properly
    private static $username = "root";
    private static $password = "root";
    private static $database = "tasksdb";

    // Read and Write connections are set up in case we want to separate the DBs for load balancing puposes
    private static $writeDBConnection;
    private static $readDBConnecton;


    // Ugly, but did it like this for now as PDO doesn't work (wrong installation with MAMP???)

    public static function connectWriteDB() {

      if (self::$writeDBConnection === null) {
        self::$writeDBConnection = new mysqli(self::$servername, self::$username, self::$password, self::$database);
        if (!self::$writeDBConnection) {
        	echo "Connection error: "+mysqli_connect_error(); // OPTIMIZE: What to do if DB connection fails?
        } else {
        	//echo "Connection to WriteDB successful!";
        }
      }
      return self::$writeDBConnection;
    }

    public static function connectReadDB() {

      if (self::$readDBConnecton === null) {
        self::$readDBConnecton = new mysqli(self::$servername, self::$username, self::$password, self::$database);
        if (!self::$readDBConnecton) {
        	echo "Connection error: "+mysqli_connect_error(); // OPTIMIZE: What to do if DB connection fails?
        } else {
        	//echo "Connection to ReadDB successful!";
        }
      }
      return self::$readDBConnecton;
    }

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


/*
    public static function connectWriteDB_Mike() {

      echo "12 ";
      if (self::$writeDBConnection === null) {
      echo "13 ";
        // Using PDO to make implementation universal across different DBs (mySQL, MSSQL, etc.)
        self::$writeDBConnection = new PDO('mysql:host=localhost;dbname=tasksdb;charset=utf8', 'root', 'root');
        echo "14 ";
        self::$writeDBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // To catch exceptions
        echo "15 ";
        self::$writeDBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // To use placeholder in SQL statements - MySQL doesn't need to emulate
        echo "16 ";
      }
      return self::$writeDBConnection;
    }

    public static function connectReadDB_Mike() {
      if (self::$readDBConnecton === null) {
        // Using PDO to make implementation universal across different DBs (mySQL, MSSQL, etc.)
        self::$readDBConnecton = new PDO('mysql:host=localhost;dbname=taskdb;utf8', 'root', 'root');
        self::$readDBConnecton->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // To catch exceptions
        self::$readDBConnecton->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // To use placeholder in SQL statements - MySQL doesn't need to emulate
      }
      return self::$readDBConnecton;
    }
*/


  }


?>
