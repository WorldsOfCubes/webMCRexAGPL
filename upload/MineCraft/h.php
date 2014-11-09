<?php
        error_reporting(0);
	define('INCLUDE_CHECK',true);
	include ("connect.php");
	@$user = mysql_real_escape_string($_GET['username']);
	@$serverid = mysql_real_escape_string($_GET['serverId']);
	
	if (!preg_match("/^[a-zA-Z0-9_-]+$/", $user) || !preg_match("/^[a-zA-Z0-9_-]+$/", $serverid)){

		echo '{"error":"Bad login","errorMessage":"Bad login"}';	
		
	exit;
    }

	$query = mysql_query("Select $db_columnUser From $db_table Where $db_columnUser='$user'") or die ("Ошибка");
	$row = $db->fetch_assoc($query);
	$realUser = $row[$db_columnUser];

	if ($user !== $realUser)
    {
    exit ('{"error":"Bad login","errorMessage":"Bad login"}');
    }	
	
	$result = mysql_query("Select $db_columnUser From $db_table Where $db_columnUser='$user' And $db_columnServer='$serverid'") or die (mysql_error());

	if($db->num_rows($result) == 1) echo '{"id":"ok"}';
	else echo '{"error":"Bad login","errorMessage":"Bad login"}';
?>
