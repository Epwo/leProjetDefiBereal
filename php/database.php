<?php
  require_once('constants.php');

  function dbPubImg($definb,$time,$login,$photo){
    try{
        mkdir("../db/defi_".$defiNb.'/'.$login);
        $file = fopen("../db/defi_".$definb."/".$login."/timestamp.txt", "w");
        if(fwrite($file, $time)==FALSE){
            echo "error writing timestamp";
        }
        fclose($file);
        
        base64_to_jpeg($photo,"../db/defi_".$definb."/".$login."/image.jpg");

        
    }
    catch(PDOException $exception){
      return false;
    }
    return true;
  }


  function dbCreateDefi($chrono,$consigne,$nbPts){
    try{
      $directory = "../db/";  // Path to the current folder
      $folders = array_filter(glob($directory . '*'), 'is_dir');
      $arrayDefis= [];

      foreach ($folders as $folder) {
        $defiNb = str_replace('../db/defi_','',$folder);
        array_push($arrayDefis,$defiNb);
      }
      $nbDef = max($arrayDefis)+1;

      mkdir("../db/defi_".$nbDef);

      echo 'Created defi '.$nbDef;
      echo '<br>';
      
      $file = fopen("../db/defi_".$nbDef."/consigne.txt", "w");
      $consigne = str_replace('_',' ',$consigne);
      fwrite($file, $consigne);

      echo 'Add consigne.txt such as <b>'.$consigne.'</b>';
      echo '<br>';

      mkdir("../db/defi_".$nbDef.'/personne@isen-ouest.yncrea.fr');
      fclose($file);
      $file = '../db/defi_1/personne@isen-ouest.yncrea.fr/image.jpg';
      $newfile = "../db/defi_".$nbDef.'/personne@isen-ouest.yncrea.fr/image.jpg';
      copy($file, $newfile);
      $file = '../db/defi_1/personne@isen-ouest.yncrea.fr/timestamp.txt';
      $newfile = "../db/defi_".$nbDef.'/personne@isen-ouest.yncrea.fr/timestamp.txt';
      copy($file, $newfile);
      echo 'created "personne" and added image';
      echo '<br>';

      $file = fopen("../db/defi_".$nbDef.'/createdtime.txt','w');
      $time = time();
      fwrite($file,$time);
      fclose($file);
      echo 'created at '.$time;
      echo '<br>';
      $file = fopen("../db/defi_".$nbDef.'/timetomake.txt','w');
      $chronoTimestamp = $chrono * 60;
      fwrite($file,$chronoTimestamp);

      echo 'written timetomake as '.$chronoTimestamp;
      echo'<br>';
      
      $file = fopen("../db/defi_".$nbDef.'/nbPts.txt','w');
      fwrite($file,$nbPts);
      fclose($file);
      echo'defi for'.$nbPts.' points';

      
    }
    catch(PDOException $exception){
      return false;
    }
    return true;
  }
  

  function dbGetCurrentDefi(){
    try{
      $directory = "../db/";  // Path to the current folder

      $folders = array_filter(scandir($directory), function($item) use ($directory) {
          return is_dir($directory . $item) && !in_array($item, ['.', '..']);
      });

      $DefiList = [];
      $currentTime = time();
      //aller regarder dans tt les fichiers pour trouver le vainqueur
      //btw pire facon de faire ca avec des fichiers et pas une bd sql ..

      foreach ($folders as $folder) {
          $handleCreated = fopen($directory.$folder."/createdtime.txt",'r');
          $timestamp = fread($handleCreated, filesize($directory.$folder."/createdtime.txt"));

          $handleChrono = fopen($directory.$folder.'/timetomake.txt','r');
          $timeChrono = fread($handleChrono, filesize($directory.$folder."/timetomake.txt"));
          
          $handle = fopen($directory.$folder."/consigne.txt",'r');

          $handlePts = fopen($directory.$folder.'/nbPts.txt','r');
          if(($timestamp + $timeChrono )>= $currentTime){
            $DefiList['nb'] = str_replace('defi_','',$folder);
            $DefiList['timeLeft'] = ($timestamp + $timeChrono )- $currentTime;
            $consigne = fread($handle, filesize($directory.$folder."/consigne.txt"));
            $DefiList['consigne'] = $consigne;
            $nbpts = fread($handlePts, filesize($directory.$folder.'/nbPts.txt'));
            $DefiList['nbpts'] = $nbpts;
          }
          fclose($handleChrono);
          fclose($handleCreated);
          fclose($handle);
      }
      if(!isset($DefiList['nb'])){
        $DefiList['nb'] = 'none';
      }

      //on va aller récup l'image, puis la mettre dans Winner[3] puis on send.

      
  }
  catch(PDOException $exception){
    return false;
  }
  return $DefiList;
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
        $Winner['user'] = str_replace('.',' ',str_replace('@isen-ouest.yncrea.fr','',$Winner['user']));
        $Winner['image'] = $base64Image;
    }
    catch (PDOException $exception){
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $Winner;
  }


  function dbGetAllDefis(){
    try{
        $directory = "../db/";  // Path to the current folder

        $folders = array_filter(glob($directory . '*'), 'is_dir');

        $arrayDefis= [];
        //aller regarder dans tt les fichiers pour trouver le vainqueur
    
        foreach ($folders as $folder) {
          $defiNb = str_replace('../db/defi_','',$folder);
          $arrayDefis[$defiNb] = [];
          $handle = fopen($directory.$folder."/consigne.txt",'r');
          $consigne = fread($handle, filesize($directory.$folder."/consigne.txt"));
          $arrayAll[$defiNb]['consigne'] = $consigne;
          fclose($handle);
        }

    }
    catch (PDOException $exception){
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $arrayAll;
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

  function base64_to_jpeg($base64_string, $output_file) {
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp ); 

    return $output_file; 
}

?>
