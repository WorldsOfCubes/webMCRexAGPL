<?php
define('INCLUDE_CHECK',true);
include("connect.php");
//include("loger.php");
if (($_SERVER['REQUEST_METHOD'] == 'POST' ) && (stripos($_SERVER["CONTENT_TYPE"], "application/json") === 0)) {
    $json = json_decode($HTTP_RAW_POST_DATA);
    
} else {

}

@$aT = $json->accessToken; @$sP = @$json->selectedProfile; @$sI = $json->serverId;
@$user                      = $db->safe($aT);
@$sessionid                 = $db->safe($sP);
@$serverid                  = $db->safe($sI);
//$logger->WriteLine($user.' '.$sessionid.' '.$serverid);

if (!preg_match("/^[a-zA-Z0-9_-]+$/", $user) || !preg_match("/^[a-zA-Z0-9:_-]+$/", $sessionid) || !preg_match("/^[a-zA-Z0-9_-]+$/", $serverid)){

echo '{"error":"Bad login","errorMessage":"Bad login"}';
exit;
}
	
	$query = $db->execute("Select $db_columnUser From $db_table Where $db_columnUser='$user'") or die ("Ошибка");
	$row = $db->fetch_assoc($query);
	$realUser = $row[$db_columnUser];

	if ($user !== $realUser)
        {
         exit ('{"error":"Bad login","errorMessage":"Bad login"}');
        }
	
	$result = $db->execute("Select $db_columnUser From $db_table Where $db_columnSesId='$sessionid' And $db_columnUser='$user' And $db_columnServer='$serverid'") or die ("Ошибка");
	if($db->num_rows($result) == 1) echo '{"id":"ok"}';
	else
	{
		$result = $db->execute("Update $db_table SET $db_columnServer='$serverid' Where $db_columnSesId='$sessionid' And $db_columnUser='$user'") or die ("Ошибка");
		if($db->affected_rows() == 1) echo '{"id":"ok"}';
		else echo '{"error":"Bad login","errorMessage":"Bad login"}';
	}
?>
