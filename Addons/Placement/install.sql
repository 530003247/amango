CREATE TABLE IF NOT EXISTS `amango_addonsplacement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `placement_start` varchar(20) NOT NULL,
  `placement_end` varchar(20) NOT NULL,
  `placement_keygroup` varchar(20) DEFAULT NULL,
  `placement_usergroup` varchar(20) DEFAULT NULL,
  `placement_add` int(10) NOT NULL,
  `placement_addtype` varchar(50) NOT NULL,
  `placement_status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='任意位置信息植入表' AUTO_INCREMENT=1 ;