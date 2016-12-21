<?php
define('MCR', '2.5 R2');
define('DEV', false);
define('EX', '2');
define('PROGNAME', 'webMCRex '.MCR);
define('FEEDBACK', '<a href="http://webmcrex.com">'.PROGNAME.'</a> &copy; 2013-' . date("Y") . ' <a href="http://webmcr.com">NC22</a>&amp;<a href="http://WorldsOfCubes.NET">WoC Team</a>');

class webMCRex {
	const version = MCR;
	const dev = DEV;
	public static function checkVersion($force = false) {
		global $checkverrunned;
		if((time() - sqlConfigGet('latest-update-date') > 3600 or $force) and empty($checkverrunned)) {
			$socket = curl_init();
			$url = (self::dev)?'https://api.webmcrex.com/?ver=latest':'https://api.webmcrex.com/?ver=stable';
			curl_setopt_array($socket, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_SSL_VERIFYHOST => 0
			));
			$response = curl_exec($socket);
			$error = curl_error($socket);
			$http_code = curl_getinfo($socket, CURLINFO_HTTP_CODE);
			curl_close($socket);
			if ($http_code == 200) {
				sqlConfigSet('latest-update-date', time());
				$response = explode(':', $response);
				sqlConfigSet('latest-version-tag', $response[0]);
				sqlConfigSet('latest-version-name', $response[1]);
			} else vtxtlog('Error: Unable to connect to webMCRex version server with error: ' . $http_code . ' ' . $error);
			$checkverrunned = true;
		}
		return strtolower(self::version) == str_replace('_', ' ', sqlConfigGet('latest-version-tag'));
	}
}

/* Base class for objects with Show method */

Class View {

	const def_theme = 'Default';

	protected $st_subdir;

	public function View($style_subdir = '') {

		if (!$style_subdir)
			$style_subdir = false;

		$this->st_subdir = $style_subdir;
	}

	// ToDo transform output

	public function ShowPage($way, $out = false) {
		global $config;

		ob_start();

		include self::Get($way, $this->st_subdir);

		return ob_get_clean();
	}

	public static function ShowStaticPage($way, $st_subdir = false, $out = false) {
		global $config;

		ob_start();

		include self::Get($way, $st_subdir);

		return ob_get_clean();
	}

	protected function GetView($way) {

		return self::Get($way, $this->st_subdir);
	}

	public static function GetURL($way = false) {
		global $config;

		$current_st_url = empty($config['s_theme']) ? DEF_STYLE_URL : STYLE_URL.$config['s_theme'].'/';

		if (!$way)
			return $current_st_url;

		if (DEF_STYLE_URL === $current_st_url)
			return BASE_URL . DEF_STYLE_URL.$way; else return (file_exists(MCR_STYLE.$config['s_theme'].'/'.$way) ? $current_st_url : DEF_STYLE_URL).$way;
	}

	public static function URL($way = false) {

		echo self::GetURL($way);
	}

	public static function Alert($text, $state = 'danger') {

		return "<div class=\"alert alert-$state\">$text</div>";
	}

	public static function Get($way, $base_ = false) {
		global $config;
		loadTool("template.class.php");
		$base = ($base_) ? $base_ : '';

		if (empty ($config['s_theme']))
			$theme_dir = ''; else {

			if ($config['s_theme'] === self::def_theme)
				return TemplateParser::MakeCache(MCR_STYLE.self::def_theme.'/'.$base.$way,
					str_replace('/', '', $base) . '_' . str_replace('/', '', $way) . '_' . self::def_theme);

			$theme_dir = $config['s_theme'].'/';
		}

		return TemplateParser::MakeCache(MCR_STYLE.((file_exists(MCR_STYLE.$theme_dir.$base.$way)) ? $theme_dir : self::def_theme.'/').$base.$way,
			str_replace('/', '', $base) . '_' . str_replace('/', '', $way) . '_' . $config['s_theme']);
	}

	public function arrowsGenerator($link, $curpage, $itemsnum, $per_page, $prefix = false) {
		global $config;
		if (!$prefix) { // Default arrows style

			$prefix = 'common';
			$st_subdir = 'other/';
		} else

			$st_subdir = $this->st_subdir;

		$numoflists = ceil($itemsnum / $per_page);
		$arrows = '';

		if ($numoflists > 10 and $curpage > 4) {

			$showliststart = $curpage - 4;
			$showlistend = $curpage + 5;

			if ($showliststart < 1)
				$showliststart = 1;

			if ($showlistend > $numoflists)
				$showlistend = $numoflists;
		} else {

			$showliststart = 1;

			if ($numoflists < 10)
				$showlistend = $numoflists; else                   $showlistend = 10;
		}

		ob_start();

		if ($numoflists > 1) {

			if ($curpage > 1) {

				if ($curpage - 4 > 1) {
					$var = 1;
					$text = '<<';
					include $this->Get($prefix.'_list_item.html', $st_subdir);
				}

				$var = $curpage - 1;
				$text = '<';
				include $this->Get($prefix.'_list_item.html', $st_subdir);
			}

			for ($i = $showliststart; $i <= $showlistend; $i++) {

				$var = $i;
				$text = $i;

				if ($i == $curpage)
					include $this->Get($prefix.'_list_item_selected.html', $st_subdir); else                include $this->Get($prefix.'_list_item.html', $st_subdir);
			}

			if ($curpage < $numoflists) {

				$var = $curpage + 1;
				$text = '>';
				include $this->Get($prefix.'_list_item.html', $st_subdir);

				if ($curpage + 5 < $numoflists) {
					$var = $numoflists;
					$text = '>>';
					include $this->Get($prefix.'_list_item.html', $st_subdir);
				}
			}
		}

		$arrows = ob_get_clean();

		if ($arrows) {

			ob_start();

			include $this->Get($prefix.'_list.html', $st_subdir);

			return ob_get_clean();
		}

		return '';
	}
}

