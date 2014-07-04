<?php
if (!defined('MCR')) exit;

ob_start();

if (!empty($user)) {

	if ((($mode == 'control') or ($mode == 'news_add') or ($mode == 'reqests')) and ($user->lvl() >=15)) 
		include View::Get('side.html', 'admin/');  
	include View::Get('mini_profile.html');    
	
} else {
	
	if ($mode == 'register') $addition_events .= "BlockVisible('reg-box',true); BlockVisible('login-box',false);";
	if ($mode == 'restorepassword') $addition_events .= "RestoreStart();";

	include View::Get('login.html');		    
}

$content_side .= ob_get_clean();

loadTool('monitoring.class.php');

$servManager = new ServerManager('serverstate/');
$content_servers = $servManager->Show('side');

unset($servManager);
?>