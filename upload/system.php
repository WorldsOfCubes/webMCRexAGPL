<?php

error_reporting(E_ALL);

$user = false; $link = false; $mcr_tools = array();

define('MCR_ROOT', dirname(__FILE__).'/');
define('MCR_LANG', 'ru_RU');

loadTool('base.class.php');
loadTool('pm.class.php');

if (!file_exists(MCR_ROOT.'main.cfg.php')) { header("Location: install/install.php"); exit; }

require(MCR_ROOT.'instruments/locale/'.MCR_LANG.'.php');
require(MCR_ROOT . 'main.cfg.php');
require(MCR_ROOT.'donate.cfg.php');

if(!isset($config['smtp_tls'])){ //Корректная работа после введения поддержки TLS/SSL
	loadTool("alist.class.php");
	$config['smtp_tls'] = false;
	ConfigManager::SaveMainConfig();
}

require(MCR_ROOT.'instruments/auth/'.$config['p_logic'].'.php');

define('MCRAFT', MCR_ROOT.$site_ways['mcraft']);
define('MCR_STYLE', MCR_ROOT.$site_ways['style']); 

define('STYLE_URL', $site_ways['style']); // deprecated
define('DEF_STYLE_URL', STYLE_URL . View::def_theme . '/');

define('BASE_URL', $config['s_root']);

date_default_timezone_set($config['timezone']);

function BD( $query ) {
global $db;

	vtxtlog("Using old method BD()");
	return $db->execute($query);
}

function BDConnect($log_script = 'default') {
global $db;

	$db = new DB();
	vtxtlog("Using old method BDConnect()");
	return $db->connect($log_script);
}

/* Системные функции */

function loadTool( $name, $sub_dir = '') {
global $mcr_tools; 

	if (in_array($name, $mcr_tools)) return;
	
	$mcr_tools[] = $name;
	
	require( MCR_ROOT . 'instruments/' . $sub_dir . $name);	
}

function lng($key, $lang = false) {
global $MCR_LANG;

	return isset($MCR_LANG[$key]) ? $MCR_LANG[$key] : $key;
}

function tmp_name($folder, $pre = '', $ext = 'tmp'){
    $name  = $pre.time().'_';
	  
    for ($i=0;$i<8;$i++) $name .= chr(rand(97,121));
	  
    $name .= '.'.$ext;
	  
return (file_exists($folder.$name))? tmp_name($folder,$pre,$ext) : $name;
}

function InputGet($key, $method = 'POST', $type = 'str') {
	
	$blank_result = array( 'str' => '', 'int' => 0, 'float' => 0, 'bool' => false);
	
	if (($method == 'POST' and !isset($_POST[$key])) or
		($method != 'POST' and !isset($_GET[$key]))) return $blank_result[$type];
	
	$var = ($method == 'POST')? $_POST[$key] : $_GET[$key];
	
    switch($type){
		case 'str': return TextBase::HTMLDestruct($var); break;
		case 'int': return (int)$var; break;
		default: settype($var, $type); return $var; break;
	}	
}

function POSTGood($post_name, $format = array('png')) {

if ( empty($_FILES[$post_name]['tmp_name']) or 

     $_FILES[$post_name]['error'] != UPLOAD_ERR_OK or
	 
	 !is_uploaded_file($_FILES[$post_name]['tmp_name']) ) return false;
   
$extension = strtolower(substr($_FILES[$post_name]['name'], 1 + strrpos($_FILES[$post_name]['name'], ".")));

if (is_array($format) and !in_array($extension, $format)) return false;
   
return true;
}

function POSTSafeMove($post_name, $tmp_dir = false) {
	
	if (!POSTGood($post_name, false)) return false;
	
	if (!$tmp_dir) $tmp_dir = MCRAFT.'tmp/';

	if (!is_dir($tmp_dir)) mkdir($tmp_dir, 0777); 

	$tmp_file = tmp_name($tmp_dir);
	if (!move_uploaded_file( $_FILES[$post_name]['tmp_name'], $tmp_dir.$tmp_file )) { 

	vtxtlog('[POSTSafeMove] --> "'.$tmp_dir.'" <-- '.lng('WRITE_FAIL'));
	return false;
	}

return array('tmp_name' => $tmp_file, 'name' => $_FILES[$post_name]['name'], 'size_mb' => round($_FILES[$post_name]['size'] / 1024 / 1024, 2));
}

function randString( $pass_len = 50 ) {
    $allchars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $string = "";
    
    mt_srand( (double) microtime() * 1000000 );
    
    for ( $i=0; $i<$pass_len; $i++ )
	$string .= $allchars{ mt_rand( 0, strlen( $allchars )-1 ) };
	
    return $string;
}

function sqlConfigGet($type){
global $db, $bd_names;
	
	if (!in_array($type, ItemType::$SQLConfigVar)) return false;
	
    $result = $db->execute("SELECT `value` FROM `{$bd_names['data']}` WHERE `property`='". $db->safe($type) ."'");

    if ( $db->num_rows( $result ) != 1 ) return false;
	
	$line = $db->fetch_array($result, MYSQL_NUM );
	
	return $line[0];		
}

