<?php
    header('Content-Type: text/html; charset=cp1251');
	define('INCLUDE_CHECK',true);
	error_reporting(0);
	include("connect.php");
	include("loger.php");
	$action = mysql_real_escape_string($_POST['action']);
	$user = mysql_real_escape_string($_POST['user']);
	$password = mysql_real_escape_string($_POST['password']);
	$password2 = mysql_real_escape_string($_POST['password2']);
	$mail = mysql_real_escape_string($_POST['email']);
	$ip  = getenv('REMOTE_ADDR');
if($action == 'register' && !$usecheck) die("registeroff");	
if($action == 'register')
{
if(strlen($user) == 0){die("errorField");}
elseif(strlen($password) == 0){die("errorField");}
elseif(strlen($password2) == 0){die("errorField");}
elseif(strlen($mail) == 0){die("errorField");}

if(!preg_match("/^([0-9a-z_-]|(\/|\?|\\|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\=|\+|\||\;|\:|\.|\,|\~|))*$/i", $user)) die ("errorLoginSymbol");
elseif(!preg_match("/^([0-9a-z_-])*$/i", $password)) die ("passErrorSymbol");
 elseif (!preg_match("[@]",$mail))die ("errorMail");
if ((strlen($user) < 2) or (strlen($user) > 20)) die ("errorSmallLogin");
if ((strlen($password) < 4) or (strlen($password) > 20)) die ("errorPassSmall");
if($password != $password2) die("errorPassToPass");


$eMailProverka = BD("SELECT $db_columnMail FROM $db_table WHERE $db_columnMail ='{$mail}'") or die("error.".$logger->WriteLine($log_date.mysql_error())); //вывод ошибок MySQL в m.log
if (mysql_num_rows($eMailProverka))
 die("emailErrorPovtor");

$ProverkaUser = BD("SELECT $db_columnUser FROM $db_table WHERE $db_columnUser ='{$user}'") or die("error.".$logger->WriteLine($log_date.mysql_error())); //вывод ошибок MySQL в m.log
if (mysql_num_rows($ProverkaUser))
 die("loginErrorPovtor");

$Proverkaip = BD("SELECT $db_columnIp FROM $db_table WHERE $db_columnIp ='{$ip}'") or die("error.".$logger->WriteLine($log_date.mysql_error())); //вывод ошибок MySQL в m.log
if (mysql_num_rows($Proverkaip))
 die("Erroripip");
 
if($crypt == 'hash_md5')
{ 
$checkPass = md5($password);
}
else if($crypt == 'hash_dle')
{ 
$checkPass = md5(md5($password));
}
else die("Ошибка: тип хеша еще не поддерживается");
/*************************************/
if($useactivate)
{
BD("INSERT INTO $db_table ($db_columnUser,$db_columnPass,$db_columnMail,$db_columnDatareg,$db_columnIp,$db_table.$db_group) VALUES('$user','$checkPass','$mail',NOW(),'$ip','$noactive')") or die("error.".$logger->WriteLine($log_date.mysql_error())); //вывод ошибок MySQL в m.log
echo "done";
}
else
{
BD("INSERT INTO $db_table ($db_columnUser,$db_columnPass,$db_columnMail,$db_columnDatareg,$db_columnIp) VALUES('$user','$checkPass','$mail',NOW(),'$ip')") or die("error.".$logger->WriteLine($log_date.mysql_error())); //вывод ошибок MySQL в m.log
echo "done";
}
/*************************************/
}
?>