<?php
function CheckPostComplect() {

if (isset($_POST['login']) and 
	isset($_POST['pass']) and 
	isset($_POST['repass']) and 
	isset($_POST['email']) and
	isset($_POST['female']) )
	
	return 1;
	
if (isset($_GET['verificate']) and 
	isset($_GET['id'])) 
	
	return 2;
	
return false;
}

$method = CheckPostComplect();
if (!$method) exit;

require('./system.php');

loadTool('ajax.php');

if (	$config['p_logic'] != 'usual' 
	and $config['p_logic'] != 'xauth'
	and $config['p_logic'] != 'authme') aExit(1,'Registration is blocked. Used auth script from main CMS');

BDConnect('register');

loadTool('wocapir.class.php');
loadTool('user.class.php');
	$api = new WoCAPIr();
$rcodes  = array();  

function tryExit() {
global $rcodes;
    
    $message = '';
    $rnum    = sizeof($rcodes);
    if (!$rnum) return;

        for ($i=0; $i < $rnum; $i++) {

        $modifed = true;

			switch ($rcodes[$i]) {
                case 2 :  $message .= lng('INCORRECT').'. ('.lng('LOGIN').')'; break;
                case 3 :  $message .= lng('INCORRECT').'. ('.lng('PASS').')'; break;
				case 4 :  $message .= lng('INCORRECT').'. ('.lng('REPASS').')'; break;
                case 12 : $message .= lng('INCORRECT').'. ('.lng('EMAIL').')'; break;
                case 6 :  $message .= lng('INCORRECT_LEN').'. ('.lng('LOGIN').')'; break;
                case 7 :  $message .= lng('INCORRECT_LEN').'. ('.lng('PASS').')'; break;
                case 8 :  $message .= lng('INCORRECT_LEN').'. ('.lng('REPASS').')';  break;
                case 9 :  $message .= lng('REPASSVSPASS'); break;
                case 13 : $message .= lng('INCORRECT_LEN').'. ('.lng('EMAIL').')'; break;
                default : $modifed = false; break;
            }	

        if ($modifed) $message .= "<br />";	
        }
		
    aExit(2, $message ); 
}

if ($method == 2) {

	$tmp_user = new User((int)$_GET['id']);
	
	if ($tmp_user->id() and !strcmp($tmp_user->getVerificationStr(), $_GET['verificate'])) $tmp_user->changeGroup(1);

	exit(View::ShowStaticPage('mail_verification_ok.html', 'other/'));
}

RefreshBans();
	
$login  = $_POST['login'];
$pass   = $_POST['pass'];
$repass = $_POST['repass'];	
	
$female = (!(int)$_POST['female'])? 0 : 1;
$email  = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL); 
	
if (!CanAccess()) aExit(11, lng('IP_BANNED'));
    tryExit();
	
if (empty($login) || empty($pass) || empty($repass) || empty($_POST['email'])) aExit(1, lng('INCOMPLETE_FORM'));
    
	if (!preg_match("/^[a-zA-Z0-9_-]+$/", $login))  $rcodes[] = 2; 
    if (!preg_match("/^[a-zA-Z0-9_-]+$/", $pass))   $rcodes[] = 3;
    if (!preg_match("/^[a-zA-Z0-9_-]+$/", $repass)) $rcodes[] = 4;
	if (!$email)                                    $rcodes[] = 12;


	if ((strlen($login) < 4)  or (strlen($login) > 15))  $rcodes[] = 6;
	if ((strlen($pass) < 4)   or (strlen($pass) > 15))   $rcodes[] = 7;
	if ((strlen($repass) < 4) or (strlen($repass) > 15)) $rcodes[] = 8;
    if (strlen($email) > 50)   							 $rcodes[] = 13;
	if (strcmp($pass,$repass)) 							 $rcodes[] = 9;
	
	if($api->register($login, $pass, $repass, $female, $email)) {
		switch ($api->error) {
			case "bad login": aExit(2, lng('INCORRECT').'. ('.lng('LOGIN').')');
			case "bad pass": aExit(2, lng('INCORRECT').'. ('.lng('PASS').')');
			case "bad repass": aExit(2, lng('INCORRECT').'. ('.lng('REPASS').')');
			case "bad mail": aExit(2, lng('INCORRECT').'. ('.lng('EMAILL').')');
			
			case "bad llogin": aExit(2, lng('INCORRECT_LEN').'. ('.lng('LOGIN').')');
			case "bad lpass": aExit(2, lng('INCORRECT_LEN').'. ('.lng('PASS').')');
			case "bad lrepass": aExit(2, lng('INCORRECT_LEN').'. ('.lng('REPASS').')');
			case "bad lmail": aExit(2, lng('INCORRECT_LEN').'. ('.lng('EMAILL').')');
			case "bad rrepass": aExit(2, lng('REPASSVSPASS').')');
			
			case "elogin": aExit(2, lng('AUTH_EXIST_LOGIN'));
			case "email": aExit(2, lng('AUTH_EXIST_EMAIL'));
			default : aExit(2, $api->error);
		}
	}
	

	$tmp_user = new User(mysql_insert_id());
	$tmp_user->setDefaultSkin();	

    $next_reg = (int) sqlConfigGet('next-reg-time');	
	 
	
				
		if ( $tmp_user->changeEmail($email, true) > 1 ) aExit(14, lng('MAIL_FAIL'));
	
	    aExit(0, lng('REG_COMPLETE') .'. '. lng('REG_CONFIRM_INFO'));
	unset($tmp_user);
?>