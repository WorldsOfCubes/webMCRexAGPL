<?php
//if (empty($user) or $user->lvl() < 1) { accss_deny(); }

$menu->SetItemActive('users');

$num_by_page = 25;

loadTool('profile.class.php');
if (isset($_GET['do']))
	$do = $_GET['do']; elseif (isset($_POST['do']))
	$do = $_POST['do'];
else $do = 1;
$path = 'users/';
if ($do == 'full' or isset($_GET['name']) or isset($_POST['name'])) {
	if (isset($_GET['name']))
		$name = $_GET['name']; elseif (isset($_POST['name']))
		$name = $_POST['name'];
	$pl = new User($name, $bd_users['login']);
	if (!$pl->id()) {
		include(MCR_ROOT.'/location/404.php');
	} else {
		$page = lng('USER_POFILE')." - ".$name;
		$stat = $pl->getStatistic();
		ob_start();
		include View::Get('user_profile.html', $path);
		$content_main = ob_get_clean();
	}
} else {
	if ($do == 0)
		$do = 1;
	$page = lng('USERS_LIST');
	$first = ((int)$do - 1) * $num_by_page;
	if(isset($_GET['search']) and strlen($_GET['search'])) {
		$where = " WHERE {$bd_users['login']} LIKE '%".$db->safe($_GET['search'])."%'";
		$search = TextBase::HTMLDestruct($db->safe($_GET['search']));
	} else $where = $search = '';
	$query = $db->execute("SELECT `{$bd_names['users']}`.`{$bd_users['id']}`, `{$bd_names['users']}`.`{$bd_users['login']}`, `{$bd_names['users']}`.`{$bd_users['female']}`, `{$bd_names['users']}`.default_skin, `{$bd_names['groups']}`.name AS group_name
				FROM `{$bd_names['users']}`
				LEFT JOIN `{$bd_names['groups']}`
				ON `{$bd_names['groups']}`.id = `{$bd_names['users']}`.`{$bd_users['group']}`
				$where ORDER BY `{$bd_names['users']}`.`{$bd_users['login']}` ASC
				LIMIT $first, $num_by_page");
	if ($db->num_rows($query)) {
		$content_list = '';
		$num = $first + 1;
		while ($tmp_user = $db->fetch_assoc($query, 0)) {
			ob_start();
			include View::Get('users_item.html', $path);
			$content_list .= ob_get_clean();
			$num++;
		}

		$result = $db->execute("SELECT COUNT(*) FROM `{$bd_names['users']}`$where");
		$line = $db->fetch_array($result);
		$view = new View("users/");
		$url = (!$config['rewrite']) ? ((strlen($search)) ? "go/users/search/$search/" : "go/users/") : ((strlen($search)) ? "?mode=users&search=$search&do=" : "?mode=users&do=");

	} else $content_list = View::ShowStaticPage('no_users.html', $path);
	ob_start();
	include View::Get('users_list.html', $path);
	$content_main = ob_get_clean();
	if ($db->num_rows($query)) $content_main .= $view->arrowsGenerator($url, $do, $line[0], $num_by_page, "pagin");
}