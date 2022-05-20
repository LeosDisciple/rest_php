<?php

  class Response {


    public static function sendResponse($httpStatusCode, $success, $message, $data) {

      $responsData = array();
      header('Content-type: application/json;charset=utf-8');

      // Status 500 Internal server error, if success or statusCode are not set properly
      if (($success !== false && $success !== true) || !is_numeric($httpStatusCode)) { // If no proper value
        http_response_code(500);
        $responsData['statusCode'] = 500;
        $responsData['success'] = false;
        $responsData['message'] = "Response creation error";
      }
      else { // Sucessful response
        http_response_code($httpStatusCode);
        $responsData['statusCode'] = $httpStatusCode;
        $responsData['success'] = $success;
        $responsData['message'] = $message;
        $responsData['data'] = $data;
      }

      // Output
      echo json_encode($responsData);
      exit;
    }


    // Private members (_ for the members)
    private $_success;
    private $_httpStatusCode;
    private $_messages = array();
    private $_data;
    private $_toCache = false; // to cache some responses (never security relevant data)
    private $_responsData = array(); // jsonEncode with transform array into a JSON

    // Getters & Setters
    public function setSuccess($success) {
      $this->_success = $success; // no $ needed to access member
    }

    public function setHttpStatusCode($httpStatusCode) {
      $this->_httpStatusCode = $httpStatusCode;
    }

    public function addMessage($message) {
      $this->_messages[] = $message;
    }

    public function setData($data) {
      $this->_data = $data;
    }

    public function toCache($toCache) {
      $this->_toCache = $toCache;
    }

    public function send() {

      header('Content-type: application/json;charset=utf-8');

      // To tell client that he can cache this object for a while
      if ($this->_toCache == true) {
        header('Cache-control: max-age=60'); // Cache for 60s
      }
      else {
        header('Cache-control: no-cache, no-store');
      }

      // Status 500 Internal server error, if success or statusCode are not set properly
      if (($this->_success !== false && $this->_success !== true) || !is_numeric($this->_httpStatusCode)){ // If no proper value
        http_response_code(500);
        $this->_responsData['statusCode'] = 500;
        $this->_responsData['success'] = false;
        $this->addMessage("Response creation error");
        $this->_responsData['message'] = $this->_messages;
      }
      else { // Sucessful response
        http_response_code($this->_httpStatusCode);
        $this->_responsData['statusCode'] = $this->_httpStatusCode;
        $this->_responsData['success'] = $this->_success;
        $this->_responsData['message'] = $this->_messages;
        $this->_responsData['data'] = $this->_data;
      }

      // Output
      echo json_encode($this->_responsData);

    }

  }





?>
