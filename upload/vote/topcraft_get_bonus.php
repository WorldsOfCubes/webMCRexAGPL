<?php
//Скрипт поощрений за голосования рейтинга TopCraft.RU для webMCRex. ©KobaltMR
require('../system.php');
loadTool('user.class.php');
$db = new DB();
$timestamp = $_POST['timestamp']; //Передает время, когда человек проголосовал за проект
$username = htmlspecialchars($_POST['username']); //Передает Имя проголосовавшего за проект
$db->connect('topcraft');
if ($username !='') {
  $name=$username;
  $user = new User($name, $bd_users['login']); 
  if(!$user->id()) die;// Если такого юзера нет, то УМРИ!!!
  if ($_POST['signature'] != sha1($username.$timestamp.$donate['vote_topcraft_secret_key'])) die("Не верный секретный ключ!");
  $sql = $db->execute("SELECT `vote` FROM `{$bd_names['users']}` WHERE `{$bd_users['login']}`='".$user->name()."'");
  $query = $db->fetch_array($sql);
  $query = $query['vote'];
  $query++;
  if($query%10 != 0)
    if ($donate['vote_real'])
	{
		$user->addMoney($donate['vote']);
		$db->execute("UPDATE `{$bd_names['users']}` SET `vote`=`vote`+1 WHERE `{$bd_users['login']}`='".$user->name()."'");
	}else{
		$user->addEcon($donate['vote']);
		$db->execute("UPDATE `{$bd_names['users']}` SET `vote`=`vote`+1 WHERE `{$bd_users['login']}`='".$user->name()."'");
	}elseif ($donate['vote_real']) {
		$user->addMoney($donate['vote10']);
		$db->execute("UPDATE `{$bd_names['users']}` SET `vote`=`vote`+1 WHERE `{$bd_users['login']}`='".$user->name()."'");
	}else{
		$user->addEcon($donate['vote10']);
		$db->execute("UPDATE `{$bd_names['users']}` SET `vote`=`vote`+1 WHERE `{$bd_users['login']}`='".$user->name()."'");
	}
	echo 'OK<br />';
}
?>