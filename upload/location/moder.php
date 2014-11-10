<?php


if (empty($user) or $user->group() == 4) { accss_deny(); }

$check = "";
$page = 'Набор в модераторы - форма заявки';


$sql2 = $db->execute('SELECT * FROM `reqests` WHERE `name`="'.$_SESSION['user_name'].'"');
$resultsql2 = $db->fetch_array($sql2,0);

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
			if ((mb_strlen($realname, "utf8") < 20) && (mb_strlen($surname, "utf8") < 20) && (mb_strlen($old, "utf8") < 3) && (mb_strlen($skype, "utf8") < 20))
			{
				if ($resultsql2['name'] != $_SESSION['user_name'])
				{

			$content = View::Alert("Заявка отправлена</div>", 'success');
			$db->execute("INSERT INTO `reqests` (name, realname, surname, old, skype, comment) VALUES ('".$player."', '". $db->safe($realname) ."', '". $db->safe($surname) ."', '". $db->safe($old) ."', '". $db->safe($skype) ."', '". $db->safe($comment) ."')");
				}
				elseif ($resultsql2['name'] == $_SESSION['user_name'])
				{
				$content = View::Alert("Вы уже подали заявку!");
				}else $content ="дебаг";
			}
			else
			{

			$content = View::Alert("Слишком длинные значения! Попробуйте написать короче!");
			}
		}
		else
		{

		$content = View::Alert("Вы использовали запрещённые символы!");
		}
	}
	else
	{

	$content = View::Alert("Не все поля заполнены!");
	}
}

$sql2 = $db->execute('SELECT * FROM `reqests` WHERE `name`="'.$_SESSION['user_name'].'"');
$resultsql2 = $db->fetch_array($sql2,0);

if ($resultsql2['answer'] == $check)
{
$content = '';
}
elseif ($resultsql2['answer'] == '1')
{
$content = View::Alert("Твоя заявка на рассмотрении у администрации.", 'info');
}
elseif ($resultsql2['answer'] == 2)
{
$content = View::Alert("Твоя заявка отклонена администратором.");
}
elseif ($resultsql2['answer'] == 3)
{
$content = View::Alert("Твою заявку приняли.", 'success');
}

ob_start();
include View::Get('join_team.html');  
$content_main = ob_get_clean();