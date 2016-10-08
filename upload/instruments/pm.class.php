<?php

class PManager {
	public static function CheckNew() {
		global $db, $pm_count, $user;
		if (!$user)
			return 0;
		if (!isset($pm_count)) {
			$pm_count = $db->execute("SELECT COUNT(*) FROM `pm` WHERE `reciver` = '".$user->name()."' AND `viewed`=0");
			$pm_count = $db->fetch_array($pm_count);
			$pm_count = $pm_count[0];
		}
		return $pm_count;
	}

	public static function SendNotify($user, $topic, $text) {
		global $db;
		return $db->execute("INSERT INTO `pm` (`date`, `sender`, `reciver`, `topic`, `text`) VALUES (NOW(), '".$db->safe(sqlConfigGet('email-name'))."', '".$db->safe($user)."', '".$db->safe($topic)."', '".$db->safe($text)."');");
	}

	public static function SendPM($from, $to, $topic, $text) {
		global $db;
		return $db->execute("INSERT INTO `pm` (`date`, `sender`, `reciver`, `topic`, `text`) VALUES (NOW(), '".$db->safe($from)."', '".$db->safe($to)."', '".$db->safe($topic)."', '".$db->safe($text)."');");
	}
}
class PrivateMessage extends Item {
	public function __construct($id = false, $style_sd = false) {
		global $db, $bd_names;

		parent::__construct($id, ItemType::PM, 'pm', $style_sd);

		if (!$this->id)
			return false;

		$result = $db->execute("SELECT `user_id`, `item_id`, `item_type` FROM `{$this->db}` WHERE `id`='".$this->id."'");

		if ($db->num_rows($result) != 1) {
			$this->id = false;
			return false;
		}

		$line = $db->fetch_array($result, MYSQL_NUM);

		$this->user_id = (int)$line[0];
		$this->parent_id = (int)$line[1];
		$this->parent_type = (int)$line[2];
		$this->parent_obj = false;
	}
}