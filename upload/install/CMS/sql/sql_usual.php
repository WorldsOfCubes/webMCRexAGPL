<?php 
if ($mysql_rewrite) 
BD("DROP TABLE IF EXISTS `{$bd_names['users']}`;");

BD("CREATE TABLE IF NOT EXISTS `{$bd_names['users']}` (
  `{$bd_users['id']}` bigint(20) NOT NULL AUTO_INCREMENT,
  `{$bd_users['login']}` char(32) DEFAULT NULL,
  `{$bd_users['female']}` tinyint(1) NOT NULL DEFAULT '2',
  `{$bd_users['deadtry']}` tinyint(1) DEFAULT 0,
  `{$bd_users['email']}` varchar(50) default NULL,
  `{$bd_users['password']}` char(32) DEFAULT NULL,
  `{$bd_users['tmp']}` char(32) NOT NULL DEFAULT '0',
  `{$bd_users['ip']}` varchar(16) DEFAULT NULL,
  `{$bd_users['group']}` int(10) NOT NULL DEFAULT 1,
  `comments_num` int(10) NOT NULL DEFAULT 0,
  `gameplay_last` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `{$bd_users['ctime']}` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active_last` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `play_times` int(10) NOT NULL DEFAULT 0,
  `undress_times` int(10) NOT NULL DEFAULT 0,
  `default_skin` tinyint(1) NOT NULL DEFAULT '1',
  `{$bd_users['session']}` varchar(255) default NULL,
  `{$bd_users['clientToken']}` varchar(255) default NULL,
  `{$bd_users['server']}` varchar(255) default NULL,  

  PRIMARY KEY (`{$bd_users['id']}`),
  UNIQUE KEY `Login` (`{$bd_users['login']}`),
  KEY `group_id` (`{$bd_users['group']}`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

BD("INSERT INTO `{$bd_names['groups']}` 
(`id`,`name`,`lvl`,`system`,`change_skin`,`change_pass`,`change_login`,`change_cloak`,`add_news`,`add_comm`,`adm_comm`) VALUES 
(1,'Пользователь',2,1,1,1,0,0,0,1,0), 
(2,'Заблокированный',0,1,0,0,0,0,0,0,0), 
(3,'Администратор',15,1,1,1,1,1,1,1,1), 
(4,'Непроверенный',1,1,0,0,0,0,0,0,0), 
(5,'VIP',5,0,1,1,0,1,0,1,0),
(6,'Premium',6,0,1,1,0,1,0,1,0);");
BD("CREATE TABLE IF NOT EXISTS `{$bd_names['iconomy']}` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`{$bd_money['login']}` varchar(20) CHARACTER SET utf8 NOT NULL,
`{$bd_money['bank']}` double(64,2) NOT NULL DEFAULT '0.00',
`{$bd_money['money']}` double(64,2) NOT NULL DEFAULT '0.00',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `permission` varchar(200) NOT NULL,
  `world` varchar(50) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=192 ;

CREATE TABLE IF NOT EXISTS `permissions_entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `prefix` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `default` (`default`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

CREATE TABLE IF NOT EXISTS `permissions_inheritance` (
  `id` int(11) NOT NULL auto_increment,
  `child` varchar(50) NOT NULL,
  `parent` varchar(50) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `world` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `child` (`child`,`parent`,`type`,`world`),
  KEY `child_2` (`child`,`type`),
  KEY `parent` (`parent`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;");