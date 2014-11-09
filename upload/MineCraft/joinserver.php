<?php

require('../system.php');

if (empty($_GET['sessionId']) or empty($_GET['user']) or empty($_GET['serverId'])) {

vtxtlog("[joinserver.php] join process [GET parameter empty] [ ".((empty($_GET['sessionId']))? 'SESSIONID ':'').((empty($_GET['user']))? 'USER ':'').((empty($_GET['serverId']))? 'SERVERID ':'')."]");
exit('Bad login');
}	

loadTool('user.class.php');
$db = new DB();
$db->connect('joinserver');

$login 		= $_GET['user']; 
$serverid	= $_GET['serverId'];
$sessionid	= $_GET['sessionId'];

$sessionidv16 =  explode (":", $sessionid);

if ( ($sessionidv16[0] == "token") && ($sessionidv16[2] == "2") ){
    $sessionid = $sessionidv16[1];
}

if (!preg_match("/^[a-zA-Z0-9_-]+$/", $login) or 
	!preg_match("/^[0-9]+$/", $sessionid) or
	!preg_match("/^[a-z0-9_-]+$/", $serverid)) {
		
	vtxtlog("[joinserver.php] error while login process [input login ".$login." sessionid ".$sessionid." serverid ".$serverid."]");
	exit('Bad login'); 		
}	

$tmp_user = new User($login, $bd_users['login']);
if ($tmp_user->id() === false or $tmp_user->name() !== $login)  {

vtxtlog("[joinserver.php] Bad login register");
exit ('Bad login');
}
	
$result = $db->execute("SELECT `{$bd_users['login']}` FROM `{$bd_names['users']}` WHERE `{$bd_users['session']}`='". $db->safe($sessionid) ."' AND `{$bd_users['login']}`='". $db->safe($login) ."' AND `{$bd_users['server']}`='". $db->safe($serverid) ."'");

if( $db->num_rows($result) == 1 ){
	vtxtlog('[joinserver.php] join Server [Result] Relogin OK'); 
	exit('OK');
} 

$result = $db->execute("UPDATE `{$bd_names['users']}` SET `{$bd_users['server']}`='". $db->safe($serverid) ."' WHERE `{$bd_users['session']}`='". $db->safe($sessionid) ."' AND `{$bd_users['login']}`='". $db->safe($login) ."'");

if(mysql_affected_rows() == 1){
	vtxtlog('[joinserver.php] join Server [Result] login OK'); 
	exit('OK');
}

vtxtlog("[joinserver.php] join Server [Result] Bad Login - input Session [$sessionid] User [$login] Server [$serverid]");
exit('Bad login');
?>