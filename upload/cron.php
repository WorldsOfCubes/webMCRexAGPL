<?php
require('system.php');
loadTool('user.class.php');

$db = new DB();
$db->connect('cron');

$query = $db->execute("SELECT * FROM `permissions` WHERE `permission` = 'group-vip-until' OR `permission` = 'group-premium-until';");
$current_time = time();
$i = 0;
while ($result = $db->fetch_assoc($query)) {
	if ($result['value'] < $current_time) {
		$db->execute("DELETE FROM `permissions_inheritance` WHERE child='{$result['name']}';");
		$db->execute("DELETE FROM `permissions_entity` WHERE `name`='{$result['name']}';");
		$tmp_user = new User($result['name'], $bd_users['login']);
		$tmp_user->changeGroup(1);
		$i++;
	}
}
echo("Снято $i статусов");