class Item extends View {

	protected $type;
	protected $id;

	protected $db;

	public function __construct($id, $type, $db, $style_sd = false) {

		parent::View($style_sd);

		$this->id = (int)$id;
		$this->db = $db;

		$this->type = (int)$type;
	}

	public function type() {

		if (!$this->Exist())
			return false;

		return $this->type;
	}

	public function id() {

		return $this->id;
	}

	public function Exist() {

		if ($this->id)
			return true;
		return false;
	}

	public function Delete() {
		global $db;
		if (!$this->Exist())
			return false;

		$db->execute("DELETE FROM `{$this->db}` WHERE `id`='".$this->id."'");

		$this->id = false;
		return true;
	}
}

class ItemType {  // stock types

	const News = 1;
	const Comment = 2;
	const Skin = 3;
	const Server = 4;
	const PM = 5;

	/** @const */
	public static $SQLConfigVar = array(
		'rcon-port',
		'rcon-serv',
		'rcon-pass',
		'next-reg-time',
		'email-verification',
		'email-verification-salt',
		'email-name',
		'email-mail',
		'json-verification-salt',
		'protection-key',
		'launcher-version',
		'game-link-win',
		'game-link-osx',
		'game-link-lin',
		'smtp-user',
		'smtp-pass',
		'smtp-host',
		'smtp-port',
		'smtp-hello',
		'latest-update-date',
		'latest-version-tag',
		'latest-version-name',
	);
}

Class TextBase {

	public static function HTMLDestruct($text) {

		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}

	public static function HTMLRestore($text) {

		return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	}

	public static function StringLen($text) {

		return mb_strlen($text, 'UTF-8');
	}

	public static function SQLSafe($text) {
		global $db;
		vtxtlog("Using old method TextBase::SQLSafe()");
		return $db->safe($text);
	}

	public static function CutString($text, $from = 0, $to = 255) {

		return mb_substr($text, $from, $to, 'UTF-8');
	}

	public static function CutWordWrap($text) {

		return str_replace(array("\r\n", "\n", "\r"), '', $text);
	}

	/* WordWrap - разбиение непрерывного текстового сообщения пробелами	*/

	public static function WordWrap($text, $width = 60, $break = "\n") {

		return preg_replace('#([^\s]{'.$width.'})#u', '$1'.$break, $text);
	}
}

