<?php

include('../system.php');
$db = new DB();
$db->connect('pay');
loadTool('log.class.php');
loadTool('user.class.php');
function upSign($params, $upKey){
	ksort($params);
	unset($params['sign']);
	return md5(join(null, $params).$upKey);
}
$params = $_GET['params'];
if($params['sign'] != upSign($params, $donate['up_secret_key'])) {
	Logs::write($params['date']."\tНеверная подпись: {$params['account']} {$params['orderSum']} ");
	exit('{"error": {"message": "Неверная подпись"}}');
}
if($_GET['method'] != 'pay') exit ('{"result": {"message":"Запрос успешно обработан"}}');
$user = new User($params['account'], $bd_users['login']);
if(!$user->id()) exit ('{"error": {"message": "Пользователь не найден"}}');
$user->addMoney($params['orderSum']);

//vtxtlog($ik_payment_timestamp."\t$paymentId произвел платеж на $summ руб");
echo '{"result": {"message":"Запрос успешно обработан"}}';