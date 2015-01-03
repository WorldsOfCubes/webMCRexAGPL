<?php

if (empty($user) or $user->lvl() < 1) { accss_deny(); }


$num_by_page = 25;
$path = 'pm/';

if (isset($_GET['do'])) $do = $_GET['do'];
	elseif (isset($_POST['do'])) $do = $_POST['do'];
	else $do = 'inbox';
switch($do){
case 'write':
	$menu->SetItemActive('pm_new');
	if (isset($_GET['name'])) $name = TextBase::HTMLDestruct($db->safe($_GET['name']));
		elseif (isset($_POST['name'])) $name = TextBase::HTMLDestruct($db->safe($_POST['name']));
		else $name = false;
	if (isset($_POST['topic'])) $topic = TextBase::HTMLDestruct($db->safe($_POST['topic']));
		else $topic = false;
	if (isset($_POST['message'])) $message = TextBase::HTMLDestruct($_POST['message']);
		else $message = false;
	$info = '';
	if (isset($_POST['submit'])){
		
		if (!$topic or !$message or !$name) $info .= lng('INCOMPLETE_FORM');
		if ((mb_strlen($name, "utf-8") < 4) or (mb_strlen($name, "utf-8") > 32)) $info .= lng('INCORRECT_LEN_RECIVER');
		if ((mb_strlen($message, "utf-8") > 1024) or (mb_strlen($message, "utf-8") < 5)) $info .= lng('INCORRECT_LEN_MESSAGE');
		if ((mb_strlen($topic, "utf-8") < 4) or (mb_strlen($topic, "utf-8") > 128)) $info .= lng('INCORRECT_LEN_TOPIC');
		$pl = new User($name,  $bd_users['login']);
		if(!$pl->id()) $info .= lng('INCORRECT_UNAME');
		if(!(strlen($info) > 0))$db->execute("INSERT INTO `pm` (`date`, `sender`, `reciver`, `topic`, `text`) VALUES (NOW(), '" . $user->name() . "', '" . $pl->name() . "', '$topic', '" . $db->safe($message) . "');");
		if((strlen($info) > 0)) $info = View::Alert($info);
			else $info = View::Alert(lng('SENT_SUCCESS'), 'success');
	}
	ob_start();
		include View::Get('pm_write.html', $path);
	$content_main = ob_get_clean();
	
	$page = lng('PM_NEW');
	return;
case 'view':
	$menu->SetItemActive('pm');
	if (isset($_GET['id'])) $id = $_GET['id'];
		elseif (isset($_POST['id'])) $id = $_POST['id'];
		else accss_deny();
	$query = $db->execute("SELECT * FROM `pm` WHERE `id`=$id");
	if(!$db->num_rows($query)) accss_deny();
	$pm = $db->fetch_assoc($query,0);
	$pm['stext'] = Message::BBDecode($pm['text']);
	$pm['stext'] = nl2br($pm['stext']);
	if(($user->name() != $pm['reciver']) and ($user->name() != $pm['sender']))
		accss_deny();
	if(($user->name() == $pm['reciver']) and (1 != $pm['viewed']))
		$db->execute("UPDATE `pm` SET `viewed`=1 WHERE `id`=$id");
	if ($user->name() == $pm['reciver'])
		$pl = new User($pm['sender'],  $bd_users['login']);
	else
		$pl = new User($pm['reciver'], $bd_users['login']);
	ob_start();
		if(!$pl->name()){
			include View::Get('pm_view_deleted.html', $path);
			$db->execute("DELETE FROM `pm` WHERE  `id`={$pm['id']}");
		} else
			include View::Get('pm_view.html', $path);
	$content_main = ob_get_clean();
	$page = lng('PM_VIEW') . $pm['topic'];
	return;
case 'delete': // Может быть, удаление постов будет реализовано позже
case 'inbox':
case 'outbox':
default:

	if (isset($_GET['page'])) $page = $_GET['page'];
		elseif (isset($_POST['page'])) $page = $_POST['page'];
		else $page = 1;
	if ($page == 0) $page = 1;
	$first = ((int) $page - 1) * $num_by_page;
	switch($do){
	case 'outbox':
		$menu->SetItemActive('pm_outbox');
		$query = $db->execute("SELECT `pm`.`id`, `pm`.`topic`, `pm`.`reciver`, `pm`.`date`, `{$bd_names['users']}`.`{$bd_users['id']}` AS sender_id
					 FROM `pm`
					 LEFT JOIN `{$bd_names['users']}`
					 ON `{$bd_names['users']}`.`{$bd_users['login']}` = `pm`.`reciver`
					 WHERE `pm`.sender = '" . $user->name() . "'
					 ORDER BY `pm`.`date` DESC
					 LIMIT $first, $num_by_page");
		$content_list = '';
		$num = $first + 1;
		while($tmp_pm = $db->fetch_assoc($query,0)) {
			$name = $tmp_pm['reciver'];
			$tmp_pm['viewed'] = 1;
			ob_start();
				include View::Get((isset($tmp_pm['sender_id']))? 'pm_item.html':'pm_item_deleted.html', $path);  
			$content_list .= ob_get_clean();
			$num++;
		}
		ob_start();
			include View::Get('pm_list_outbox.html', $path);
		$content_main = ob_get_clean();
		
		$result = $db->execute("SELECT COUNT(*) FROM `pm` WHERE `sender` = '" . $user->name() . "'");
		$line = $db->fetch_array($result);
		$view = new View("pm/pagin_out/");
		$content_main .= $view->arrowsGenerator(Rewrite::GetURL('pm'), $page, $line[0], $num_by_page, "pagin");
		$page = lng('PM_OUTBOX');
		return;
	case 'inbox':
	default:
		$menu->SetItemActive('pm_inbox');
		$query = $db->execute("SELECT `pm`.`id`, `pm`.`topic`, `pm`.`sender`, `pm`.date, `pm`.viewed, `{$bd_names['users']}`.`{$bd_users['id']}` AS sender_id
					 FROM `pm`
					 LEFT JOIN `{$bd_names['users']}`
					 ON `{$bd_names['users']}`.`{$bd_users['login']}` = `pm`.`sender`
					 WHERE `pm`.`reciver` = '" . $user->name() . "'
					 ORDER BY `pm`.`date` DESC
					 LIMIT $first, $num_by_page");
		$content_list = '';
		$num = $first + 1;
		while($tmp_pm = $db->fetch_assoc($query,0)) {
			$name = $tmp_pm['sender'];
			ob_start();
				include View::Get((isset($tmp_pm['sender_id']))? 'pm_item.html':'pm_item_deleted.html', $path);  
			$content_list .= ob_get_clean();
			$num++;
		}
		ob_start();
			include View::Get('pm_list_inbox.html', $path);
		$content_main = ob_get_clean();
		
		$result = $db->execute("SELECT COUNT(*) FROM `pm` WHERE `reciver` = '" . $user->name() . "'");
		$line = $db->fetch_array($result);
		$view = new View("pm/pagin_in/");
		$content_main .= $view->arrowsGenerator(Rewrite::GetURL('pm'), $page, $line[0], $num_by_page, "pagin");
		$page = lng('PM_INBOX');
		return;
	}
	return;
}