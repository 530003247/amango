CREATE TABLE IF NOT EXISTS `amango_addonsdiychaxun` (
  `title` varchar(255) NOT NULL,
  `tableid` int(11) NOT NULL,
  `rules` text NOT NULL,
  `replytype` varchar(250) NOT NULL,
  `tpl` text NOT NULL,
  `cache` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `amango_addonsexcel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tablename` varchar(250) DEFAULT NULL,
  `fileds` text,
  `rows` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;