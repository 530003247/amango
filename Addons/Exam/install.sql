CREATE TABLE IF NOT EXISTS `amango_addonsexam` (
  `id` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `has_name` tinyint(2) unsigned NOT NULL,
  `has_share` tinyint(2) unsigned NOT NULL,
  `order` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `logo` varchar(255) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `author` varchar(50) DEFAULT NULL,
  `createtime` int(13) unsigned NOT NULL,
  `desc` text NOT NULL,
  `score` int(4) NOT NULL,
  `score_param` text NOT NULL,
  `has_paiming` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `has_error` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `questions` text,
  `password` varchar(20) DEFAULT NULL,
  `views` int(13) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='考试插件-考卷信息' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `amango_addonsexamlog` (
  `id` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `fromusername` varchar(255) NOT NULL,
  `addtime` int(13) unsigned NOT NULL,
  `testid` int(13) unsigned NOT NULL,
  `score` int(13) unsigned NOT NULL DEFAULT '0',
  `errors` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='考试插件-用户记录' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `amango_addonsques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `q_titletype` varchar(50) NOT NULL,
  `q_title` varchar(255) NOT NULL,
  `a_typedata` text NOT NULL,
  `a_choices` text NOT NULL,
  `a_type` varchar(50) NOT NULL,
  `q_right` text NOT NULL,
  `errors` int(11) NOT NULL DEFAULT '0',
  `group` int(13) unsigned NOT NULL,
  `paixu` int(13) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='考试插件-考题信息' AUTO_INCREMENT=1 ;