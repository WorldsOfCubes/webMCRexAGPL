<?php /* WEB-APP : WebMCR (С) 2013 NC22 | License : GPLv3 */
$timer_start = microtime();
$mem_start = memory_get_usage();
header('Content-Type: text/html; charset=UTF-8');
$queries = 0;

require_once('./system.php');
$db = new DB();
$db->connect('index');

loadTool('user.class.php');
MCRAuth::userLoad();


function GetRandomAdvice() {
	return ($quotes = @file(View::Get('sovet.txt'))) ? $quotes[rand(0, sizeof($quotes) - 1)] : "Советов нет";
}

$addition_events = '';
$content_main = '';
$content_side = '';
$content_js = '';
function LoadTinyMCE() {
	global $addition_events, $content_js;

	if (!file_exists(MCR_ROOT.'instruments/tiny_mce/tinymce.min.js'))
		return false;

	$tmce = 'tinymce.init({';
	$tmce .= 'selector: "textarea.form-control",';
	$tmce .= 'language : "ru",';
	$tmce .= 'plugins: "code preview image link",';
	$tmce .= 'toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | link image | preview",';
	$tmce .= '});';

	$addition_events .= $tmce;
	$content_js .= '<script type="text/javascript" src="'.BASE_URL.'instruments/tiny_mce/tinymce.min.js"></script>';

	return true;
}

function InitJS() {
	global $addition_events;

	$init_js = "var pbm; var way_style = '".DEF_STYLE_URL."'; var cur_style = '".View::GetURL()."'; var base_url  = '".BASE_URL."';";
	$init_js .= "window.onload = function () { mcr_init(); $('.stt').tooltip(); $('.spp').popover(); $('#datepicker').datepicker(); ".$addition_events." } ";
	return '<script type="text/javascript">'.$init_js.'</script>';
}

$content_advice = GetRandomAdvice();

if (!empty($user)) {

	$player = $user->name();
	$player_id = $user->id();
	$player_lvl = $user->lvl();
	$player_email = $user->email();
	if (empty($player_email))
		$player_email = lng('NOT_SET');
	$player_group = $user->getGroupName();
	$player_econ = $user->getEcon();
	$player_money = $user->getMoney();

	if ($user->group() == 4)
		$content_main .= View::ShowStaticPage('profile_verification.html', 'profile/', $player_email);
}

if ($config['offline'] and (empty($user) or $user->group() != 3)) {
	$menu = new Menu();
	$menu->SetItemActive('main');
	$content_menu = $menu->Show();
	$content_js .= InitJS();
	$mode = 'closed';
	$page = 'Технические работы';
	$content_main = View::ShowStaticPage('site_closed.html');
	include('./location/side.php');
	ob_start();
	include View::Get('index.html');
	$html_page = ob_get_clean();
	loadTool("template.class.php");
	$parser = new TemplateParser();
	$html_page = $parser->parse($html_page);
	echo $html_page;
	exit;
}
function accss_deny() {
	show_error('accsess_denied', 'Доступ запрещен');
}

function show_error($html, $page) {
	global $config, $content_js, $content_advice, $content_side, $user, $db;
	if (!empty($user)) {
		$player = $user->name();
		$player_id = $user->id();
		$player_lvl = $user->lvl();
		$player_email = $user->email();
		if (empty($player_email))
			$player_email = lng('NOT_SET');
		$player_group = $user->getGroupName();
		$player_econ = $user->getEcon();
		$player_money = $user->getMoney();
	}
	$menu = new Menu();
	$menu->SetItemActive('main');
	$content_menu = $menu->Show();
	$content_js .= InitJS();
	$mode = 'denied';
	$content_main = View::ShowStaticPage($html.'.html');
	include('./location/side.php');
	ob_start();
	include View::Get('index.html');
	$html_page = ob_get_clean();
	loadTool("template.class.php");
	$parser = new TemplateParser();
	$html_page = $parser->parse($html_page);
	echo $html_page;
	exit;
}

$menu = new Menu();


$mode = $config['s_dpage'];

if (isset($_GET['id']) and !isset($_GET['mode']))
	$mode = 'news_full'; elseif (isset($_GET['mode']))
	$mode = $_GET['mode'];
elseif (isset($_POST['mode']))
	$mode = $_POST['mode'];

if ($mode == 'side')
	$mode = $config['s_dpage'];
if ($mode == 'users')
	$mode = 'user';

switch ($mode) {
	case 'start':
		$page = 'Начать игру';
		$content_main = View::ShowStaticPage('start_game.html');
		break;
	case 'register':
	case 'restorepassword':
	case 'news':
		include('./location/news.php');
		break;
	case 'news_full':
		include('./location/news_full.php');
		break;
	case 'options':
		include('./location/options.php');
		break;
	case 'news_add':
		include('./location/news_add.php');
		break;
	case 'control':
		include('./location/admin.php');
		break;
	default:
		if (!preg_match("/^[a-zA-Z0-9_-]+$/", $mode) or !file_exists(MCR_ROOT.'/location/'.$mode.'.php'))
			$mode = "404";

		include(MCR_ROOT.'/location/'.$mode.'.php');
		break;
}
if ($user and !$user->pass_set()) {
	ob_start();
	include View::Get('wocpassunset.html', 'other/');
	$content_main = ob_get_clean().$content_main;
}
$content_menu = $menu->Show();
include('./location/side.php');
$content_js .= InitJS();
if (!empty($user) and $mode != 'pm')
	$content_side .= CheckPM();
ob_start();
include View::Get('index.html');
$html_page = ob_get_clean();
loadTool("template.class.php");
$parser = new TemplateParser();
$html_page = $parser->parse($html_page);
echo $html_page;
if(isset($config['debug']) and $config['debug']) {
	echo (memory_get_usage() - $mem_start)/1024 . "КБ памяти использовано";
	echo "\n<br />SQL запросов сделано: ".$queries."\r\n";
	$timer_end = microtime();
	$timer_total = round($timer_end - $timer_start, 7);
	echo "\n<br />Страница сгенерирована за ".$timer_total." секунд\r\n";
}
?>