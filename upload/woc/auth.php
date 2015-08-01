<?php
require('../system.php');
if (!isset($config['woc_id']) or !isset($config['security_key']))
	die('NOT_CONFIGURED');
$db = new DB();
$db->connect('WoCAuth');
loadTool('user.class.php');
if (isset($_POST['user']) and isset($_POST['user_id']) and isset($_POST['mail']) and isset($_POST['tmp']) and isset($_POST['female']) and isset($_POST['hash'])) {
	if (md5(md5($config['security_key'] . ":" . $_POST['female'] . ":" . $_POST['mail'] . ":" . $_POST['user'] . ":" . $_POST['tmp'])) != $_POST['hash'])
		exit("BAD_HASH");
	$user = new User($_POST['user'], $bd_users['login']);
	if ($user->id() and $user->lvl() < 2)
		$user->Delete();
	$user = new User($_POST['mail'], $bd_users['email']);
	if ($user->id() and $user->lvl() < 2)
		$user->Delete();
	$user = new User($_POST['user_id'], 'wocid');
	(!$user->id()) ?
		$db->execute("INSERT INTO `{$bd_names['users']}` (`wocid`," . "`{$bd_users['login']}`," . "`{$bd_users['tmp']}`," . "`{$bd_users['email']}`," . "`{$bd_users['female']}`) " . "VALUES('{$_POST['user_id']}','{$_POST['user']}','{$_POST['tmp']}','{$_POST['mail']}','{$_POST['female']}');") :
		$db->execute("UPDATE `{$bd_names['users']}` SET `{$bd_users['tmp']}`='{$_POST['tmp']}' WHERE `{$bd_users['id']}`=" . $user->id());
	$mysql_error = $db->error();
	if ($mysql_error != "")
		exit ("MySQL_ERROR: " . $mysql_error);
	echo "OK";
} elseif (isset($_POST['user']) and isset($_POST['user_id']) and isset($_POST['mail']) and isset($_POST['hash'])) {
	if (md5(md5($config['security_key'] . ":" . $_POST['user'] . ":" . $_POST['user_id'] . ":" . $_POST['mail'])) != $_POST['hash'])
		exit("BAD_HASH");
	if (isset($_POST['additional'])) {
		$user = new User($_POST['additional'], 'woctoken');
		($user->id()) ?
			$db->execute("UPDATE `{$bd_names['users']}` SET `wocid`='{$_POST['user_id']}', `woctoken`=NULL WHERE `{$bd_users['id']}`=" . $user->id()) :
			exit('BAD_TOKEN');
		echo 'OK';
	} else {
		$user = new User((int)$_POST['user_id'], 'wocid');
		if (!$user->id()) {
			$user = new User($_POST['user'], $bd_users['login']);
			$err = ($user->id() and $user->lvl() > 1) ? 'EXISTS_NOT_CONNECTED' : 'OK';
			$user = new User($_POST['mail'], $bd_users['email']);
			echo ($user->id() and $user->lvl() > 1) ? 'MAIL_EXISTS_NOT_CONNECTED' : $err;
		} else echo "OK";
	}
} elseif (isset($_GET['cookie'])) {
	session_start();
	$user = new User($_GET['cookie'], $bd_users['tmp']);
	$user->login(randString(rand(16, 32)), GetRealIp(), true);
	header("Location: " . BASE_URL);
} elseif (isset($_GET['cookie4'])) {
	$user = new User($_GET['cookie4'], $bd_users['tmp']);
	$user->login(randString(rand(16, 32)), GetRealIp(), true);
	?>
	<html>
	<body onload="CloseAndRefresh()">
	now page must refresh...
	<script language="JavaScript">
		function CloseAndRefresh() {
			opener.document.location.reload(true);
			self.close();
		}
	</script>
	</body>
	</html>
	<?php
} else echo("BAD_REQUEST");