<?php
	if(!defined('INCLUDE_CHECK')) die("You don't have permissions to run this");
	/* Метод хеширования пароля для интеграции с различними плагинами/сайтами/cms/форумами
	'hash_md5' 			- md5 хеширование
	'hash_authme'   	- интеграция с плагином AuthMe
	'hash_cauth' 		- интеграция с плагином Cauth
	'hash_xauth' 		- интеграция с плагином xAuth
	'hash_joomla' 		- интеграция с Joomla (v1.6- v1.7)
	'hash_ipb' 			- интеграция с IPB
	'hash_xenforo' 		- интеграция с XenForo
	'hash_wordpress' 	- интеграция с WordPress
	'hash_vbulletin' 	- интеграция с vBulletin
	'hash_dle' 			- интеграция с DLE
	'hash_drupal'     	- интеграция с Drupal (v.7)
	'hash_launcher'		- интеграция с лаунчером sashok724 (Регистрация через лаунчер)
	*/
	require('../system.php');
	BDConnect('auth');
	$crypt 				= 'hash_md5';
	
	$db_host			=  $config['db_host']; // Ip-адрес MySQL
	$db_port			= $config['db_port']; // Порт базы данных
	$db_user			= $config['db_login']; // Пользователь базы данных
	$db_pass			= $config['db_passw']; // Пароль базы данных
	$db_database		= $config['db_name']; //База данных
	
	$db_table       	= $bd_names['users']; //Таблица с пользователями
	$db_group           = $bd_users['group']; //Для webmcr (минификс)
	$db_columnId  		= $bd_users['id']; //Колонка с ID пользователей
	$db_columnUser  	= $bd_users['login']; //Колонка с именами пользователей
	$db_columnPass  	= $bd_users['password']; //Колонка с паролями пользователей
	$db_tableOther 		= 'xf_user_authenticate'; //Дополнительная таблица для XenForo, не трогайте
	$db_columnSesId	 	= $bd_users['session']; //Колонка с сессиями пользователей, не трогайте
	$db_columnServer	= $bd_users['server']; //Колонка с серверами пользователей, не трогайтe
	$db_columnSalt  	= 'members_pass_salt'; //Настраивается для IPB и vBulletin: , IPB - members_pass_salt, vBulletin - salt
    $db_columnIp  		= $bd_users['ip']; //Колонка с IP пользователей
	
	$db_columnDatareg   = $bd_users['ctime']; // Колонка даты регистрации
	$db_columnMail      = $bd_users['email']; // Колонка mail
	
	$masterversion  	= sqlConfigGet('launcher-version'); //Мастер-версия лаунчера
	$protectionKey		= sqlConfigGet('protection-key'); //Ключ защиты сессии. Никому его не говорите.


//========================= Настройки ЛК =======================//	
	
	$uploaddirs = $site_ways['skins'];  //Папка скинов
	$uploaddirp = $site_ways['cloaks']; //Папка плащей
	
	$usePersonal 		=  true; //Использовать личный кабинет
	$canUploadSkin		=  true; //Можно ли заливать скины
	$canUploadCloak		=  true; //Можно ли заливать плащи
	$canBuyVip			=  true; //Можно ли покупать VIP
	$canBuyPremium		=  true; //Можно ли покупать Premium
	$canBuyUnban		=  true; //Можно ли покупать разбан
	$canActivateVaucher =  false; //Можно ли активировать ваучер
	$canExchangeMoney   =  true; //Можно ли обменивать Realmoney -> IConomy
	$canUseJobs			=  false; //Можно ли использовать работы
	$usecheck			=  true; //Можно ли использовать регистрацию в лаунчере
	
	$cloakPrice			=  0;   //Цена плаща (В рублях)
	$vipPrice			=  100;  //Цена випа (В руб/мес)
	$premiumPrice		=  250;  //Цена премиума (В руб/мес)
	$unbanPrice			=  150;  //Цена разбана (В рублях)
	
	$initialIconMoney	=  0;  //Сколько денег дается при регистрации в IConomy
	$exchangeRate		=  100; //Курс обмена Realmoney -> IConomy
?>
