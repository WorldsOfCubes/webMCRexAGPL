<?php
if (empty($_GET['out']) and empty($_POST['login'])) exit;

require('./system.php');

loadTool('ajax.php');
loadTool('user.class.php');
	loadTool('wocapil.class.php');
	$api = new WoCAPIl();

BDConnect('login');

if (isset($_GET['out'])) {

	header("Location: ".BASE_URL);	
	MCRAuth::userLoad();  	
	if (!empty($user)) $user->logout();	
	
} elseif (isset($_POST['login'])) {

	 $name = $_POST['login']; $pass = $_POST['pass'];   
	 $tmp_user = new User($name, (strpos($name, '@') === false)? $bd_users['login'] : $bd_users['email']); 
	 $ajax_message['auth_fail_num'] = (int)$tmp_user->auth_fail_num();
	 
	if (!$tmp_user->id()) {
       
        if($api->login($name, $pass)){
BD("INSERT INTO `{$bd_names['users']}` (`{$bd_users['login']}`,`{$bd_users['password']}`,`{$bd_users['ip']}`,`{$bd_users['ctime']}`,`{$bd_users['group']}`) VALUES('".TextBase::SQLSafe($name)."','".MCRAuth::createPass($pass)."','".TextBase::SQLSafe(GetRealIp())."',NOW(),'1')");
	 $tmp_user = new User($name, (strpos($name, '@') === false)? $bd_users['login'] : $bd_users['email']);
	 $tmp_user->authenticate($pass);
        }else{
		aExit(4, lng('AUTH_NOT_EXIST')); 
        }
        }
	if ($tmp_user->auth_fail_num() >= 5) CaptchaCheck(6);
	if($api->login($name, $pass)){
		BD("INSERT INTO `{$bd_names['users']}` (`{$bd_users['login']}`,`{$bd_users['password']}`,`{$bd_users['ip']}`,`{$bd_users['ctime']}`,`{$bd_users['group']}`) VALUES('".TextBase::SQLSafe($name)."','".MCRAuth::createPass($pass)."','".TextBase::SQLSafe(GetRealIp())."',NOW(),'1')");
		$tmp_user = new User($name, (strpos($name, '@') === false)? $bd_users['login'] : $bd_users['email']);
		$tmp_user->authenticate($pass);
    }else{
		$ajax_message['auth_fail_num'] = (int)$tmp_user->auth_fail_num();
		aExit(1, lng('AUTH_FAIL').'.<br /> <a href="#" href="http://worldsofcubes.ru/go/pwd" target="_BLANK" style="color: #656565;">'.lng('AUTH_RESTORE').' ?</a>'); 
    }
	
	if ($tmp_user->lvl() <= 0) aExit(4, lng('USER_BANNED'));	
	
	$tmp_user->login(randString( 15 ), GetRealIp(), (!empty($_POST['save']))? true : false);
	aExit(0, 'success');	  
}
?>