<?php

include('../system.php');
$db = new DB();
$db->connect('pay');

loadTool('log.class.php');
function ikSign($params, $ikKey) {
	// удаляем ненужные параметры
	unset($params['ik_sign']);
	foreach ($params as $key => $value)
		if (!preg_match("/^ik_/is", $key))
			unset($params[$key]);

	ksort($params, SORT_STRING);
	array_push($params, $ikKey);
	$sign = implode(":", $params);
	$sign = base64_encode(md5($sign, true));
	return $sign;
}


$kassaId = trim($_POST['ik_co_id']);
$paymentId = trim(strip_tags($_POST['ik_pm_no']));
$summ = intval($_POST['ik_am']);
$paySystem = trim($_POST['ik_pw_via']);
$payStatus = trim($_POST['ik_inv_st']);
$sign = trim($_POST['ik_sign']);
$ik_payment_timestamp = trim($_POST['ik_inv_prc']);
$secretKey = $donate['ik_secret_key'];
// тестирование
if ($donate['ik_testing'] and ($paySystem == "test_interkassa_test_xts")) {
	$secretKey = $donate['ik_secret_key_test'];
} elseif ($paySystem == "test_interkassa_test_xts") {
	vtxtlog($ik_payment_timestamp."\t$paymentId не произвел тестовый платеж на $summ руб");
	exit("OK");
}

if ($kassaId != $donate['ik_shop_id'])
	exit("Неверный ID кассы");
if ($sign != ikSign($_POST, $secretKey)) {
	Logs::write($ik_payment_timestamp."\tНеверная подпись: $sign $summ ");
	exit("Bad sign");
}
loadTool('user.class.php');
$user = new User($paymentId, $bd_users['login']);
$user->addMoney($summ);
//$db->execute("UPDATE `{$bd_names['iconomy']}` SET `{$bd_money['bank']}`=`{$bd_money['bank']}`+$summ WHERE `{$bd_money['login']}`='$paymentId'");


vtxtlog($ik_payment_timestamp."\t$paymentId произвел платеж на $summ руб");
echo "ok";