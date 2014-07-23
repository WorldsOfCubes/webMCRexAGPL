<?php 
if (empty($user) or $user->lvl() < 1) { accss_deny(); }

$num_by_page = 25;

if (isset($_GET['do'])) $do = $_GET['do'];
	elseif (isset($_POST['do'])) $do = $_POST['do'];
	else $do = 1;
$path = 'users/';
if ($do == 'full' or isset($_GET['name']) or isset($_POST['name'])) {
	if (isset($_GET['name'])) $name = $_GET['name'];
		elseif (isset($_POST['name'])) $name = $_POST['name'];
	$pl = new User($name, $bd_users['login']);
	if(!$pl->id()) {
		$page = 'Страница не найдена';
		$content_main = View::ShowStaticPage('404.html');
		ob_start();
		include View::Get('index.html');
		$html_page = ob_get_clean();
		loadTool("template.class.php");
		$parser = new TemplateParser();
		$html_page = $parser->parse($html_page);
		echo $html_page;
		exit;
	}
	$page = lng('USER_POFILE') . " - " . $name;
	ob_start();
		include View::Get('user_profile.html', $path);
	$content_main = ob_get_clean();
} else {
	$page = lng('USERS_LIST');
	$first = ((int) $do - 1) * $num_by_page;
	$last  = (int) $do * $num_by_page;
	$query = BD("SELECT `{$bd_names['users']}`.`{$bd_users['id']}`, `{$bd_names['users']}`.`{$bd_users['login']}`, `{$bd_names['users']}`.`{$bd_users['female']}`, `{$bd_names['users']}`.default_skin, `{$bd_names['groups']}`.name AS group_name
				FROM `{$bd_names['users']}`
				LEFT JOIN `{$bd_names['groups']}`
				ON `{$bd_names['groups']}`.id = `{$bd_names['users']}`.`{$bd_users['group']}`
				ORDER BY `{$bd_names['users']}`.`{$bd_users['id']}` ASC
				LIMIT $first, $last");
	$content_list = '';
	print(mysql_error());
	$num = $first + 1;
	while($tmp_user = mysql_fetch_assoc($query,0)) {
		ob_start();
			include View::Get('users_item.html', $path);  
		$content_list .= ob_get_clean();
		$num++;
	}
	ob_start();
		include View::Get('users_list.html', $path);
	$content_main = ob_get_clean();
	
	$result = BD("SELECT COUNT(*) FROM `{$bd_names['users']}`");
	$line = mysql_fetch_array($result);
	$view = new View("users/");
	$content_main .= $view->arrowsGenerator(Rewrite::GetURL(array('go', 'users')), $do, $line[0], $num_by_page, "pagin");
}