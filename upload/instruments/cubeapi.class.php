<?php
        class CubeAPI {
             
                public $mcUsername = '';
                public $sessionID = '';
             
                public function login($username, $password, $version=13){
                        $mcSocket = fopen("http://cubesworld.tk/MineCraft/auth.php?user=$username&password=$password&version=$version", "rb");
                        $mcOutput = '';
                        while (!feof($mcSocket)) {
                                $mcOutput .= fgets($mcSocket, 128);
                        }
                        fclose($mcSocket);
                        // Проверяем ответ сайта minecraft.net
                        if(strpos($mcOutput, 'Bad login') === false){
                                $mcValues = explode(':', $mcOutput);
                                if(count($mcValues) > 0){
                                        // берем нужные нам данные из ответа сайта minecraft.net
                                        $this->mcUsername = $mcValues[2]; //Ник игрока
                                        $this->sessionID = $mcValues[3]; //Сессия
                                        return true;
                                }else{
                                        return false;
                                }
                             
                        }else{
                                return false;
                        }
                }
     
        }    
?>