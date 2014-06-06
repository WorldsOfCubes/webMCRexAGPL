<?php 
if ($mysql_rewrite) 

BD("ALTER TABLE `{$bd_names['users']}` 
DROP   `{$bd_users['female']}`,
DROP   `{$bd_users['email']}`,
DROP   `{$bd_users['tmp']}`,
DROP   `{$bd_users['group']}`,
DROP   `{$bd_users['deadtry']}`,
DROP   `comments_num`,
DROP   `gameplay_last`,
DROP   `{$bd_users['ctime']}`,
DROP   `active_last`,
DROP   `play_times`,
DROP   `undress_times`,
DROP   `default_skin`,
DROP   `{$bd_users['session']}`,
DROP   `{$bd_users['clientToken']}`,    
DROP   `{$bd_users['server']}`;");	

BD($bd_alter_users."ADD `{$bd_users['deadtry']}` tinyint(1) DEFAULT 0;");
BD($bd_alter_users."ADD `{$bd_users['female']}` tinyint(1) NOT NULL DEFAULT '2';");
BD($bd_alter_users."ADD `{$bd_users['email']}` varchar(50) DEFAULT NULL;");
BD($bd_alter_users."ADD `{$bd_users['tmp']}` char(32) NOT NULL DEFAULT '0';");
BD($bd_alter_users."ADD `{$bd_users['group']}` int(10) NOT NULL DEFAULT 1;");
BD($bd_alter_users."ADD `comments_num` int(10) NOT NULL DEFAULT 0;");
BD($bd_alter_users."ADD `gameplay_last` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';");
BD($bd_alter_users."ADD `{$bd_users['ctime']}` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';");
BD($bd_alter_users."ADD `active_last` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';");
BD($bd_alter_users."ADD `play_times` int(10) NOT NULL DEFAULT 0;");
BD($bd_alter_users."ADD `undress_times` int(10) NOT NULL DEFAULT 0;");
BD($bd_alter_users."ADD `default_skin` tinyint(1) NOT NULL DEFAULT '1';");
BD($bd_alter_users."ADD `{$bd_users['session']}` varchar(255) DEFAULT NULL;");
BD($bd_alter_users."ADD `{$bd_users['clientToken']}` varchar(255) DEFAULT NULL;");
BD($bd_alter_users."ADD `{$bd_users['server']}` varchar(255) DEFAULT NULL;");
BD($bd_alter_users."ADD `vote` int(10) NOT NULL DEFAULT 0;");
