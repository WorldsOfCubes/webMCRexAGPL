<?php

header('Content-Type: text/html; charset=utf8');
define('INCLUDE_CHECK', true);
include("connect.php");
loadTool("user.class.php");
$login = $db->safe($_POST['login']);
$postPass = $db->safe($_POST['password']);
$client = $db->safe($_POST['client']);
$action = $db->safe($_POST['action']);


if (!preg_match("/^[a-zA-Z0-9_-]+$/", $login) || !preg_match("/^[a-zA-Z0-9_-]+$/", $postPass) || !preg_match("/^[a-zA-Z0-9_-]+$/", $action)) {

	echo "errorLogin";

	exit;
}
$user = new User($login, (strpos($login, '@') === false) ? $bd_users['login'] : $bd_users['email']);
if (!$user->id())
	exit("errorLogin");
if ($user->lvl() <= 1)
	exit("Аккаунт заблокирован или не активирован");
if (!$user->authenticate($postPass))
	die("errorLogin");

if ($action == 'auth') {
	if (!file_exists("clients/".$client."/bin/client.zip") || !file_exists("clients/".$client."/bin/minecraft.jar") || !file_exists("clients/".$client."/bin/libraries.jar") || !file_exists("clients/".$client."/bin/Forge.jar") || !file_exists("clients/".$client."/bin/extra.jar") || !file_exists("clients/".$client."/mods/") || !file_exists("clients/".$client."/coremods/") || !file_exists("clients/".$client."/bin/assets.zip")
	)
		die("Ошибка: клиент $client не найден");


	if ($action == 'getpersonal')
		die("Использование ЛК выключено в webMCRex");
	if ($action == 'uploadskin')
		die("Функция недоступна");
	if ($action == 'uploadcloak')
		die("Функция недоступна");
	if ($action == 'buyvip')
		die("Функция недоступна");
	if ($action == 'buypremium')
		die("Функция недоступна");
	if ($action == 'buyunban')
		die("Функция недоступна");
	if ($action == 'exchange')
		die("Функция недоступна");
	if ($action == 'activatekey')
		die("Функция недоступна");
	$chars = "0123456789abcdef";
	$max = 32;
	$size = StrLen($chars) - 1;
	$password = null;
	while ($max--)
		$password .= $chars[rand(0, $size)];
	$chars2 = "0123456789abcdef";
	$max2 = 32;
	$size2 = StrLen($chars) - 1;
	$password2 = null;
	while ($max2--)
		$password2 .= $chars2[rand(0, $size2)];

	$sessid = "token:".$password.":".$password2;
	//$sessid 		= generateSessionId();
	$md5zip = md5_file("clients/".$client."/bin/client.zip");
	$md5czip = strtoint(xorencode($md5zip, $protectionKey));
	$md52zip = md5_file("clients/".$client."/bin/assets.zip");
	$md52czip = strtoint(xorencode($md52zip, $protectionKey));
	$md5jar = md5_file("clients/".$client."/bin/minecraft.jar");
	$md5cjar = strtoint(xorencode($md5jar, $protectionKey));
	$md5lwjql = md5_file("clients/".$client."/bin/libraries.jar");
	$md5clwjql = strtoint(xorencode($md5lwjql, $protectionKey));
	$md5lwjql_util = md5_file("clients/".$client."/bin/Forge.jar");
	$md5clwjql_util = strtoint(xorencode($md5lwjql_util, $protectionKey));
	$md5jinput = md5_file("clients/".$client."/bin/extra.jar");
	$md5cjinput = strtoint(xorencode($md5jinput, $protectionKey));
	$db->execute("UPDATE `{$bd_names['users']}` SET `{$bd_users['session']}`='".$db->safe($sessid)."', `gameplay_last`=NOW() WHERE `{$bd_users['login']}`='".$db->safe($user->name())."'") or die ("Ошибка.");
	echo "$md5czip<:>$md52czip<:>$md5cjar<:>$md5clwjql<:>$md5clwjql_util<:>$md5cjinput<:>$masterversion<br>".$user->name().'<:>'.strtoint(xorencode($sessid, $protectionKey)).'<br>';

	$colMods = 0;
	$files = scandir("clients/".$client."/mods");
	for ($i = 0; $i < sizeof($files); $i++)
		if (substr($files[$i], -4) == ".zip" || substr($files[$i], -4) == ".jar" || substr($files[$i], -8) == ".litemod") {
			echo $files[$i].":>".md5_file("clients/".$client."/mods/".$files[$i])."<:>";
			$colMods++;
		}
	if ($colMods == 0)
		;
	echo '::';
	$colCoreMods = 0;
	$coremods = scandir("clients/".$client."/coremods");
	for ($i = 0; $i < sizeof($coremods); $i++)
		if (substr($coremods[$i], -4) == ".zip" || substr($coremods[$i], -4) == ".jar") {
			echo $coremods[$i].":>".md5_file("clients/".$client."/coremods/".$coremods[$i])."<:>";
			$colCoreMods++;
		}
	if ($colCoreMods == 0)
		echo "nomods";
} else echo "Запрос составлен неверно";

//===================================== Вспомогательные функции ==================================//

function xorencode($str, $key) {
	while (strlen($key) < strlen($str)) {
		$key .= $key;
	}
	return $str ^ $key;
}

function strtoint($text) {
	$res = "";
	for ($i = 0; $i < strlen($text); $i++)
		$res .= ord($text{$i})."-";
	$res = substr($res, 0, -1);
	return $res;
}

function generateSessionId() {
	srand(time());
	$randNum = rand(1000000000, 2147483647).rand(1000000000, 2147483647).rand(0, 9);
	return $randNum;
}

function hash_drupal() {
	global $postPass, $realPass;
	$cryptPass = false;
	$setting = substr($realPass, 0, 12);
	$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$count_log2 = strpos($itoa64, $setting[3]);
	$salt = substr($setting, 4, 8);
	$count = 1 << $count_log2;
	$input = hash('sha512', $salt.$postPass, TRUE);
	do $input = hash('sha512', $input.$postPass, TRUE); while (--$count);

	$count = strlen($input);
	$i = 0;

	do {
		$value = ord($input[$i++]);
		$cryptPass .= $itoa64[$value & 0x3f];
		if ($i < $count)
			$value |= ord($input[$i]) << 8;
		$cryptPass .= $itoa64[($value >> 6) & 0x3f];
		if ($i++ >= $count)
			break;
		if ($i < $count)
			$value |= ord($input[$i]) << 16;
		$cryptPass .= $itoa64[($value >> 12) & 0x3f];
		if ($i++ >= $count)
			break;
		$cryptPass .= $itoa64[($value >> 18) & 0x3f];
	} while ($i < $count);
	$cryptPass = $setting.$cryptPass;
	$cryptPass = substr($cryptPass, 0, 55);
	return $cryptPass;
}

?>