Class DB {
	private $link;
	private $method;
	private $sql_config;

	public function connect($log_script, $die = true) {
		global $config, $bd_names;
		$this->method = (isset($config['db_method'])) ? $config['db_method'] : 'mysql'; //правит совместимость со старыми версиями. Если были поставлены моды для webMCR 2.35 и более ранних версий, то ничего не упадет.
		switch ($this->method) {
			case 'mysql':
				$this->link = mysql_connect($config['db_host'].':'.$config['db_port'], $config['db_login'], $config['db_passw']);
				if (!$this->link) {
					if ($die)
						die(lng('BD_ERROR').lng('BD_AUTH_FAIL')); else return 1;
				}
				if (!mysql_select_db($config['db_name'], $this->link)) {
					if ($die)
						die(lng('BD_ERROR').lng('BD_AUTH_FAIL')); else return 2;
				}
				break;
			case 'mysqli':
			default:
				$this->link = mysqli_connect($config['db_host'], $config['db_login'], $config['db_passw'], '', $config['db_port']);

				if (!$this->link) {
					if ($die)
						die(lng('BD_ERROR').lng('BD_AUTH_FAIL')); else return 1;
				}
				if (!mysqli_select_db($this->link, $config['db_name'])) {
					if ($die)
						die(lng('BD_ERROR').lng('BD_AUTH_FAIL')); else return 2;
				}
		}
		$this->execute("SET time_zone = '".date('P')."'");
		$this->execute("SET character_set_client='utf8'");
		$this->execute("SET character_set_results='utf8'");
		$this->execute("SET collation_connection='utf8_general_ci'");
		$query = $this->execute("SELECT * FROM `{$bd_names['data']}`");
		while ($query and $temp_cfg = $this->fetch_array($query))
			$this->sql_config[$temp_cfg['property']] = $temp_cfg['value'];
		if ($log_script and $config['action_log'])
			ActionLog($log_script);
		if ($die)
			CanAccess(2);
		return 0;
	}

	public function sql_config_get($property) {
		if (!in_array($property, ItemType::$SQLConfigVar))
			return false;
		return (isset($this->sql_config[$property]))?$this->sql_config[$property]:0;
	}

	public function sql_config_set($property, $value) {
		global $db, $bd_names;

		if (!in_array($property, ItemType::$SQLConfigVar))
			return false;
		$result = $db->execute("INSERT INTO `{$bd_names['data']}` (value,property) VALUES ('".$db->safe($value)."','".$db->safe($property)."') ON DUPLICATE KEY UPDATE `value`='".$db->safe($value)."'");
		if ($result)
			$this->sql_config[$property] = $value;
		return ($result) ? true : false;
	}

	public function execute($query, $log = true) {
		global $queries;
		$queries++;
		switch ($this->method) {
			case 'mysql':
				$result = mysql_query($query, $this->link);
				break;
			case 'mysqli':
			default:
				$result = mysqli_query($this->link, $query);
				break;
		}
		if ($log and is_bool($result) and $result == false and function_exists("vtxtlog"))
			vtxtlog('SQLError: '.$this->error().' in query ['.$query.']');
		return $result;
	}

	public function safe($text) {
		switch ($this->method) {
			case 'mysql':
				return mysql_real_escape_string($text, $this->link);
				break;
			case 'mysqli':
			default:
				return mysqli_real_escape_string($this->link, $text);
				break;
		}
	}

	public function fetch_assoc($query) {
		switch ($this->method) {
			case 'mysql':
				return mysql_fetch_assoc($query);
				break;
			case 'mysqli':
			default:
				return mysqli_fetch_assoc($query);
				break;
		}
	}

	public function fetch_array($query, $result_type = MYSQLI_BOTH) {
		switch ($this->method) {
			case 'mysql':
				return mysql_fetch_array($query, $result_type);
				break;
			case 'mysqli':
			default:
				return mysqli_fetch_array($query, $result_type);
				break;
		}
	}

	public function num_rows($query) {
		switch ($this->method) {
			case 'mysql':
				return mysql_num_rows($query);
				break;
			case 'mysqli':
			default:
				return mysqli_num_rows($query);
				break;
		}
	}

	public function error() {
		switch ($this->method) {
			case 'mysql':
				return mysql_error($this->link);
				break;
			case 'mysqli':
			default:
				return mysqli_error($this->link);
				break;
		}
	}

	public function insert_id() {
		switch ($this->method) {
			case 'mysql':
				return mysql_insert_id($this->link);
				break;
			case 'mysqli':
			default:
				return mysqli_insert_id($this->link);
				break;
		}
	}

	public function affected_rows() {
		switch ($this->method) {
			case 'mysql':
				return mysql_affected_rows($this->link);
				break;
			case 'mysqli':
			default:
				return mysqli_affected_rows($this->link);
				break;
		}
	}

	public function fetch_row($query) {
		switch ($this->method) {
			case 'mysql':
				return mysql_fetch_row($query);
				break;
			case 'mysqli':
			default:
				return mysqli_fetch_row($query);
				break;
		}
	}
}

