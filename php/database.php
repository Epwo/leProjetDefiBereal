<?php
  require_once('constants.php');


  function dbPubImg($definb,$time,$login,$photo){
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


  function dbGetWinnerOfDefi($nbDefi){
    try{
        $directory = "../db/".$nbDefi."/";  // Path to the current folder

        $folders = array_filter(scandir($directory), function($item) use ($directory) {
            return is_dir($directory . $item) && !in_array($item, ['.', '..']);
        });
        $Winner = [];
        //aller regarder dans tt les fichiers pour trouver le vainqueur
        //btw pire facon de faire ca avec des fichiers et pas une bd sql ..

        foreach ($folders as $folder) {
            $handle = fopen($directory.$folder."/timestamp.txt",'r');
            $timestamp = fread($handle, filesize($directory.$folder."/timestamp.txt"));
            if(!isset($Winner['timestamp']) || $timestamp < $Winner['timestamp']  ){
                $Winner['user'] = $folder;
                $Winner['timestamp'] = $timestamp;
            }
            fclose($handle);
        }

        //on va aller récup l'image, puis la mettre dans Winner[3] puis on send.
        $imageData = file_get_contents($directory.$Winner['user']."/image.jpg");
        $base64Image = base64_encode($imageData);
        $Winner['image'] = $base64Image;
    }
    catch (PDOException $exception){
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $Winner;
  }


  function dbGetAllOfDefi($nbDefi){
    try{
        $directory = "../db/".$nbDefi."/";  // Path to the current folder

        $folders = array_filter(scandir($directory), function($item) use ($directory) {
            return is_dir($directory . $item) && !in_array($item, ['.', '..']);
        });
        $arrayAll= [];
        //aller regarder dans tt les fichiers pour trouver le vainqueur

        foreach ($folders as $folder) {
            $arrayAll[$folder] = [];
            $handle = fopen($directory.$folder."/timestamp.txt",'r');
            $timestamp = fread($handle, filesize($directory.$folder."/timestamp.txt"));
            $arrayAll[$folder]['timestamp'] = $timestamp;
            fclose($handle);

            $imageData = file_get_contents($directory.$folder."/image.jpg");
            $base64Image = base64_encode($imageData);
            $arrayAll[$folder]['image']= $base64Image;
        }

    }
    catch (PDOException $exception){
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $arrayAll;
  }

?>
