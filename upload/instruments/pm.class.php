<?php

class PManager {
	public static function CheckNew() {
	global $db, $pm_count, $user;
		if(!$user) return 0 ;
		if(!isset($pm_count)) {
			$pm_count = $db->execute("SELECT COUNT(*) FROM `pm` WHERE `reciver` = '" . $user->name() . "' AND `viewed`=0");
			$pm_count = mysql_fetch_array($pm_count);
			$pm_count = $pm_count[0];
		}
		return $pm_count;
	}
	public static function SendNotify($user, $topic, $text) {
		global $db;
		return $db->execute("INSERT INTO `pm` (`date`, `sender`, `reciver`, `topic`, `text`) VALUES (NOW(), '" . $db->safe(sqlConfigGet('email-name')) . "', '" . $db->safe($user) . "', '" . $db->safe($topic) . "', '" . $db->safe($text) . "');");
	}
	public static function SendPM($from, $to, $topic, $text) {
		global $db;
		return $db->execute("INSERT INTO `pm` (`date`, `sender`, `reciver`, `topic`, `text`) VALUES (NOW(), '" . $db->safe($from) . "', '" . $db->safe($to) . "', '" . $db->safe($topic) . "', '" . $db->safe($text) . "');");
	}
} 