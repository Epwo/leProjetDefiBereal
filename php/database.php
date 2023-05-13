<?php
  require_once('constants.php');

  function dbPubImg($definb,$time,$login,$photo){
    try{
        mkdir("../db/".$login);
        $file = fopen("../db/defi_".$definb."/".$login."/timestamp.txt", "w");
        if(fwrite($file, $time)==FALSE){
            echo "error writing timestamp";
        }
        fclose($file);
        
        base64_to_jpeg($photo,"../db/defi_".$definb."/".$login."/image1.jpg");

        
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


  function dbGetAllDefis(){
    try{
        $directory = "../db/";  // Path to the current folder

        $folders = array_filter(scandir($directory), function($item) use ($directory) {
            return is_dir($directory . $item) && !in_array($item, ['.', '..']);
        });
        $arrayDefis= [];
        //aller regarder dans tt les fichiers pour trouver le vainqueur

        foreach ($folders as $folder) {
            $arrayDefis[$folder] = [];
            $handle = fopen($directory.$folder."/consigne.txt",'r');
            $consigne = fread($handle, filesize($directory.$folder."/consigne.txt"));
            $arrayAll[$folder]['consigne'] = $consigne;
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
