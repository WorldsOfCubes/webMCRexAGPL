<?php 
//if (empty($user) or $user->lvl() < 1) { accss_deny(); }

$menu->SetItemActive('users');

$num_by_page = 25;

loadTool('profile.class.php');
if (isset($_GET['do'])) $do = $_GET['do'];
	elseif (isset($_POST['do'])) $do = $_POST['do'];
	else $do = 1;
$path = 'users/';
if ($do == 'full' or isset($_GET['name']) or isset($_POST['name'])) {
	if (isset($_GET['name'])) $name = $_GET['name'];
		elseif (isset($_POST['name'])) $name = $_POST['name'];
	$pl = new User($name, $bd_users['login']);
	if(!$pl->id()) {
		include(MCR_ROOT.'/location/404.php');
	} else {
		$page = lng('USER_POFILE') . " - " . $name;
		$stat = $pl->getStatistic();
		ob_start();
			include View::Get('user_profile.html', $path);
		$content_main = ob_get_clean();
	}
} else {
	if ($do == 0) $do = 1;
	$page = lng('USERS_LIST');
	$first = ((int) $do - 1) * $num_by_page;
	$query = $db->execute("SELECT `{$bd_names['users']}`.`{$bd_users['id']}`, `{$bd_names['users']}`.`{$bd_users['login']}`, `{$bd_names['users']}`.`{$bd_users['female']}`, `{$bd_names['users']}`.default_skin, `{$bd_names['groups']}`.name AS group_name
				FROM `{$bd_names['users']}`
				LEFT JOIN `{$bd_names['groups']}`
				ON `{$bd_names['groups']}`.id = `{$bd_names['users']}`.`{$bd_users['group']}`
				ORDER BY `{$bd_names['users']}`.`{$bd_users['login']}` ASC
				LIMIT $first, $num_by_page");
	$content_list = '';
	$num = $first + 1;
	while($tmp_user = $db->fetch_assoc($query,0)) {
		ob_start();
			include View::Get('users_item.html', $path);  
		$content_list .= ob_get_clean();
		$num++;
	}
	ob_start();
		include View::Get('users_list.html', $path);
	$content_main = ob_get_clean();
	
	$result = $db->execute("SELECT COUNT(*) FROM `{$bd_names['users']}`");
	$line = $db->fetch_array($result);
	$view = new View("users/");
	$content_main .= $view->arrowsGenerator(Rewrite::GetURL('users'), $do, $line[0], $num_by_page, "pagin");
}