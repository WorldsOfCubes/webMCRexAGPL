<?php 
//if (empty($user) or $user->lvl() < 1) { accss_deny(); }
		$content_main = '';
$menu->SetItemActive('banlist');
// Требует доработки
if (!empty($user) and $user->lvl() > 0){
	if ($query = BD("SELECT * FROM `banlist` WHERE `name`='".$user->name()."'")){
		$tmp_item = mysql_fetch_assoc($query,0);
		$message = ($tmp_item['type'] == 9)? lng('BAN_PERMANENT') : lng('BAN_TEMP');
//		$content_main .= "<div class='alert alert-danger'>$message</div>";
	} else {
//		$content_main .= "<div class='alert alert-success'>" . lng("NOT_BANED") . "</div>";
	}
}
print mysql_error(); 
$num_by_page = 25;

if (isset($_GET['page'])) $page = $_GET['page'];
	elseif (isset($_POST['page'])) $page = $_POST['page'];
	else $page = 1;

$menu->SetItemActive('banlist');

if ($page == 0) $page = 1;

$path = 'banlist/';
$first = ((int) $page - 1) * $num_by_page;
$last  = (int) $page * $num_by_page;
$query = BD("SELECT * FROM `banlist` ORDER BY `time` DESC LIMIT $first, $last");
print mysql_error();
$content_list = '';
$num = $first + 1;

while($tmp_item = mysql_fetch_assoc($query,0)) {
	$tmp_item['name'] = htmlspecialchars($tmp_item['name']);
	$tmp_item['reason'] = htmlspecialchars($tmp_item['reason']);
	$tmp_item['admin'] = htmlspecialchars($tmp_item['admin']);
	$tmp_item['time'] = date("d.m.Y в H:i:s", intval($tmp_item['time']));
	$tmp_item['temptime'] = (intval($tmp_item['temptime'])==0) ? 'Нет' : date("d.m.Y в H:i:s", intval($tmp_item['temptime']));
	$tmp_item['type'] = intval($tmp_item['type']);

	switch($tmp_item['type']){
		case 0: $tmp_item['type'] = 'Бан'; break;
		case 1: $tmp_item['type'] = 'Бан по IP'; break;
		case 2: $tmp_item['type'] = 'Предупреждение'; break;
		case 3: $tmp_item['type'] = 'Кик'; break;
		case 4: $tmp_item['type'] = 'Штраф'; break;
		case 5: $tmp_item['type'] = 'Разбан'; break;
		case 6: $tmp_item['type'] = 'Тюрьма'; break;
		case 9: $tmp_item['type'] = 'Перманентный бан'; break;

		default: $tmp_item['type'] = 'Неизвестно'; break;
	}
	
	ob_start();
		include View::Get('ban_item.html', $path);  
	$content_list .= ob_get_clean();
	$num++;
}

ob_start();
include View::Get('ban_list.html', $path);
$content_main .= ob_get_clean();

$result = BD("SELECT COUNT(*) FROM `banlist`");
$line = mysql_fetch_array($result);
$view = new View("banlist/");
$content_main .= $view->arrowsGenerator(Rewrite::GetURL('banlist'), $page, $line[0], $num_by_page, "pagin");

$page = lng('BANLIST');