<?php

include('../system.php');
$db = new DB();
$db->connect('pay');
loadTool('log.class.php');
loadTool('user.class.php');
function upSign($params, $upKey){
	// удаляем ненужные параметры
	unset($params['sign']);

	array_push($params, $upKey);
	ksort($params, SORT_STRING);
	$sign = implode(":", $params);
	$sign = md5($sign);
	return $sign;
}
$params = $_GET['params'];
if($params['sign'] != upSign($params, $donate['up_secret_key'])) {
	Logs::write($params['date']."\tНеверная подпись: {$params['account']} {$params['orderSum']} ");
	exit('{"error": {"message": "Неверная подпись"}}');
}
if($_GET['method'] != 'pay') exit ('{"result": {"message":"Запрос успешно обработан"}}');
$user = new User($params['account']);
if(!$user->id()) exit ('{"error": {"message": "Пользователь не найден"}}');
$user->addMoney($params['orderSum']);

//vtxtlog($ik_payment_timestamp."\t$paymentId произвел платеж на $summ руб");
echo '{"result": {"message":"Запрос успешно обработан"}}';