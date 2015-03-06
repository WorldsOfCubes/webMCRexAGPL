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
  `pass_set` tinyint(1) NOT NULL DEFAULT 0,
  `{$bd_users['tmp']}` char(32) NOT NULL DEFAULT '0',
  `{$bd_users['ip']}` varchar(16) DEFAULT NULL,
  `{$bd_users['group']}` int(10) NOT NULL DEFAULT 1,
  `comments_num` int(10) NOT NULL DEFAULT 0,
  `vote` int(10) NOT NULL DEFAULT 0,
  `gameplay_last` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `{$bd_users['ctime']}` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active_last` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `play_times` int(10) NOT NULL DEFAULT 0,
  `undress_times` int(10) NOT NULL DEFAULT 0,
  `default_skin` tinyint(1) NOT NULL DEFAULT '1',
  `{$bd_users['session']}` varchar(255) default NULL,
  `{$bd_users['clientToken']}` varchar(255) default NULL,
  `{$bd_users['server']}` varchar(255) default NULL,  
  `warn_lvl` smallint(10) DEFAULT '0',
  `topics` smallint(10) DEFAULT '0',
  `posts` smallint(10) DEFAULT '0',

  PRIMARY KEY (`{$bd_users['id']}`),
  UNIQUE KEY `Login` (`{$bd_users['login']}`),
  KEY `group_id` (`{$bd_users['group']}`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

BD("INSERT INTO `{$bd_names['groups']}` 
(`id`,`name`,`pex_name`,`lvl`,`system`,`change_skin`,`change_pass`,`change_login`,`change_cloak`,`change_prefix`,`add_news`,`add_comm`,`adm_comm`) VALUES
(1,'Пользователь','Default',2,1,1,1,0,0,0,0,1,0),
(2,'Заблокированный','Default',0,1,0,0,0,0,0,0,0,0),
(3,'Администратор','admin',15,1,1,1,1,1,1,1,1,1),
(4,'Непроверенный','Default',1,1,0,0,0,0,0,0,0,0),
(5,'VIP','vip',5,1,1,1,0,1,0,0,1,0),
(6,'Premium','premium',6,1,1,1,0,1,1,0,1,0),
(8,'Модератор','moder',8,1,1,1,0,1,1,0,1,0);");