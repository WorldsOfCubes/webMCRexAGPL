<?php

if (empty($user) or $user->group() == 4) { accss_deny(); }

$check = "";
$page = 'Набор в модераторы - форма заявки';


$sql2 = BD('SELECT * FROM `reqests` WHERE `name`="'.$_SESSION['user_name'].'"');
$resultsql2 = mysql_fetch_array($sql2,0);

if (!isset($_POST['playername']) && !isset($_POST['realname']) && !isset($_POST['surname']) && !isset($_POST['old']) && !isset($_POST['skype']))
	{
	}
elseif (isset($_POST['playername']) && isset($_POST['realname']) && isset($_POST['surname']) && isset($_POST['old']) && isset($_POST['skype']))
	{
$realname = $_POST['realname'];
$surname  = $_POST['surname'];
$old      = $_POST['old'];
$skype    = $_POST['skype'];
$comment  = $_POST['comment'];
if ($realname != $check && $surname != $check && $old != $check && $skype != $check && $comment != $check)
	{
		if (preg_match('/^[а-яА-Я]+/iu', $realname) && preg_match('/^[а-яА-Я]+/iu', $surname) && preg_match('/^[0-9]+/iu', $old) && preg_match('/^[a-zA-Z0-9]+/iu', $skype) && preg_match('/^[а-яА-Яa-zA-Z0-9 ]+/iu', $comment))
		{
			if ((strlen($realname) < 20) && (strlen($surname) < 20) && (strlen($old) < 3) && (strlen($skype) < 20))
			{
				if ($resultsql2['name'] != $_SESSION['user_name'])
				{

			$content = '<div class="alert alert-success">Заявка отправлена</div>';
			BD("INSERT INTO `reqests` (name, realname, surname, old, skype, comment) VALUES ('".$player."', '".TextBase::SQLSafe($realname)."', '".TextBase::SQLSafe($surname)."', '".TextBase::SQLSafe($old)."', '".TextBase::SQLSafe($skype)."', '".TextBase::SQLSafe($comment)."')");
				}
				elseif ($resultsql2['name'] == $_SESSION['user_name'])
				{
				$content = '<div class="alert alert-danger">Вы уже подали заявку!</div>';
				}else $content ="дебаг";
			}
			else
			{

			$content = '<div class="alert alert-danger">Слишком длинные значения! Попробуйте написать короче!</div>';
			}
		}
		else
		{

		$content = '<div class="alert alert-danger">Вы использовали запрещённые символы!</div>';
		}
	}
	else
	{

	$content = '<div class="alert alert-danger">Не все поля заполнены!</div>';
	}
}

$sql2 = BD('SELECT * FROM `reqests` WHERE `name`="'.$_SESSION['user_name'].'"');
$resultsql2 = mysql_fetch_array($sql2,0);

if ($resultsql2['answer'] == $check)
{
$content = '';
}
elseif ($resultsql2['answer'] == '1')
{
$content = '
<div class="alert alert-info alert-block">
		<button type="button" class="close" data-dismiss="alert"></button>
	Твоя заявка на рассмотрении у администрации.
</div>
';
}
elseif ($resultsql2['answer'] == 2)
{
$content = '
<div class="alert alert-danger alert-block">
		<button type="button" class="close" data-dismiss="alert"></button>
		Твоя заявка отклонена администратором.
</div>
';
}
elseif ($resultsql2['answer'] == 3)
{
$content = '
<div class="alert alert-success alert-block">
		<button type="button" class="close" data-dismiss="alert"></button>
		Твою заявку приняли! Скоро в тебе в скайп отпишет администратор.
</div>
';
}

ob_start();
include View::Get('join_team.html');  
$content_main = ob_get_clean();