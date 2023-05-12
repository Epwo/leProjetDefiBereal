<?php
  require_once('constants.php');


  function dbPubImg($db,$definb,$time,$login,$photo){
    try{
        mkdir("../db/".$login);
        $file = fopen("../db".$login."/".$time.".txt", "w");
        $tempPath = $photo['tmp_name'];
        $destination = "../db".$login."/".$photo.".txt";
        if(move_uploaded_file($tempPath, $destination)){
            echo 'File uploaded successfully.';
        } else {
          echo 'Error moving uploaded file.';
        }        
    }
    catch(PDOException $exception){
      return false;
    }
    return true;
  }


?>
