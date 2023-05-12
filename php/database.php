<?php
  require_once('constants.php');


  function dbPubImg($db,$definb,$time,$login,$photo){
    try{
        mkdir("../db/".$login);
        $file = fopen("../db/".$definb."/".$login."/timestamp.txt", "w");
        if(fwrite($file, $time)==FALSE){
            echo "error writing timestamp";
        }

        $tempPath = $photo['tmp_name'];
        $destination = "../db/".$definb."/".$login."/image.jpg";
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


  function dbGetWinnerOfDefi($db,$nbDefi){
    try{
        $directory = "../db/".$definb."/";  // Path to the current folder

        $folders = array_filter(scandir($directory), function($item) use ($directory) {
            return is_dir($directory . $item) && !in_array($item, ['.', '..']);
        });
        $Winner = [];
        //aller regarder dans tt les fichiers pour trouver le vainqueur
        //btw pire facon de faire ca avec des fichiers et pas une bd sql ..

        foreach ($folders as $folder) {
            $handle = fopen($directory.$folder."/timestamp.txt",'r');
            $timestamp = fread($handle, filesize('timestamp.txt'));
            if($timestamp < $Winner[0]){
                $Winner[1] = $folder;
                $Winner[0] = $timestamp;
            }
            fclose($handle);
        }

        //on va aller récup l'image, puis la mettre dans Winner[3] puis on send.
        $imageData = file_get_contents($directory.$Winner[1]."/image.jpg");
        $base64Image = base64_encode($imageData);
        $Winner[3] = $base64Image;
    }
    catch (PDOException $exception){
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $Winner;
  }

?>
