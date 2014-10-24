CREATE TABLE IF NOT EXISTS `amango_addonssharepics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `picurl` varchar(255) NOT NULL,
  `sharetime` int(13) unsigned NOT NULL,
  `content` text NOT NULL,
  `views` int(13) unsigned NOT NULL,
  `status` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图片分享插件' AUTO_INCREMENT=1 ;