Class EMail {
	const ENCODE = 'utf-8';

	public static function Send($mail_to, $subject, $message) {
		global $config;

		$headers = array();
		$headers[] = "Reply-To: ".sqlConfigGet('email-mail');
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-Type: text/html; charset=\"".self::ENCODE."\"";
		$headers[] = "Content-Transfer-Encoding: 8bit";
		$headers[] = "From: \"".sqlConfigGet('email-name')."\" <".sqlConfigGet('email-mail').">";
		$headers[] = "To: ".$mail_to." <".$mail_to.">";
		$headers[] = "X-Priority: 3";
		$headers[] = "X-Mailer: PHP/".phpversion();

		$headers = implode("\r\n", $headers);

		if (!$config['smtp_tls'])
			$subject = '=?'.self::ENCODE.'?B?'.base64_encode($subject).'?=';

		return ($config['smtp']) ? self::smtpmail($mail_to, $subject, $message, $headers) : mail($mail_to, $subject, $message, $headers);
	}

	private static function smtpmail($mail_to, $subject, $message, $headers) {
		global $config;
		$smtp_user = sqlConfigGet('smtp-user');
		$smtp_pass = sqlConfigGet('smtp-pass');
		$smtp_host = sqlConfigGet('smtp-host');
		$smtp_port = (int)sqlConfigGet('smtp-port');
		$smtp_hello = sqlConfigGet('smtp-hello');
		if ($config['smtp_tls']) {
			loadTool("mail.class.php");
			$m = new Mail;
			$m->From(sqlConfigGet('email-name').';'.sqlConfigGet('email-mail'));
			$m->To($mail_to);
			$m->Subject($subject);
			$m->Body($message);
			$m->Priority(3);
			$m->smtp_on("ssl://".$smtp_host, $smtp_user, $smtp_pass, $smtp_port);
			return $m->Send();
		} else {
			$send = "Date: ".date("D, d M Y H:i:s")." UT\r\n";
			$send .= "Subject: {$subject}\r\n";
			$send .= $headers."\r\n\r\n".$message."\r\n";

			if (!$socket = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, 10)) {
				vtxtlog('[SMPT] '.$errno." | ".$errstr);
				return false;
			}

			stream_set_timeout($socket, 10);

			if (!self::server_action($socket, false, "220") or !self::server_action($socket, $smtp_hello." ".$smtp_host."\r\n", "250", 'Приветствие сервера недоступно')
			)
				return false;

			if (!empty($smtp_user))
				if (!self::server_action($socket, "AUTH LOGIN\r\n", "334", 'Нет ответа авторизации') or !self::server_action($socket, base64_encode($smtp_user)."\r\n", "334", 'Неверный логин авторизации') or !self::server_action($socket, base64_encode($smtp_pass)."\r\n", "235", 'Неверный пароль авторизации')
				)
					return false;

			if (!self::server_action($socket, "MAIL FROM: <".$smtp_user.">\r\n", "250", 'Ошибка MAIL FROM') or !self::server_action($socket, "RCPT TO: <".$mail_to.">\r\n", "250", 'Ошибка RCPT TO') or !self::server_action($socket, "DATA\r\n", "354", 'Ошибка DATA') or !self::server_action($socket, $send."\r\n.\r\n", "250", 'Ошибка сообщения')
			)
				return false;

			self::server_action($socket, "QUIT\r\n");
			return true;
		}
	}

	private static function server_action($socket, $command = false, $correct_response = false, $error_mess = false, $line = __LINE__) {

		if ($command)
			fputs($socket, $command);
		if ($correct_response) {

			$server_response = '';
			while (substr($server_response, 3, 1) != ' ') {
				if ($server_response = fgets($socket, 256))
					continue;

				if ($error_mess)
					vtxtlog('[SMPT] '.$error_mess.' Line: '.$line);
				return false;
			}
			$code = substr($server_response, 0, 3);
			if ($code == $correct_response)
				return true;
		}

		if ($error_mess)
			vtxtlog('[SMPT] '.$error_mess.' | Code: '.$code.' Line: '.$line);
		fclose($socket);

		if ($correct_response)
			return false;
		return true;
	}
}

