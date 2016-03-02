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
						`{$bd_names['forum_part']}`,
						`{$bd_names['forum_topics']}`,
						`{$bd_names['forum_mess']}`,
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

BD("CREATE TABLE IF NOT EXISTS `pm` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`date` DATETIME DEFAULT NULL,
	`sender` CHAR(32) DEFAULT NULL,
	`reciver` CHAR(32) DEFAULT NULL,
	`viewed` INT(11) NOT NULL DEFAULT '0',
	`topic` CHAR(255) DEFAULT NULL,
	`text` TEXT,
	`hide_for` BIGINT(20) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

BD("CREATE TABLE IF NOT EXISTS `reqests` (
	`id` INT(255) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(30) NOT NULL COLLATE 'utf8_unicode_ci',
	`realname` VARCHAR(30) NOT NULL COLLATE 'utf8_unicode_ci',
	`surname` VARCHAR(255) NOT NULL,
	`old` VARCHAR(20) NOT NULL COLLATE 'utf8_unicode_ci',
	`skype` VARCHAR(20) NOT NULL,
	`answer` VARCHAR(5) NOT NULL DEFAULT '1',
	`comment` VARCHAR(500) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

BD("CREATE TABLE IF NOT EXISTS `pages` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`author` BIGINT(20) NOT NULL,
	`url` VARCHAR(50) NOT NULL,
	`menu_item` VARCHAR(50) NOT NULL,
	`title` VARCHAR(50) NOT NULL,
	`title_inbody` VARCHAR(50) NOT NULL,
	`content` LONGTEXT NOT NULL,
	`created` DATETIME NOT NULL,
	`updated` DATETIME NULL DEFAULT NULL,
	`show_info` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `UNIQUE KEY` (`url`)
)  DEFAULT CHARSET=utf8 ENGINE=MyISAM;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['news_categorys']}` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` char(255) NOT NULL,
  `description` char(255) NOT NULL,
  `priority` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['groups']}` (
  `id`      int(10) NOT NULL AUTO_INCREMENT,
  `name`   char(64) NOT NULL,
  `pex_name`   char(64) NOT NULL,
  `lvl`     int(10) NOT NULL DEFAULT 1,
  `system` tinyint(1) NOT NULL DEFAULT 0,
  `change_skin` tinyint(1) NOT NULL DEFAULT 0,  
  `change_pass` tinyint(1) NOT NULL DEFAULT 0,
  `change_login` tinyint(1) NOT NULL DEFAULT 0,
  `change_cloak` tinyint(1) NOT NULL DEFAULT 0,
  `change_prefix` tinyint(1) NOT NULL DEFAULT 0,
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

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

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['forum_part']}` (
`id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT '0',
  `priority` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['forum_topics']}` (
  `id` int(11) NOT NULL,
  `partition_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` int(11) NOT NULL,
  `top` char(1) NOT NULL DEFAULT 'N',
  `closed` char(1) NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['forum_mess']}` (
  `id` int(11) NOT NULL,
  `partition_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` int(11) NOT NULL,
  `topmsg` char(1) NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

BD("CREATE TABLE IF NOT EXISTS `menu` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `align` smallint(1) NOT NULL DEFAULT '0',
  `txtid` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(50) NOT NULL DEFAULT '',
  `parent_id` varchar(50) NOT NULL DEFAULT '-1',
  `lvl` smallint(2) NOT NULL DEFAULT '-1',
  `permission` varchar(50) NOT NULL DEFAULT '-1',
  `active` smallint(1) NOT NULL DEFAULT '0',
  `inner_html` varchar(50) NOT NULL DEFAULT '',
  `system` smallint(1) NOT NULL DEFAULT '0',
  `priority` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Индекс 2` (`txtid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

/* Игорь косорукая тварь */
BD("ALTER TABLE `{$bd_names['forum_part']}` ADD PRIMARY KEY (`id`);");
BD("ALTER TABLE `{$bd_names['forum_topics']}` ADD PRIMARY KEY (`id`);");
BD("ALTER TABLE `{$bd_names['forum_mess']}` ADD PRIMARY KEY (`id`);");
BD("ALTER TABLE `{$bd_names['forum_part']}` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1");
BD("ALTER TABLE `{$bd_names['forum_topics']}` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1");
BD("ALTER TABLE `{$bd_names['forum_mess']}` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1");

BD("CREATE TABLE IF NOT EXISTS `warnings` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `uid` BIGINT(20) NOT NULL,
  `mid` BIGINT(20) NOT NULL,
  `percentage` INT(11) NOT NULL,
  `reason` TEXT NOT NULL,
  `time` DATETIME NOT NULL,
  `expires` DATE NOT NULL,
  `type` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8;");

/* DEFAULT INFO ADD */
BD("INSERT INTO `{$bd_names['news_categorys']}` (`id`,`name`) VALUES (1,'Без категории');");
if (!file_exists(MCR_ROOT . "instruments/menu_items.php"))
	(MCR_LANG == "ru_RU") ?
		BD("INSERT INTO `menu` (`align`, `txtid`, `name`, `url`, `parent_id`, `lvl`, `permission`, `active`, `inner_html`, `system`, `priority`) VALUES
	(0, 'main', '<i class=\"glyphicon glyphicon-home\"></i> Главная', '/', '-1', -1, '-1', 0, '', 1, 0),
	(0, 'info', '<i class=\"glyphicon glyphicon-info-sign\"></i> Информация', '/go/guide/', '-1', -1, '-1', 0, '', 1, -1),
	(0, 'guide', '<i class=\"fa fa-question-circle\"></i> Гайд', '/go/guide/', 'info', -1, '-1', 0, '', 1, -2),
	(0, 'rules', '<i class=\"fa fa-book\"></i> Правила', '/go/rules/', 'info', -1, '-1', 0, '', 1, -3),
	(0, 'accs', '<i class=\"fa fa-users\"></i> Игроки', '/go/users/', '-1', -1, '-1', 0, '', 1, -4),
	(0, 'users', '<i class=\"fa fa-user\"></i> Все', '/go/users/', 'accs', -1, '-1', 0, '', 1, -5),
	(0, 'banlist', '<i class=\"fa fa-user-times\"></i> Банлист', '/go/banlist/', 'accs', -1, '-1', 0, '', 1, -6),
	(0, 'forum', '<i class=\"glyphicon glyphicon-comment\"></i> Форум', '/go/forum/', '-1', -1, '-1', 0, '', 1, -7),
	(1, 'pm', '<i class=\"glyphicon glyphicon-envelope\"></i>{PM_CHECKNEW}', '/go/pm/', '-1', 1, '-1', 0, '', 1, -8),
	(1, 'pm_new', '<i class=\"fa fa-pencil-square-o\"></i> Написать ЛС', '/go/pm/write/', 'pm', 1, '-1', 0, '', 1, -9),
	(1, 'pm_inbox', '<i class=\"fa fa-inbox\"></i> Входящие', '/go/pm/inbox/', 'pm', 1, '-1', 0, '', 1, -10),
	(1, 'pm_outbox', '<i class=\"fa fa-paper-plane\"></i> Отправленные', '/go/pm/outbox/', 'pm', 1, '-1', 0, '', 1, -11),
	(1, 'preferences', '<i class=\"glyphicon glyphicon-cog\"></i> Настройки', '/go/options/', '-1', 1, '-1', 0, '', 1, -12),
	(1, 'options', '<i class=\"glyphicon glyphicon-user\"></i> Настройки аккаунта', '/go/options/', 'preferences', 1, '-1', 0, '', 1, -13),
	(1, 'admin', '<i class=\"glyphicon glyphicon-wrench\"></i> Админка', '/go/admin', 'preferences', 15, '-1', 0, '', 1, -14),
	(1, 'admin_news', '<i class=\"fa fa-newspaper-o\"></i>Управление новостями', '/', 'admin', -1, '-1', 0, '', 0, -15),
	(1, 'add_news', '<i class=\"glyphicon glyphicon-plus\"></i> Добавить новость', '/go/news_add/', 'admin_news', 1, 'add_news', 0, '', 1, -16),
	(1, 'category_news', '<i class=\"fa fa-files-o\"></i> Категории новостей', '/control/category/', 'admin_news', 15, '-1', 0, '', 1, -17),
	(1, 'admin_pages', '<i class=\"fa fa-file-o\"></i> Управление страницами', '/control/pages/', 'admin', 1, '-1', 0, '', 1, -18),
	(1, 'add_page', '<i class=\"glyphicon glyphicon-plus\"></i> Добавить страницу', '/control/page_add/', 'admin_pages', 1, '-1', 0, '', 1, -19),
	(1, 'pages', '<i class=\"fa fa-file-text-o\"></i> Редактировать страницы', '/control/pages/', 'admin_pages', 1, '-1', 0, '', 1, -20),
	(1, 'admin_menu', '<i class=\"fa fa-compass\"></i> Управление меню', '/control/menu/', 'admin', 1, '-1', 0, '', 1, -21),
	(1, 'menu_add', '<i class=\"glyphicon glyphicon-plus\"></i> Добавить пункт меню', '/control/menu_add/', 'admin_menu', 1, '-1', 0, '', 1, -22),
	(1, 'menu', '<i class=\"fa fa-bars\"></i> Редактировать меню', '/control/menu/', 'admin_menu', 1, '-1', 0, '', 1, -23),
	(1, 'admin_users', '<i class=\"fa fa-users\"></i> Управление пользователями', '/control/user/', 'admin', 15, '-1', 0, '', 1, -24),
	(1, 'control', '<i class=\"fa fa-user\"></i> Аккаунты', '/control/user/', 'admin_users', 15, '-1', 0, '', 1, -25),
	(1, 'reqests', '<i class=\"fa fa-user-times\"></i> Заявки в модераторы', '/go/reqests/', 'admin_users', 1, '-1', 0, '', 1, -26),
	(1, 'reg_edit', '<i class=\"fa fa-user-plus\"></i> Регистрация', '/control/ipbans/', 'admin_users', 15, '-1', 0, '', 1, -27),
	(1, 'group_edit', '<i class=\"fa fa-user-secret\"></i> Группы пользователей', '/control/group/', 'admin_users', 15, '-1', 0, '', 1, -28),
	(1, 'admin_game', '<i class=\"fa fa-gamepad\"></i> Управление игрой', '/control/forum/', 'admin', 15, '-1', 0, '', 1, -29),
	(1, 'donate_edit', '<i class=\"fa fa-money\"></i> Настройки доната', '/control/donate/', 'admin_game', 15, '-1', 0, '', 1, -30),
	(1, 'rcon', '<i class=\"fa fa-terminal\"></i> RCON', '/control/rcon/', 'admin_game', 15, '-1', 0, '', 1, -31),
	(1, 'game_edit', '<i class=\"fa fa-play\"></i> Настройки лончера', '/control/update/', 'admin_game', 15, '-1', 0, '', 1, -32),
	(1, 'serv_edit', '<i class=\"fa fa-server\"></i> Мониторинг серверов', '/control/servers/', 'admin_game', 15, '-1', 0, '', 1, -33),
	(1, 'file_edit', '<i class=\"fa fa-file-code-o\"></i> Файлы', '/control/filelist/', 'admin', 15, '-1', 0, '', 1, -34),
	(1, 'site_edit', '<i class=\"fa fa-bomb\"></i> Настройки сайта', '/control/constants/', 'admin', 15, '-1', 0, '', 1, -35),
	(1, 'forum_edit', '<i class=\"fa fa-comments-o\"></i> Настройки форума', '/control/forum/', 'admin', 15, '-1', 0, '', 1, -36),
	(1, 'exit', '<i class=\"glyphicon glyphicon-log-out\"></i> Выход', '/login.php?out=1', '-1', 1, '-1', 0, '', 1, -37);") :
		BD("INSERT INTO `menu` (`align`, `txtid`, `name`, `url`, `parent_id`, `lvl`, `permission`, `active`, `inner_html`, `system`, `priority`) VALUES
	(0, 'main', '<i class=\"glyphicon glyphicon-home\"></i> Home', '/', '-1', -1, '-1', 0, '', 1, 0),
	(0, 'info', '<i class=\"glyphicon glyphicon-info-sign\"></i> Information', '/go/guide/', '-1', -1, '-1', 0, '', 1, -1),
	(0, 'guide', '<i class=\"fa fa-question-circle\"></i> Guide', '/go/guide/', 'info', -1, '-1', 0, '', 1, -2),
	(0, 'rules', '<i class=\"fa fa-book\"></i> Rules', '/go/rules/', 'info', -1, '-1', 0, '', 1, -3),
	(0, 'accs', '<i class=\"fa fa-users\"></i> Players', '/go/users/', '-1', -1, '-1', 0, '', 1, -4),
	(0, 'users', '<i class=\"fa fa-user\"></i> All', '/go/users/', 'accs', -1, '-1', 0, '', 1, -5),
	(0, 'banlist', '<i class=\"fa fa-user-times\"></i> Blacklist', '/go/banlist/', 'accs', -1, '-1', 0, '', 1, -6),
	(0, 'forum', '<i class=\"glyphicon glyphicon-comment\"></i> Forum', '/go/forum/', '-1', -1, '-1', 0, '', 1, -7),
	(1, 'pm', '<i class=\"glyphicon glyphicon-envelope\"></i>{PM_CHECKNEW}', '/go/pm/', '-1', 1, '-1', 0, '', 1, -8),
	(1, 'pm_new', '<i class=\"fa fa-pencil-square-o\"></i> Write PM', '/go/pm/write/', 'pm', 1, '-1', 0, '', 1, -9),
	(1, 'pm_inbox', '<i class=\"fa fa-inbox\"></i> Inbox', '/go/pm/inbox/', 'pm', 1, '-1', 0, '', 1, -10),
	(1, 'pm_outbox', '<i class=\"fa fa-paper-plane\"></i> Outbox', '/go/pm/outbox/', 'pm', 1, '-1', 0, '', 1, -11),
	(1, 'preferences', '<i class=\"glyphicon glyphicon-cog\"></i> Preferences', '/go/options/', '-1', 1, '-1', 0, '', 1, -12),
	(1, 'options', '<i class=\"glyphicon glyphicon-user\"></i> Account settings', '/go/options/', 'preferences', 1, '-1', 0, '', 1, -13),
	(1, 'admin', '<i class=\"glyphicon glyphicon-wrench\"></i> Admin', '/go/admin', 'preferences', 15, '-1', 0, '', 1, -14),
	(1, 'admin_news', '<i class=\"fa fa-newspaper-o\"></i>Manage News', '/', 'admin', -1, '-1', 0, '', 0, -15),
	(1, 'add_news', '<i class=\"glyphicon glyphicon-plus\"></i> Add news', '/go/news_add/', 'admin_news', 1, 'add_news', 0, '', 1, -16),
	(1, 'category_news', '<i class=\"fa fa-files-o\"></i> News categories', '/control/category/', 'admin_news', 15, '-1', 0, '', 1, -17),
	(1, 'admin_pages', '<i class=\"fa fa-file-o\"></i> Manage pages', '/control/pages/', 'admin', 1, '-1', 0, '', 1, -18),
	(1, 'add_page', '<i class=\"glyphicon glyphicon-plus\"></i> Add page', '/control/page_add/', 'admin_pages', 1, '-1', 0, '', 1, -19),
	(1, 'pages', '<i class=\"fa fa-file-text-o\"></i> Edit pages', '/control/pages/', 'admin_pages', 1, '-1', 0, '', 1, -20),
	(1, 'admin_menu', '<i class=\"fa fa-compass\"></i> Manage menu', '/control/menu/', 'admin', 1, '-1', 0, '', 1, -21),
	(1, 'menu_add', '<i class=\"glyphicon glyphicon-plus\"></i> Add menu item', '/control/menu_add/', 'admin_menu', 1, '-1', 0, '', 1, -22),
	(1, 'menu', '<i class=\"fa fa-bars\"></i> Edit menu', '/control/menu/', 'admin_menu', 1, '-1', 0, '', 1, -23),
	(1, 'admin_users', '<i class=\"fa fa-users\"></i> Manage users', '/control/user/', 'admin', 15, '-1', 0, '', 1, -24),
	(1, 'control', '<i class=\"fa fa-user\"></i> Accounts', '/control/user/', 'admin_users', 15, '-1', 0, '', 1, -25),
	(1, 'reqests', '<i class=\"fa fa-user-times\"></i> Moderators requests', '/go/reqests/', 'admin_users', 1, '-1', 0, '', 1, -26),
	(1, 'reg_edit', '<i class=\"fa fa-user-plus\"></i> Registration', '/control/ipbans/', 'admin_users', 15, '-1', 0, '', 1, -27),
	(1, 'group_edit', '<i class=\"fa fa-user-secret\"></i> User groups', '/control/group/', 'admin_users', 15, '-1', 0, '', 1, -28),
	(1, 'admin_game', '<i class=\"fa fa-gamepad\"></i> Game setup', '/control/forum/', 'admin', 15, '-1', 0, '', 1, -29),
	(1, 'donate_edit', '<i class=\"fa fa-money\"></i> Donate', '/control/donate/', 'admin_game', 15, '-1', 0, '', 1, -30),
	(1, 'rcon', '<i class=\"fa fa-terminal\"></i> RCON', '/control/rcon/', 'admin_game', 15, '-1', 0, '', 1, -31),
	(1, 'game_edit', '<i class=\"fa fa-play\"></i> Launcher settings', '/control/update/', 'admin_game', 15, '-1', 0, '', 1, -32),
	(1, 'serv_edit', '<i class=\"fa fa-server\"></i> Monitoring', '/control/servers/', 'admin_game', 15, '-1', 0, '', 1, -33),
	(1, 'file_edit', '<i class=\"fa fa-file-code-o\"></i> Files', '/control/filelist/', 'admin', 15, '-1', 0, '', 1, -34),
	(1, 'site_edit', '<i class=\"fa fa-bomb\"></i> Site constants', '/control/constants/', 'admin', 15, '-1', 0, '', 1, -35),
	(1, 'forum_edit', '<i class=\"fa fa-comments-o\"></i> Forum management', '/control/forum/', 'admin', 15, '-1', 0, '', 1, -36),
	(1, 'exit', '<i class=\"glyphicon glyphicon-log-out\"></i> Exit', '/login.php?out=1', '-1', 1, '-1', 0, '', 1, -37);");

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

	BD("ALTER TABLE `{$bd_names['comments']}` ADD `item_type` smallint(3) DEFAULT " . ItemType::News . ";");
	BD("ALTER TABLE `{$bd_names['comments']}` DROP KEY `item_id`");
	BD("ALTER TABLE `{$bd_names['comments']}` ADD KEY `uniq_item` (`item_id`, `item_type`);");

	BD("ALTER TABLE `{$bd_names['news']}` CHANGE COLUMN `hide_vote` `vote` tinyint(1) NOT NULL DEFAULT 1;");
	BD("ALTER TABLE `{$bd_names['news']}` ADD `discus` tinyint(1) NOT NULL DEFAULT 1;");
	BD("ALTER TABLE `{$bd_names['news']}` ADD `comments` int(10) NOT NULL DEFAULT 0;");
}

/* webMCRex 1.235a UPDATE */
if (!BD_ColumnExist($bd_names['iconomy'], $bd_money['bank'])) {

	BD("ALTER TABLE `{$bd_names['iconomy']}` ADD `{$bd_money['bank']}` double(64,2) NOT NULL DEFAULT '0.00';");
}

/* webMCRex 1.235b_r2 UPDATE */
if (!BD_ColumnExist($bd_names['users'], 'vote')) {

	BD("ALTER TABLE `{$bd_names['users']}` ADD `vote` smallint(10) DEFAULT 0;");
}

/* webMCRex 2.0b2 UPDATE */
if (!BD_ColumnExist($bd_names['groups'], 'pex_name')) {

	BD("ALTER TABLE `{$bd_names['groups']}` ADD `pex_name` char(64) NOT NULL;");
}

/* webMCRex 2.0b4 UPDATE */
if (!BD_ColumnExist($bd_names['users'], 'topics')) {
	BD("ALTER TABLE `{$bd_names['users']}` ADD `topics` smallint(10) DEFAULT 0;");
	BD("ALTER TABLE `{$bd_names['users']}` ADD `posts` smallint(10) DEFAULT 0;");
}

/* webMCRex 2.0b40 UPDATE */
if (!BD_ColumnExist($bd_names['groups'], 'change_prefix')) {

	BD("ALTER TABLE `{$bd_names['groups']}` ADD `change_prefix` SMALLINT(1) NOT NULL DEFAULT 0;");
}

/* webMCRex 2.1b4 UPDATE, fixed in 2.0b5 */
if (!BD_ColumnExist($bd_names['users'], 'wocid')) {
	BD("ALTER TABLE `{$bd_names['users']}` ADD `wocid` bigint(20) NOT NULL DEFAULT 0;");
	BD("ALTER TABLE `{$bd_names['users']}` ADD `woctoken` char(32) NOT NULL DEFAULT '';");
}

/* webMCRex 2.5b2 UPDATE */
if (!BD_ColumnExist('pm', 'hide_for')) {
	BD("ALTER TABLE `pm` ADD `hide_for` BIGINT(20) NOT NULL DEFAULT 0;");
}

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['action_log']}` (
  `IP` varchar(16) NOT NULL,
  `first_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `query_count` int(10) NOT NULL DEFAULT 1,
  `info` varchar(255) NOT NULL,
  PRIMARY KEY (`IP`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['iconomy']}` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`{$bd_money['login']}` varchar(20) CHARACTER SET utf8 NOT NULL,
`{$bd_money['bank']}` double(64,2) NOT NULL DEFAULT '0.00',
`{$bd_money['money']}` double(64,2) NOT NULL DEFAULT '0.00',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `type` TINYINT(1) NOT NULL,
  `permission` VARCHAR(200) NOT NULL,
  `world` VARCHAR(50) DEFAULT NULL,
  `value` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `permissions_entity` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `type` TINYINT(1) NOT NULL,
  `prefix` VARCHAR(255) NOT NULL,
  `suffix` VARCHAR(255) NOT NULL,
  `default` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `default` (`default`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `permissions_inheritance` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `child` VARCHAR(50) NOT NULL,
  `parent` VARCHAR(50) NOT NULL,
  `type` TINYINT(1) NOT NULL,
  `world` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `child` (`child`,`parent`,`type`,`world`),
  KEY `child_2` (`child`,`type`),
  KEY `parent` (`parent`,`type`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE `unbans` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) DEFAULT NULL,
  `numofban` VARCHAR(3) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `banlist` (
  `name` VARCHAR(32) NOT NULL,
  `reason` TEXT NOT NULL,
  `admin` VARCHAR(32) NOT NULL,
  `time` BIGINT(20) NOT NULL,
  `temptime` BIGINT(20) NOT NULL,
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `type` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");

BD("CREATE TABLE IF NOT EXISTS `banlistip` (
`name` VARCHAR(32) NOT NULL,
`lastip` TINYTEXT NOT NULL,
PRIMARY KEY (`name`)
) ENGINE=MyISAM CHARSET=utf8 AUTO_INCREMENT=1;");