<?php 
if (empty($user) or $user->lvl() < 9) { accss_deny(); }

// Мини настройки для RB

$content = '';
$page = 'Управление запросами';
$check  = false;

$menu->SetItemActive('reqests');

$sql = BD('SELECT * FROM `reqests` WHERE `answer`=1 ORDER BY id DESC');
$sql2 = BD('SELECT name FROM `reqests` WHERE `answer`=1 ORDER BY id DESC');
// Функция обработки заявки
if (!isset($_POST['userid']) && !isset($_POST['answer']))
{
}
elseif(isset($_POST['userid']) && isset($_POST['answer']) && $user->lvl() >= 8)
{
$answer = $_POST['answer'];
$userid = $_POST['userid'];
if ($answer != $check && $userid != $check)
{
if ($answer != $check)
{
$helperinfo = BD('SELECT * FROM `reqests` WHERE `id`='.$userid.'');
$helperresult = mysql_fetch_array($helperinfo,0);
	if ($helperresult['name'] != $check)
	{
		if ($answer == 'yes') {
			$content = '<div class="alert alert-success">Заявка успешно принята!</div>';
			BD("UPDATE `reqests` SET `answer`=3 WHERE `id`='".$userid."'")or die(mysql_error());
		} elseif ($answer == 'no') {
			$content = '<div class="alert alert-success">Заявка успешно отклонена!</div>';
			BD("UPDATE `reqests` SET `answer`=2 WHERE `id`='".$userid."'")or die(mysql_error());
		} elseif ($answer == 'un') {
			$content = '<div class="alert alert-success">Заявка успешно помечена непрочитанной!</div>';
			BD("UPDATE `reqests` SET `answer`=1 WHERE `id`='".$userid."'")or die(mysql_error());
		} elseif ($answer == 'del' && $user->lvl() >= 15) {
			$content = '<div class="alert alert-success">Заявка успешно удалена!</div>';
			BD("DELETE FROM `reqests` WHERE `id` = '".$userid."'")or die(mysql_error());
		} else {
			$content = '<div class="alert alert-danger">Подмена запроса Answer</div>';
		}
	}
}
else
{
	$content = '<div class="alert alert-danger">Выбери отказать или принять!</div>';
}
}
}
$sql = BD('SELECT * FROM `reqests` WHERE `answer`=1 ORDER BY id DESC');
$sql2 = BD('SELECT name FROM `reqests` WHERE `answer`=1 ORDER BY id DESC');
$checkanswers = mysql_fetch_array($sql2,0);
//Определяем первое место
$id = 1;

$check = "";

// Проверка наличия заявок
if ($checkanswers == $check)
{
$content .= '';
}
elseif($checkanswers != $check)
{
	$content .= '
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert"></button>
			Есть необработанные заявки!
		</div>
	';
}

$table_items = '';

while($reqests = mysql_fetch_assoc($sql,0)) {

	ob_start();
		include View::Get('admin/team_table_item.html');  
	$table_items .= ob_get_clean();	
}	
$sql = BD('SELECT * FROM `reqests` WHERE `answer`=3 ORDER BY id DESC');

$table_items2 = '';

while($reqests = mysql_fetch_assoc($sql,0)) {

	ob_start();
		include View::Get('admin/team_table_item2.html');  
	$table_items2 .= ob_get_clean();	
}	
$sql = BD('SELECT * FROM `reqests` WHERE `answer`=2 ORDER BY id DESC');

$table_items3 = '';

while($reqests = mysql_fetch_assoc($sql,0)) {

	ob_start();
		include View::Get('admin/team_table_item3.html');  
	$table_items3 .= ob_get_clean();	
}	

ob_start();
include View::Get('admin/team_moders.html');  
$content_main = ob_get_clean();