<?php
if (!defined('MCR')) exit;
if (empty($user) or $user->lvl() <= 0) { accss_deny(); }

/* Default vars */
$page = lng('PAGE_OPTIONS');

$prefix = 'profile/';
$message = '';
$user_img_get = $user->getSkinLink().'&amp;refresh='.rand(1000, 9999);
$menu->SetItemActive('options');
if (isset($_GET['result'])) {
	if ($_GET['result'] == "success") {
		$message = View::Alert("Вы успешно пополнили донат-счет! Спасибо за помощь проекту!", 'success');
	} elseif ($_GET['result'] == "fail") {
		$message = View::Alert("К сожалению, платеж не прошел.");
	} elseif ($_GET['result'] == "wait") {
		$message = View::Alert("Платеж ожидает проведение", 'info');
	}
}

if ($user->group() == 4 or !$user->email() or $user->gender() > 1) {
	
	loadTool('ajax.php'); $html_info = '';	
	
	if (CaptchaCheck(0, false)) {
	
		if (isset($_POST['female']) and $user->gender() > 1) 
		
			$user->changeGender((!(int)$_POST['female'])? 0 : 1);

		if (!empty($_POST['email'])) { 
		
			$send_result = $user->changeEmail($_POST['email'], true);
			
				if ( $send_result == 1) $html_info = lng('REG_CONFIRM_INFO');
			elseif ( $send_result == 1902) $html_info = lng('AUTH_EXIST_EMAIL');
			else $html_info = lng('MAIL_FAIL');				
		}
	} elseif ( isset($_POST['antibot']) ) $html_info = lng('CAPTCHA_FAIL');
	
	if ($user->group() == 4 or !$user->email() or $user->gender() > 1) {
	
	ob_start();	
	
	include View::Get('cp_form.html', $prefix);	
	
		if ( $user->group() == 4 or !$user->email() ) include View::Get('profile_email.html', $prefix);	
	
		if ($user->gender() > 1 ) include View::Get('profile_gender.html', $prefix);
			
	include View::Get('cp_form_footer.html', $prefix);
	
	$content_main .= ob_get_clean();
	}	
}

