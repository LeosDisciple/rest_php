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

}

?>
