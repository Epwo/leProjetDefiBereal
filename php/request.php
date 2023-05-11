<?php
  require_once('database.php');

  // Enable all warnings and errors.
  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  // // Database connection.
  $db = dbConnect();
  
  // Handle channels request.
  $request = $_GET['request'];
  $method = $_SERVER['REQUEST_METHOD'];

    

  echo json_encode($data);
?>