Class Message {

	/*	 
	 Comment - Валидация короткого сообщения, для хранения в БД и вывода на странице

	 Обрезать до 255 символов
	 Расформировать HTML
	 Заменить все переносы строк на <br>
	 Удалить оставшиеся символы переноса строки
	 
	*/

	public static function Comment($text) {
		global $db;
		$text = trim($text);
		$text = TextBase::HTMLDestruct($text);
		$text = preg_replace('/(\\R{2})\\R++/Usi', '$1', $text);
		$text = nl2br($text);
		$text = TextBase::CutWordWrap($text);
		$text = TextBase::CutString($text);

		return $db->safe($text);
	}

	/*
	
	 RestoreCom - Привести короткое сообщение в редактируемый вид
	
	*/

	public static function RestoreCom($string) {

		return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
	}

	// TODO BBEncode

	public static function BBDecode($text) {

		$text = preg_replace("/\[b\](.*)\[\/b\]/Usi", "<b>\\1</b>", $text);
		$text = preg_replace("/\[u\](.*)\[\/u\]/Usi", "<u>\\1</u>", $text);
		$text = preg_replace("/\[i\](.*)\[\/i\]/Usi", "<i>\\1</i>", $text);
		$text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\](.*)\[\/color\]/Usi", "<span style=\"color:\\1\">\\2</span>", $text);
		$text = preg_replace("/\[url=(?:&#039;|&quot;)http:\/\/([^<]+)(?:&#039;|&quot;)](.*)\[\/url]/Usi", "<a href=\"http://\\1\">\\2</a>", $text, 3);

		$tmp = $text;

		while (strcmp($text = preg_replace("/\[quote=(?:&#039;|&quot;)(.*)(?:&#039;|&quot;)\](.+?)\[\/quote\]/Uis", "<div class=\"comment-quote\"><div class=\"comment-quote-a\">\\1 сказал(a):</div><div class=\"comment-quote-c\">\\2</div></div>", $tmp), $tmp) != 0)
			$tmp = $text;

		while (strcmp($text = preg_replace("/\[spoiler=(.*)\](.*)\[\/spoiler]/Usi", "<div class=\"panel-group\"><div class=\"panel panel-default\"><div class=\"panel-heading\"><a class=\"spoiler-tlink\" data-toggle=\"collapse\" href=\"\"><strong>\\1</strong></a></div><div class=\"spoiler-ttext panel-collapse collapse\"><div class=\"panel-body\">\\2</div></div></div></div>", $tmp), $tmp) != 0)
			$tmp = $text;

		return $text;
	}
}