function sqlConfigSet($type, $value) {
global $db, $bd_names;

	if (!in_array($type, ItemType::$SQLConfigVar)) return false;
	
	$result = $db->execute("INSERT INTO `{$bd_names['data']}` (value,property) VALUES ('". $db->safe($value) ."','". $db->safe($type) ."') ON DUPLICATE KEY UPDATE `value`='". $db->safe($value) ."'");
	return true;
}

function GetRealIp(){

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
	
	$ip = $_SERVER['HTTP_CLIENT_IP']; 
	 
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
	
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	 
	else 
	 
	$ip = $_SERVER['REMOTE_ADDR'];
 
return substr($ip, 0, 16);
}

function RefreshBans() {
global $db, $bd_names;

	/* Default ban until time */
	$db->execute("DELETE FROM {$bd_names['ip_banning']} WHERE (ban_until='0000-00-00 00:00:00') AND (time_start<NOW()-INTERVAL ".((int) sqlConfigGet('next-reg-time'))." HOUR)");
	
	$db->execute("DELETE FROM {$bd_names['ip_banning']} WHERE (ban_until<>'0000-00-00 00:00:00') AND (ban_until<NOW())");
}

function vtxtlog($string) {
global $config;

if (!$config['log']) return;

$log_file = MCR_ROOT.'log.txt';

	if (file_exists($log_file) and round(filesize ($log_file) / 1048576) >= 50) unlink($log_file);

	if ( !$fp = fopen($log_file,'a') ) exit('[vtxtlog]  --> '.$log_file.' <-- '.lng('WRITE_FAIL'));
	
	fwrite($fp, date("H:i:s d-m-Y").' < '.$string.PHP_EOL); 
	fclose($fp);	
}

function ActionLog($last_info = 'default_action') {
global $db, $config, $bd_names;

	$ip = GetRealIp();
	$db->execute("DELETE FROM `{$bd_names['action_log']}` WHERE `first_time` < NOW() - INTERVAL {$config['action_time']} SECOND");

	$sql  = "INSERT INTO `{$bd_names['action_log']}` (IP, first_time, last_time, query_count, info) ";
	$sql .= "VALUES ('". $db->safe($ip) ."', NOW(), NOW(), 1, '". $db->safe($last_info) ."') ";
	$sql .= "ON DUPLICATE KEY UPDATE `last_time` = NOW(), `query_count` = `query_count` + 1, `info` = '". $db->safe($last_info) ."' ";
	
	$db->execute($sql);
	
	$result = $db->execute("SELECT `query_count` FROM `{$bd_names['action_log']}` WHERE `IP`='". $db->safe($ip) ."'");
	$line = $db->fetch_array($result, MYSQL_NUM);
	
	$query_count = (int) $line[0];
	if ($query_count > $config['action_max']) {
	
	$db->execute("DELETE FROM `{$bd_names['action_log']}` WHERE `IP` = '". $db->safe($ip) ."'");
	
	RefreshBans();
	
	$sql  = "INSERT INTO {$bd_names['ip_banning']} (IP, time_start, ban_until, ban_type, reason) ";
	$sql .= "VALUES ('". $db->safe($ip) ."', NOW(), NOW()+INTERVAL ". $db->safe($config['action_ban']) ." SECOND, '2', 'Many BD connections (".$query_count.") per time') ";
	$sql .= "ON DUPLICATE KEY UPDATE `ban_type` = '2', `reason` = 'Many BD connections (".$query_count.") per time' ";
	
	$db->execute($sql);
	}
	
	return $query_count;
}

function CanAccess($ban_type = 1) {
global $db, $link, $bd_names;

	$ip = GetRealIp(); 
	$ban_type = (int) $ban_type;
	
	$result = $db->execute("SELECT COUNT(*) FROM `{$bd_names['ip_banning']}` WHERE `IP`='". $db->safe($ip) ."' AND `ban_type`='".$ban_type."' AND `ban_until` <> '0000-00-00 00:00:00' AND `ban_until` > NOW()");
	$line = $db->fetch_array($result, MYSQL_NUM);
	$num = (int)$line[0];

	if ($num) {
	
		mysql_close( $link );
		
		if ( $ban_type == 2 ) exit('(-_-)zzZ <br>'.lng('IP_BANNED'));
		return false;
	}
	
	return true;					
}

function CheckPM() {
	$pm_count = PManager::CheckNew();
	ob_start();
	include View::Get("pm_new_modal.html", "pm/");
	$message = ob_get_clean();
	return ($pm_count!=0)? $message : '';
}

function CheckPMMenu(){
global $user;
	if (empty($user)) return '';
	$pm_count = PManager::CheckNew();
	return ($pm_count!=0)? "&nbsp;({$pm_count})" : '';
}
?>