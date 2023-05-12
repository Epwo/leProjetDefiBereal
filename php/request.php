<?php
  require_once('database.php');

  // Enable all warnings and errors.
  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  // // Database connection.
  // Handle channels request.
  $request = $_GET['request'];
  $method = $_SERVER['REQUEST_METHOD'];


  if($method == 'POST'){
    if($request == 'publi' && isset($_POST['NbDefi']) && isset($_POST['time']) && isset($_POST['user']) && isset($_POST['img'])){
      $definb = intval($_POST['NbDefi']);
      $photo = $_FILES['img'];
      $time = $_POST['time'];
      $login = 'user';
      $data = dbPubImg($definb,$time,$login,$photo);
    }
  }
  if($method == 'GET'){
    if($request == 'getFirstOfDefi' && isset($_GET['nbDefi'])){
      $nbDefi = "defi_".$_GET['nbDefi'];
      $data = dbGetWinnerOfDefi($nbDefi);
    }
    elseif($request == 'getAll' && isset($_GET['nbDefi'])){
      $nbDefi = "defi_".$_GET['nbDefi'];
      $data = dbGetAllOfDefi($nbDefi);
    }
}
    

  echo json_encode($data);
?>