if ($user->group() != 4 ) {
	
	
	
	if(isset($_POST['prprem']) && $user->lvl() == 6) {
		if($player_money >= $donate['premiumcash']/2){
			$user->addMoney(0 - $donate['premiumcash']/2);
			$db->execute("UPDATE permissions SET value=value+2678400 WHERE name='$player'");
			$message = View::Alert("Вы успешно продлили Premium! Спасибо за помощь проекту!", 'success');
		}else{
			$message = View::Alert("К сожалению, у вас недостаточно средств, пополните счет!");
		}
	}

	if(isset($_POST['prvip']) && $user->lvl() == 5) {
		if($player_money >= $donate['vipcash']/2){
			$user->addMoney(0 - $donate['vipcash']/2);
			$db->execute("UPDATE permissions SET value=value+2678400 WHERE name='$player'");
			$message = View::Alert("Вы успешно продлили VIP! Спасибо за помощь проекту!", 'success');
		}else{
			$message = View::Alert("К сожалению, у вас недостаточно средств или вы не пермиум!");
		}
	}

	if(isset($_POST['unban'])) {
		$sql2 = $db->execute("SELECT name FROM banlist WHERE name='$player'");
		if($sql2) {
			$query2 = $db->fetch_array($sql2);
			$query2 = $query2['name'];
		} else $query2 = false;
		$sql = $db->execute("SELECT numofban FROM unbans WHERE name='$player'");
		if($sql) {
			$query = $db->fetch_array($sql);
			$query = $query['numofban'];
		} else $query = false;
		if($query == ''){
			if($query2 != ''){
				if($player_money >=  $donate['unban']){
					$db->execute("INSERT INTO unbans VALUES (NULL, '$player', '1')");
					$db->execute("DELETE FROM banlist WHERE name='$player'");
					$user->addMoney(0 - $donate['unban']);
					$message = View::Alert("Это ваш первый разбан, не нарушайте правила сервера!", 'success');
				}else{
					$message = View::Alert("У вас недостаточно средств для разбана!");
				}
			}else{
				$message = View::Alert("Вы не забанены!");
			}
		}elseif($query >= 1){
			if($query2 != ''){
				if($player_money >=  $donate['unban']*$query) {
					$db->execute("UPDATE unbans SET numofban=numofban+1 WHERE name='$player'");
					$db->execute("DELETE FROM banlist WHERE name='$player'");
					$user->addMoney(0 - $donate['unban']*$query);
					$message = View::Alert("Это ваш очередной разбан, может, пора себя хорошо вести?!", 'warning');
				}else{
					$message = View::Alert("У вас недостаточно средств для разбана!");
				}
			}else{
				$message = View::Alert("Вы не забанены!");
			}
		}
	}

	if(isset($_POST['buym'])) {
		$wantbuy = (int)$_POST['wantby'];
		$gamemoneyadd = ($wantbuy*$donate['exchangehow']);
		if($wantbuy == '' || $wantbuy < 1) $message = View::Alert("Вы не ввели сумму!");
			else{
				if($player_money >= $wantbuy){
					$user->addEcon($gamemoneyadd);
					$player_econ += $gamemoneyadd;
					$user->addMoney(0 - $wantbuy);
					$player_money -= $wantbuy;
					$message = View::Alert("На ваш счет зачислено $gamemoneyadd монет!", 'success');
				}else{
					$message = View::Alert("На вашем счету недостаточно средств!");
				}
		}
	}
	if(isset($_POST['govip'])) {
		if($player_money >= $donate['vipcash']){
			$unixtime = time();
			$A=$unixtime;
			$B=2678400;
			$pexdate=$A+$B;
			$expdate = date('d-m-Y H:i:s', $pexdate);
			$user->changeGroup(5);
			$player_group = "VIP";
			$db->execute("DELETE FROM `permissions` WHERE `name`='$player';");
			$db->execute("INSERT INTO permissions (id, name, type, permission, world, value) VALUES (NULL, '$player', '1', 'group-vip-until', ' ', '$pexdate')");
			$user->addMoney(0 - $donate['vipcash']);
			$player_money -= $donate['vipcash'];
			$message = View::Alert("Вы успешно купили VIP! Спасибо за помощь проекту", 'success');
		}else{
			$message = View::Alert("К сожалению у вас недостаточно средств, пополните счет!");
		}
	}

	if(isset($_POST['goprem'])) {
		if ($user->lvl()<5) {
			if($player_money >= $donate['premiumcash']) {
				$unixtime = time();
				$A=$unixtime;
				$B=2678400;
				$pexdate=$A+$B;
				$expdate = date('d-m-Y H:i:s', $pexdate);
				$user->changeGroup(6);
				$player_group = "Premium";
				$db->execute("DELETE FROM `permissions` WHERE `name`='$player';");
				$db->execute("INSERT INTO permissions (id, name, type, permission, world, value) VALUES (NULL, '$player', '1', 'group-premium-until', ' ', '$pexdate')");
				$user->addMoney(0 - $donate['premiumcash']);
				$player_money -= $donate['premiumcash'];
				$message = View::Alert("Вы успешно купили Premium! Спасибо за помощь проекту!", 'success');
			}else{
				$message = View::Alert("К сожалению у вас недостаточно средств, пополните счет!");
			}
		} elseif ($user->lvl()==5) {
			if($player_money >= $donate['premiumcash'] / 2) {
				$unixtime = time();
				$A=$unixtime;
				$B=2678400;
				$pexdate=$A+$B;
				$expdate = date('d-m-Y H:i:s', $pexdate);
				$user->changeGroup(6);
				$player_group = "Premium";
				$db->execute("DELETE FROM `permissions` WHERE `name`='$player';");
				$db->execute("INSERT INTO permissions (id, name, type, permission, world, value) VALUES (NULL, '$player', '1', 'group-premium-until', ' ', '$pexdate')");
				$user->addMoney(0 - ($donate['premiumcash'] / 2));
				$player_money -= $donate['premiumcash'] / 2;
				$message = View::Alert("Вы успешно купили Premium! Спасибо за помощь проекту!", 'success');
			}else{
				$message = View::Alert("К сожалению у вас недостаточно средств, пополните счет!");
			}
		}
	}


	ob_start();

	if ($user->getPermission('change_skin'))  include View::Get('profile_skin.html', $prefix);
	if ($user->getPermission('change_skin')   and !$user->defaultSkinTrigger())
											  include View::Get('profile_del_skin.html', $prefix);
	if ($user->getPermission('change_cloak')) {
		include View::Get('profile_cloak.html', $prefix);
	} else include View::Get('profile_cloak_buy.html', $prefix);
	if ($user->getPermission('change_cloak')  and file_exists($user->getCloakFName()))
											  include View::Get('profile_del_cloak.html', $prefix);
	if ($user->getPermission('change_login')) include View::Get('profile_nik.html', $prefix);
	if ($user->getPermission('change_pass'))  include View::Get((!$user->pass_set())?'profile_pass_noold.html':'profile_pass.html', $prefix);

	$profile_inputs = ob_get_clean();

	ob_start();
	if ($user->getPermission('change_prefix')) include View::Get('profile_prefix.html', $prefix);
		else include View::Get('profile_prefix_buy.html', $prefix);
	$profile_prefix = ob_get_clean();
	ob_start();
	if ($user->lvl() == 6) include View::Get('profile_donate_buttons_premium.html', $prefix);
		elseif ($user->lvl() == 5) include View::Get('profile_donate_buttons_vip.html', $prefix);
		elseif ($user->lvl() == 2) include View::Get('profile_donate_buttons_default.html', $prefix);
	$profile_donate_btns = ob_get_clean();

	loadTool('profile.class.php'); $user_profile = new Profile($user, 'other/', 'profile', true);
	$profile_info = $user_profile->Show(false);

	ob_start(); include View::Get('profile.html', $prefix);

	$content_main .= ob_get_clean();
}