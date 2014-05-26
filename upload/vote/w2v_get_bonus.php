<?php

require("../system.php");
loadTool('user.class.php');

BDConnect('w2vote');

if ($_GET['nickname']!='') {
  $name=$_GET['nickname'];
  $user = new User($name, $bd_users['login']); 
  if(!$user->id()) die;// Если такого юзера нет, то УМРИ!!!
  $sql = BD("SELECT `vote` FROM `{$bd_names['users']}` WHERE `{$bd_users['login']}`='".$user->name()."'");
  $query = mysql_fetch_array($sql);
  $query = $query['vote'];
  $query++;
  if($query%10 != 0)
    if ($donate['vote_real']) $user->addMoney($donate['vote']);
      else $user->addEcon($donate['vote']);
    else
      if ($donate['vote_real']) $user->addMoney($donate['vote10']);
        else $user->addEcon($donate['vote10']);
  
  BD("UPDATE `{$bd_names['users']}` SET `vote`=`vote`+1 WHERE `{$bd_users['login']}`='".$user->name()."'");
}
?>