<?php
	if (!defined('MCR')) exit;
	$faction = $_SERVER['REQUEST_URI']; // не трогать
	$url = $_SERVER['HTTP_HOST']; // не трогать
	$db_host = $config['db_host']; //хост базы данных
	$db_user = $config['db_login']; // юзер базы данных
	$db_pass = $config['db_passw']; // пароль базы данных
	$db_base = $config['db_name']; // база данных игры
	$db_econ = 'iConomy'; // таблица плагина экономики
	$vipcash = '50'; //цена vip
	$premiumcash = '80'; //цена premium
	$prvipcash = '25'; // цена продления vip
	$prpremiumcash = '40'; // цена продления premium
	$lvlvip = 5; // Уровень доступа (lvl) vip
	$lvlprem = 6; // Уровень доступа (lvl) premium
	$idvip = 5; // ID группы vip
	$idprem = 101; // ID группы premium
	$unban = '25'; // цена первого разбана
	$exchangehow = '100'; //сколько монет давать за 1р
	$dbbonussize = '3'; //размер бонуса за голосование
	$dbbonussize10 = '10'; //размер бонуса за каждое 10 голосование
	/////////////////////////////////////////////настройки интеркассы 2.0
	$ikshopid = '52975e09bf4efc181bdddef5'; //id вашего магазина (написан в ЛК intercassa)
	$secret_key = 'lalka'; // ваш секретный ключ интеркассы
	$secret_key_test = 'lalka'; // ваш тестовый секретный ключ интеркассы 
	$ikTesting = true;// принимать ли тестовые платежи
?>