class ItemLike {
	private $id;
	private $type;
	private $user_id;

	private $bd_content;
	private $db;

	public function ItemLike($item_type, $item_id, $user_id) {
		global $bd_names;

		$this->id = false;
		$this->bd_content = false;

		switch ($item_type) {
			case ItemType::News:
				$this->bd_content = $bd_names['news'];
				break;
			case ItemType::Comment:
				$this->bd_content = $bd_names['comments'];
				break;
			case ItemType::Skin:

				if (array_key_exists('sp_skins', $bd_names))

					$this->bd_content = $bd_names['sp_skins'];

				break;
			default:
				return false;
				break;
		}

		$this->db = $bd_names['likes'];

		$this->id = (int)$item_id;
		$this->type = (int)$item_type;
		$this->user_id = (int)$user_id;
	}

	public function Like($dislike = false) {
		global $db;
		if (!$this->bd_content)
			return 0;

		$var = (!$dislike) ? 1 : -1;

		$result = $db->execute("SELECT `var` FROM `{$this->db}` WHERE `user_id` = '".$this->user_id."' AND `item_id` = '".$this->id."' AND `item_type` = '".$this->type."'");

		if (!$db->num_rows($result)) {

			$db->execute("INSERT INTO `{$this->db}` (`user_id`, `item_id`, `item_type`, `var`) VALUES ('".$this->user_id."', '".$this->id."', '".$this->type."', '".$var."')");

			if (!$dislike)
				$db->execute("UPDATE `{$this->bd_content}` SET `likes` = `likes` + 1 WHERE `id` = '".$this->id."'"); else
				$db->execute("UPDATE `{$this->bd_content}` SET `dislikes` = `dislikes` + 1 WHERE `id` = '".$this->id."'");

			return 1;
		} else {

			$line = $db->fetch_array($result, MYSQLI_NUM);

			if ((int)$line[0] == (int)$var)
				return 0;

			$db->execute("UPDATE `{$this->db}` SET `var` = '".$var."' WHERE `user_id` = '".$this->user_id."' AND `item_id` = '".$this->id."' AND `item_type` = '".$this->type."'");

			if (!$dislike)
				$db->execute("UPDATE `{$this->bd_content}` SET `likes` = `likes` + 1, `dislikes` = `dislikes` - 1  WHERE `id` = '".$this->id."'"); else
				$db->execute("UPDATE `{$this->bd_content}` SET `likes` = `likes` - 1, `dislikes` = `dislikes` + 1 WHERE `id` = '".$this->id."'");

			return 2;
		}
	}
}

