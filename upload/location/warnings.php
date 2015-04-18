<?php

$menu->SetItemActive('users');

$num_by_page = 25;

$dir = 'warnings/';
if(isset($_GET['user'])) {
	$pl = new User($_GET['user'], $bd_users['login']);
	if (!$pl->id())
		show_error('404', 'Пользователь не найден');
} else {
	if(!$user) accss_deny();
	$pl = $user;
}
if (!$user or $user->lvl() < 8)
	$subdir = 'user/';
else $subdir = 'mod/';
if($subdir == 'mod/' and isset($_POST['expires']) and isset($_POST['points']) and isset($_POST['reason'])) {
	$pl->warn((InputGet('perm', 'POST', 'bool'))? 2: 1, TextBase::HTMLDestruct($_POST['reason']), $_POST['expires'], $_POST['points']);
}
$query = $db->execute("SELECT * FROM `warnings`
				LEFT JOIN `{$bd_names['users']}`
				ON `warnings`.`mid` = `{$bd_names['users']}`.`{$bd_users['id']}`
				WHERE `warnings`.`uid`=" . $pl->id() . "
				ORDER BY `warnings`.`time` DESC");
ob_start();
if(!$db->num_rows($query))
	include View::Get('no_warn.html', $dir . $subdir);
else
	while($warn = $db->fetch_array($query))
		include View::Get('warn.html', $dir . $subdir);
$warn_list = ob_get_clean();
ob_start();
include View::Get('index.html', $dir . $subdir);
$content_main .= ob_get_clean();