<?php

require("../system.php");
loadTool('user.class.php');

$db = new DB();
$db->connect('w2vote');

if ($_GET['nickname'] != '') {
	$name = $_GET['nickname'];
	$user = new User($name, $bd_users['login']);
	if (!$user->id())
		die;// Если такого юзера нет, то УМРИ!!!
	$sql = $db->execute("SELECT `vote` FROM `{$bd_names['users']}` WHERE `{$bd_users['login']}`='".$user->name()."'");
	$query = $db->fetch_array($sql);
	$query = $query['vote'];
	$query++;
	if ($query % 10 != 0)
		if ($donate['vote_real'])
			$user->addMoney($donate['vote']); else $user->addEcon($donate['vote']); else if ($donate['vote_real'])
		$user->addMoney($donate['vote10']); else $user->addEcon($donate['vote10']);

	$db->execute("UPDATE `{$bd_names['users']}` SET `vote`=`vote`+1 WHERE `{$bd_users['login']}`='".$user->name()."'");
}
?>