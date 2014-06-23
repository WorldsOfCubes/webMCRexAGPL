<?php 
/* SQL COMMON TABLES CREATE + ADD DEFAULT INFO + UPDATES */

BD("SET FOREIGN_KEY_CHECKS=0;");

if ($mysql_rewrite) {

BD("DROP TABLE IF EXISTS `{$bd_names['ip_banning']}`,
                        `{$bd_names['news']}`,
                        `{$bd_names['news_categorys']}`,
                        `{$bd_names['likes']}`,
                        `{$bd_names['data']}`,
                        `{$bd_names['comments']}`,
                        `{$bd_names['files']}`,
                        `{$bd_names['servers']}`,
                        `{$bd_names['iconomy']}`,
                        `{$bd_names['groups']}`,
                        `banlist`,
                        `banlistip`,
                        `permissions`,
                        `permissions_entity`,
                        `permissions_inheritance`,
                        `unbans`;");
}

/* CREATE TABLES */
	
BD("CREATE TABLE IF NOT EXISTS `{$bd_names['likes']}` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `item_type` smallint(3) NOT NULL DEFAULT 1,
  `var` tinyint(1) NOT NULL DEFAULT -1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['files']}` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_word` char(255) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `way` char(255) DEFAULT NULL,
  `name` char(255) DEFAULT NULL,
  `dislikes` int(10) DEFAULT 0,
  `likes` int(10) DEFAULT 0,
  `downloads` int(10) DEFAULT 0,
  `size` char(32) DEFAULT 0,
  `hash` char(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['news']}` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) NOT NULL DEFAULT 1,
  `user_id` bigint(20) NOT NULL,
  `dislikes` int(10) DEFAULT 0,
  `likes` int(10) DEFAULT 0,
  `hide_vote` tinyint(1) NOT NULL DEFAULT 0,
  `hits` int(10) DEFAULT 0,
  `title` char(255) NOT NULL,
  `message` TEXT NOT NULL,
  `message_full` MEDIUMTEXT NOT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['news_categorys']}` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` char(255) NOT NULL,
  `description` char(255) NOT NULL,
  `priority` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['servers']}` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `online` tinyint(1) DEFAULT 0,
  `rcon` varchar(255) DEFAULT '',
  `service_user` char(64) default NULL,
  `players` text default NULL,
  `method` tinyint(1) DEFAULT 0,
  `address` varchar(255) default NULL,
  `port` int(10) DEFAULT 25565,
  `name` varchar(255) default NULL,
  `info` char(255) default NULL,
  `numpl` char(32) default NULL,
  `slots` char(32) default NULL,
  `main_page` tinyint(1) DEFAULT 0,
  `news_page` tinyint(1) DEFAULT 0,
  `stat_page` tinyint(1) DEFAULT 0,
  `priority` tinyint(1) DEFAULT 0,
  `main` tinyint(1) DEFAULT 0,
  `refresh_time` smallint(3) NOT NULL DEFAULT '5',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['groups']}` (
  `id`      int(10) NOT NULL AUTO_INCREMENT,
  `name`   char(64) NOT NULL,
  `lvl`     int(10) NOT NULL DEFAULT 1,
  `system` tinyint(1) NOT NULL DEFAULT 0,
  `change_skin` tinyint(1) NOT NULL DEFAULT 0,  
  `change_pass` tinyint(1) NOT NULL DEFAULT 0,
  `change_login` tinyint(1) NOT NULL DEFAULT 0,
  `change_cloak` tinyint(1) NOT NULL DEFAULT 0,
  `add_news` tinyint(1) NOT NULL DEFAULT 0,
  `add_comm` tinyint(1) NOT NULL DEFAULT 0,
  `adm_comm` tinyint(1) NOT NULL DEFAULT 0,
  `max_fsize` int(10) NOT NULL DEFAULT 20,  
  `max_ratio` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['comments']}` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `message` varchar(255) NOT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['ip_banning']}` (
  `IP` varchar(16) NOT NULL,
  `time_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ban_until` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ban_type` tinyint(1) NOT NULL DEFAULT 1,
  `reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IP`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['data']}` (
  `property` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  UNIQUE KEY `property` (`property`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

/* DEFAULT INFO ADD */

BD("INSERT INTO `{$bd_names['news_categorys']}` (`id`,`name`) VALUES (1,'Без категории');");

BD("INSERT INTO `{$bd_names['data']}` (`property`, `value`) VALUES
('launcher-version', '1'),
('next-reg-time', '2'),
('email-verification', '0'),
('rcon-port', '0'),
('rcon-pass', '0'),
('rcon-serv', '0');");

BD("INSERT INTO `{$bd_names['data']}` (`property`, `value`) VALUES
('protection-key', 'lalka');");

BD("INSERT INTO `{$bd_names['data']}` (`property`, `value`) VALUES
('smtp-user', ''),
('smtp-pass', ''),
('smtp-host', 'localhost'),
('smtp-port', '25'),
('smtp-hello', 'HELO'),
('game-link-win', ''),
('game-link-osx', ''),
('game-link-lin', '');");

BD("INSERT INTO `{$bd_names['data']}` (`property`, `value`) VALUES
('email-name', 'Info'),
('email-mail', 'noreply@noreply.ru');");

/* webMCR 2.05 UPDATE */

if (!BD_ColumnExist($bd_names['ip_banning'], 'ban_type'))

BD("ALTER TABLE `{$bd_names['ip_banning']}` ADD `ban_type` tinyint(1) NOT NULL DEFAULT 1;");

if (!BD_ColumnExist($bd_names['ip_banning'], 'reason'))	

BD("ALTER TABLE `{$bd_names['ip_banning']}`  ADD `reason` varchar(255) DEFAULT NULL;");
	
/* webMCR 2.1 UPDATE */

if (!BD_ColumnExist($bd_names['news'], 'user_id')) {

BD("ALTER TABLE `{$bd_names['news']}` 
	ADD `user_id` bigint(20) NOT NULL,
	ADD `dislikes` int(10) DEFAULT 0,
	ADD `likes` int(10) DEFAULT 0;");
	
BD("ALTER TABLE `{$bd_names['news']}`	ADD KEY `category_id` (`category_id`),
										ADD KEY `user_id` (`user_id`);");
					
BD("ALTER TABLE `{$bd_names['comments']}`	ADD KEY `user_id` (`user_id`),
											ADD	KEY `item_id` (`item_id`);");

BD("ALTER TABLE `{$bd_names['users']}`	ADD	KEY `group_id` (`{$bd_users['group']}`);");	
}	

/* webMCR 2.15 UPDATE */
if (!BD_ColumnExist($bd_names['users'], $bd_users['deadtry'])) {

BD("ALTER TABLE `{$bd_names['users']}`	ADD `{$bd_users['deadtry']}` tinyint(1) DEFAULT 0;");	
}

/* webMCR 2.25b UPDATE */
if (!BD_ColumnExist($bd_names['users'], $bd_users['clientToken'])) {

BD("ALTER TABLE `{$bd_names['users']}` ADD `{$bd_users['clientToken']}` varchar(255) DEFAULT NULL;");	
}

/* webMCR 2.3 UPDATE */
if (!BD_ColumnExist($bd_names['servers'], 'service_user')) {

BD("ALTER TABLE `{$bd_names['servers']}` ADD `service_user` char(64) default NULL;");
BD("ALTER TABLE `{$bd_names['news']}` ADD `hits` int(10) DEFAULT 0;");	
}

if (!BD_ColumnExist($bd_names['news'], 'hide_vote'))

BD("ALTER TABLE `{$bd_names['news']}` ADD `hide_vote` tinyint(1) NOT NULL DEFAULT 0;");	

/* webMCR 2.31 UPDATE */
if (!BD_ColumnExist($bd_names['comments'], 'item_type')) {

BD("ALTER TABLE `{$bd_names['comments']}` ADD `item_type` smallint(3) DEFAULT ". ItemType::News .";");
BD("ALTER TABLE `{$bd_names['comments']}` DROP KEY `item_id`");
BD("ALTER TABLE `{$bd_names['comments']}` ADD KEY `uniq_item` (`item_id`, `item_type`);");

BD("ALTER TABLE `{$bd_names['news']}` CHANGE COLUMN `hide_vote` `vote` tinyint(1) NOT NULL DEFAULT 1;");
BD("ALTER TABLE `{$bd_names['news']}` ADD `discus` tinyint(1) NOT NULL DEFAULT 1;");
BD("ALTER TABLE `{$bd_names['news']}` ADD `comments` int(10) NOT NULL DEFAULT 0;");
}

/* webMCRex 1.235b_r2 UPDATE */
if (!BD_ColumnExist($bd_names['comments'], 'vote')) {

BD("ALTER TABLE `{$bd_names['comments']}` ADD `vote` smallint(10) DEFAULT 0;");
}

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['action_log']}` (
  `IP` varchar(16) NOT NULL,
  `first_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `query_count` int(10) NOT NULL DEFAULT 1,
  `info` varchar(255) NOT NULL,
  PRIMARY KEY (`IP`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

BD("INSERT INTO `{$bd_names['groups']}` 
(`id`,`name`,`lvl`,`system`,`change_skin`,`change_pass`,`change_login`,`change_cloak`,`add_news`,`add_comm`,`adm_comm`) VALUES 
(1,'Пользователь',2,1,1,1,0,0,0,1,0), 
(2,'Заблокированный',0,1,0,0,0,0,0,0,0), 
(3,'Администратор',15,1,1,1,1,1,1,1,1), 
(4,'Непроверенный',1,1,0,0,0,0,0,0,0), 
(5,'VIP',5,1,1,1,0,1,0,1,0),
(6,'Premium',6,1,1,1,0,1,0,1,0);");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['iconomy']}` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`{$bd_money['login']}` varchar(20) CHARACTER SET utf8 NOT NULL,
`{$bd_money['bank']}` double(64,2) NOT NULL DEFAULT '0.00',
`{$bd_money['money']}` double(64,2) NOT NULL DEFAULT '0.00',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `permission` varchar(200) NOT NULL,
  `world` varchar(50) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `permissions_entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `prefix` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `default` (`default`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `permissions_inheritance` (
  `id` int(11) NOT NULL auto_increment,
  `child` varchar(50) NOT NULL,
  `parent` varchar(50) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `world` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `child` (`child`,`parent`,`type`,`world`),
  KEY `child_2` (`child`,`type`),
  KEY `parent` (`parent`,`type`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE `unbans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `numofban` varchar(3) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `banlist` (
`name` varchar(32) NOT NULL,
`reason` text NOT NULL,
`admin` varchar(32) NOT NULL,
`time` bigint(20) NOT NULL,
`temptime` bigint(20) NOT NULL,
`id` int(11) NOT NULL AUTO_INCREMENT,
`type` int(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `banlistip` (
`name` varchar(32) NOT NULL,
`lastip` tinytext NOT NULL,
PRIMARY KEY (`name`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");