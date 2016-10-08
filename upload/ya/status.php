<?php

include('../system.php');
$db = new DB();
$db->connect('pay');

loadTool('log.class.php');

if (!isset($_POST['notification_type']) or !isset($_POST['operation_id']) or !isset($_POST['amount']) or !isset($_POST['currency']) or !isset($_POST['datetime']) or !isset($_POST['sender']) or !isset($_POST['codepro']) or !isset($_POST['label']) or !isset($_POST['sha1_hash'])) {
	header("HTTP/1.0 480 BadData");
	exit('bad data');
}
$notification_type = $_POST['notification_type'];
$operation_id = $_POST['operation_id'];
$amount = $_POST['amount'];
$currency = $_POST['currency'];
$datetime = $_POST['datetime'];
$sender = $_POST['sender'];
$codepro = $_POST['codepro'];
$label = $_POST['label'];
$sha1_hash = $_POST['sha1_hash'];

if (isset($_POST['unaccepted']) and $_POST['unaccepted']) {
	header("HTTP/1.0 481 Unaccepted");
	exit('bad hash');
}

if ($sha1_hash != hash('sha1', $notification_type . '&' . $operation_id . '&' . $amount . '&' . $currency . '&' . $datetime . '&' . $sender . '&' . $codepro . '&' . $donate['ya_secret_key'] . '&' . $label)) {
	header("HTTP/1.0 481 BadHash");
	exit('bad hash');
}

loadTool('user.class.php');
$user = new User($label, $bd_users['login']);

if ($user->id() == -1) {
	header("HTTP/1.0 482 BadUser");
	exit('bad user');
}
$user->addMoney($amount);
//$db->execute("UPDATE `{$bd_names['iconomy']}` SET `{$bd_money['bank']}`=`{$bd_money['bank']}`+$summ WHERE `{$bd_money['login']}`='$paymentId'");


vtxtlog($datetime."\t$label произвел платеж на $amount руб");
header("HTTP/1.0 200 OK");
echo "ok";