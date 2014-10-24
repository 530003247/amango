CREATE TABLE IF NOT EXISTS `amango_addonsneighbours` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(255) NOT NULL COMMENT '发送者昵称',
  `location` text NOT NULL COMMENT '发送地点',
  `creattime` int(13) NOT NULL COMMENT '创建时间',
  `sharetype` varchar(20) NOT NULL COMMENT '消息类型',
  `content` text NOT NULL,
  `to` varchar(250) NOT NULL,
  `school` varchar(250) NOT NULL,
  `view` int(13) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;