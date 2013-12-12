<?php
include ('system.php');
$query = BD("SELECT * FROM `{$bd_names['users']}` WHERE `{$bd_users['group']}` = {$vipG} OR `{$bd_users['group']}` = {$premG};",$sql) or die(mysql_error());
$current_time = time();
while($result = mysql_fetch_assoc($query))
{
    if($result['duration'] < $current_time) {
        mysql_query("DELETE FROM `permissions_inheritance` WHERE child='{$result['name']}';",$sql);
        mysql_query("DELETE FROM `permissions_entity` WHERE `name`='{$result['name']}';",$sql);
        mysql_query("UPDATE `{$bd_names['users']}` SET  `{$bd_users['group']}`='1' WHERE `name`='{$result[$bd_users['login']]}';",$sql);
        $path = $cloak_path.$result['name'].'.png';
        if(file_exists($path)) unlink($path);
    }
}