<?PHP
class Logs{
                //Log::write('message', 'file');
                static function write($mess="", $name="pay"){
//                        if(strlen(trim($mess)) < 2){
//                                return fasle;
//                        }
  //                      if(preg_match("/^([_a-z0-9A-Z]+)$/i", $name, $matches)){
                                $file_path = $_SERVER['DOCUMENT_ROOT'].'/'.$name.'.log.txt';
                                $text = htmlspecialchars($mess)."\r\n";
                                $handle = fopen($file_path, "a");
//                                @flock ($handle, LOCK_EX);
                                fwrite ($handle, $text);
                                fwrite ($handle, "==============================================================\r\n\r\n");
  //                              @flock ($handle, LOCK_UN);
                                fclose($handle);
                                return true;
//                        }
//                        else{
    //                            return false;
  //                      }
                }
        }
?>