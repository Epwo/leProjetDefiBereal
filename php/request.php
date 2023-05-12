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


  if($method == 'POST'){
    if($request == 'publi' && $method = 'POST' && isset($_POST['NbDefi']) && isset($_POST['time']) && isset($_POST['user']) && isset($_POST['img'])){
      $definb = intval($_POST['NbDefi']);
      $photo = $_FILES['img'];
      $time = $_POST['time'];
      $login = 'user';
      $data = dbPubImg($db,$definb,$time,$login,$photo);
    }
  }
  elseif($method == 'GET' && $request = 'getFirstOfDefi' && isset($_GET['nbDefi'])){
    $nbDefi = intval($_GET['nbDefi']);
    $data = dbGetWinnerOfDefi($db,$nbDefi);
  }
    

  echo json_encode($data);
?>
