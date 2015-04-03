<?php
require('../system.php');

$db = new DB();
$db->connect('WoCAuth');
if (isset($_POST['user']) and isset($_POST['mail']) and isset($_POST['tmp']) and isset($_POST['female']) and isset($_POST['hash'])) {
	//	$config['security_key'] = 'ipy0n1vvmt03h536';
	if (md5(md5($config['security_key'].":".$_POST['female'].":".$_POST['mail'].":".$_POST['user'].":".$_POST['tmp'])) != $_POST['hash'])
		exit("bad hash");
	$db->execute("INSERT INTO `{$bd_names['users']}` ("."`{$bd_users['login']}`,"."`{$bd_users['tmp']}`,"."`{$bd_users['email']}`,"."`{$bd_users['female']}`) "."VALUES('{$_POST['user']}','{$_POST['tmp']}','{$_POST['mail']}','{$_POST['female']}') "."ON DUPLICATE KEY UPDATE `{$bd_users['login']}`='{$_POST['user']}', `{$bd_users['tmp']}`='{$_POST['tmp']}', `{$bd_users['email']}`='{$_POST['mail']}', `{$bd_users['female']}`='{$_POST['female']}'");
	$mysql_error = $db->error();
	if ($mysql_error != "")
		exit ("MySQL ERROR: ".$mysql_error);
	echo "OK";
} elseif (isset($_GET['cookie'])) {
	session_start();
	setcookie("PRTCookie1", $_GET['cookie'], time() + 60 * 60 * 24 * 30 * 12, '/');
	header("Location: ".BASE_URL);
} else echo("bad request");