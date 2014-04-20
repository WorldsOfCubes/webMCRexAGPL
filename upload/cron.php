<?php
require ('system.php');
loadTool('user.class.php');

BDConnect('cron');

$query = BD("SELECT * FROM `permissions` WHERE `permission` = 'group-vip-until' OR `permission` = 'group-premium-until';");
$current_time = time();
$i = 0;
while($result = mysql_fetch_assoc($query))
{
    if($result['value'] < $current_time) {
        BD("DELETE FROM `permissions_inheritance` WHERE child='{$result['name']}';");
        BD("DELETE FROM `permissions_entity` WHERE `name`='{$result['name']}';");
        BD("DELETE FROM `permissions` WHERE `name`='{$result['name']}';");
        $tmp_user = new User($result['name'], $bd_users['login']);
        $tmp_user->changeGroup(1);
        $i++;
    }
}
echo("Снято $i статусов");