Class Menu extends View {
	private $menu_items;
	private $menu_fname;

	public function Menu($style_sd = false, $auto_load = true, $mfile = 'instruments/menu_items.php') {
		global $config, $db;

		parent::View($style_sd);

		$this->menu_fname = $mfile;

		if ($auto_load) {
			$menu_items = array();

			if(file_exists(MCR_ROOT.$this->menu_fname)) $this->convert_file_to_db();

			$query = $db->execute("SELECT * FROM `menu` ORDER BY `priority` DESC");
			while ($item = $db->fetch_array($query)) {
				$menu_items [$item['txtid']] = array(
					'name' => str_replace('{PM_CHECKNEW}', CheckPMMenu(), $item['name']),
					'align' => (int) $item['align'],
					'url' => $item['url'],
					'parent_id' => ($item['parent_id'] == "-1")? -1:$item['parent_id'],
					'lvl' => (int) $item['lvl'],
					'permission' => ($item['permission'] == "-1")? -1:$item['permission'],
					'active' => (boolean) $item['active'],
					'inner_html' => $item['inner_html'],
					'priority' => $item['priority'],
				);
			}

			$this->menu_items = $menu_items;
		} else $this->menu_items = array();
	}

	private static function array_insert_before(&$array, $var, $key_name) {

		$index = array_search($key_name, array_keys($array));
		if ($index === false)
			return false;

		$part_array = array_splice($array, 0, $index);
		$array = array_merge($part_array, $var, $array);
		return true;
	}

	private function SaveMenu() {}

	private function convert_file_to_db() {
		global $db;
		$menu_items = array(0 => array(), 1 => array());
		require(MCR_ROOT.$this->menu_fname);
		unlink(MCR_ROOT.$this->menu_fname);
		$this->menu_items = $menu_items;
		$priority = 0;
		$query = '';
		$result = true;
		for($c = 0; $c < 2; $c++)
			foreach($this->menu_items[$c] as $txtid => $array){
				$query = "INSERT INTO `menu` (`align`,`txtid`,`name`,`url`,`parent_id`,`lvl`,`permission`,`active`,`inner_html`,`system`,`priority`)"
						. " VALUES ($c,'$txtid','{$array['name']}','{$array['url']}','{$array['parent_id']}','{$array['lvl']}','{$array['permission']}',0,'',0,$priority)"
						. " ON DUPLICATE KEY UPDATE `name` = '{$array['name']}', `url` = '{$array['url']}', `parent_id` = '{$array['parent_id']}', `lvl` = '{$array['lvl']}', `permission` = '{$array['permission']}', `priority` = $priority;\n\n";
				$priority--;
				$result = $db->execute($query) and $result;
			}

		return (is_bool($result) and $result == false) ? false : true;
	}

	public function DeleteItem($menu, $key) {
		global $db;
//		if ($menu == 'left')
//			$menu_id = 0;

		$index = array_search($key, array_keys($this->menu_items));
		if ($index === false)
			return false;

		array_splice($this->menu_items, $index, 1);
		return $db->execute("DELETE FROM `menu` WHERE `txtid`='{$db->safe($key)}'");
	}

	/* TODO -- add config trigger checker */


	public function SaveItem($id, $menu, $info, $insert_before = false) {
		global $db;
		$menu_id = 1;
		if ($menu == 'left')
			$menu_id = 0;

		if (!is_array($info) or !$info['name'] or !is_int($info['lvl']) or (is_int($info['parent_id']) and $info['parent_id'] != -1) or (isset($info['config']) and is_int($info['config']) and $info['config'] != -1) or (is_int($info['permission']) and $info['permission'] != -1))
			return false;

		if (array_key_exists($id, $this->menu_items))
			return false;

		$new_item = array(

			'name' => $info['name'],
			'url' => $info['url'],
			'parent_id' => ($info['parent_id']) ? $info['parent_id'] : -1,
			'lvl' => (is_int($info['lvl']))? $info['lvl']: - 1,
			'permission' => $info['permission'],
			'config' => (isset($info['config'])) ? $info['config'] : -1,
			'active' => (isset($info['active'])) ? $info['active'] : false,
			'inner_html' => '',
			'align' => $menu_id,
			'priority' => ($insert_before)?$this->menu_items[$insert_before]['priority'] + 1: 0,
		);

		if ($insert_before) {
			$query = $db->execute("INSERT INTO `menu` (`align`, `txtid`, `name`, `url`, `parent_id`, `lvl`, `permission`, `active`, `inner_html`, `priority`)"
				. " VALUES ('{$db->safe($menu_id)}','{$db->safe($id)}','{$db->safe($new_item['name'])}','{$db->safe($new_item['url'])}','{$db->safe($new_item['parent_id'])}','{$db->safe($new_item['lvl'])}','{$db->safe($new_item['permission'])}','{$db->safe($new_item['active'])}','{$db->safe($new_item['inner_html'])}','{$db->safe($new_item['priority'])}')");
			if (!$query) return false;
			if (!self::array_insert_before($this->menu_items, array($id => $new_item), $insert_before)) {
				$this->menu_items[$id] = $new_item;
			}
		} else {
			$query = $db->execute("INSERT INTO `menu` (`align`, `txtid`, `name`, `url`, `parent_id`, `lvl`, `permission`, `active`, `inner_html`, `system`)"
								. " VALUES ('{$db->safe($menu_id)}','{$db->safe($id)}','{$db->safe($new_item['name'])}','{$db->safe($new_item['url'])}','{$db->safe($new_item['parent_id'])}','{$db->safe($new_item['lvl'])}','{$db->safe($new_item['permission'])}','{$db->safe($new_item['active'])}','{$db->safe($new_item['inner_html'])}')");
			if (!$query) return false;
			$this->menu_items[$id] = $new_item;
		}

		return true;
	}

	public function AddItem($name, $url, $active = false, $menu = 'left') {

		foreach ($this->menu_items as $key => $value)

				if ($value['name'] == $name)
					return $key;

		$menu_id = 1;
		if ($menu == 'left')
			$menu_id = 0;

		$new_key = sizeof($this->menu_items);

		$this->menu_items[$new_key] = array(

			'name' => $name, 'url' => $url, 'parent_id' => -1, 'lvl' => -1, 'permission' => -1, 'active' => $active, 'inner_html' => '', 'align' => $menu_id,);

		return $new_key;
	}

	public function IsItemExists($item_key) {

		if (!array_key_exists($item_key, $this->menu_items))
			return false;

		return $this->menu_items[$item_key]['align'];
	}

	public function SetItemActive($item_key) {
		$menu_id = $this->IsItemExists($item_key);
		if ($menu_id === false)
			return false;
		$this->menu_items[$item_key]['active'] = true;
		return true;
	}

	private function show_item ($id, &$item, $kid = false) {
		global $user;
		$c = 0; $sub = '';
		if ((!$user and -1 != $item['lvl']) or ($user and $user->lvl() < $this->menu_items[$id]['lvl']) or ($this->menu_items[$id]['permission'] != '-1' and !$user) or ($this->menu_items[$id]['permission'] != '-1' and $user and !$user->getPermission($this->menu_items[$id]['permission'])))
			return '';
		foreach ($this->menu_items as $tmp_id => $tmp_item)
			if ($tmp_item['parent_id'] and $tmp_item['parent_id'] == $id) {
				$sub .= $this->show_item($tmp_id, $tmp_item, true);
				if ($tmp_item['active'])
					$item ['active'] = true;
				$c++;
			}
		ob_start();
		include View::Get(($c)?($kid)?'menu/item_subdropdown.html':'menu/item_dropdown.html':'menu/item.html');
		return ob_get_clean();
	}
	public function Show() {
		$menu = array("pull-left"=>'',"pull-right"=>'');
		foreach ($this->menu_items as $id => $item)
			if ($item['parent_id'] == -1 and $item['align'] == 1)
				$menu["pull-right"] .= $this->show_item($id, $item);
		foreach ($this->menu_items as $id => $item)
			if ($item['parent_id'] == -1 and $item['align'] == 0)
				$menu["pull-left"] .= $this->show_item($id, $item);
		ob_start();
		$menu_align = "pull-left";
		include View::Get('menu/menu.html');
		$menu_align = "pull-right";
		include View::Get('menu/menu.html');
//		var_dump($this->menu_items);
		return ob_get_clean();
	}
}

Class Rewrite {

	private static function IsOn() {
		global $config;

		return ($config['rewrite']) ? true : false;
	}

	public static function GetURL($url_data, $get_params = array('mode', 'do'), $check_rewrite = true, $amp = '&amp;') {

		$str = '';
		$is_arr = (is_array($url_data)) ? true : false;

		if ($check_rewrite and self::IsOn()) {

			if ($is_arr) {

				foreach ($url_data as $key => $value)

					$str .= $value.'/';
			} else $str .= 'go/'.$url_data.'/';
		} else {

			if ($is_arr) {

				$first = true;

				foreach ($get_params as $key => $value) {

					if (!$value)
						continue;
					if ($first) {
						$str .= '?';
						$first = false;
					} else $str .= $amp;
					$str .= $value.'='.$url_data[$key];
				}
			} else $str .= '?'.$get_params[0].'='.$url_data;
		}

		return BASE_URL . $str;
	}
}