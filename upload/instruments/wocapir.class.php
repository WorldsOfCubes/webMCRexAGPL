<?php
        class WoCAPIr {
             
                public $error = '';
             
                public function register($login, $pass, $repass, $female, $email){
                        $mcSocket = fopen("http://cubesworld.tk/registerout.php?login=$login&pass=$pass&repass=$repass&female=$female&email=$email", "rb");
                        $mcOutput = '';
                        while (!feof($mcSocket)) {
                                $mcOutput .= fgets($mcSocket, 128);
                        }
                        fclose($mcSocket);
                        // Проверяем ответ сайта WorldsOfCubes.RU
                        if(strpos($mcOutput, 'OK') === true){
                                return true;
                        }else{
                                $this->error = $mcOutput;
                                return false;
                        }
                }
     
        }    
?>