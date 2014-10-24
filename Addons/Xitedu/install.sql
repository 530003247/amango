CREATE TABLE IF NOT EXISTS `amango_addonsclass` (
  `xuehao` varchar(100) NOT NULL,
  `itemname` varchar(100) NOT NULL,
  `classname` varchar(100) NOT NULL,
  `week1` longtext,
  `week2` longtext,
  `week3` longtext,
  `week4` longtext,
  `week5` longtext,
  `week6` longtext,
  `week0` longtext,
  PRIMARY KEY (`xuehao`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='教务个人课表' AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `amango_addonseduinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fromusername` varchar(255) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `headimg` varchar(255) CHARACTER SET utf8 NOT NULL,
  `xuehao` varchar(100) NOT NULL,
  `sex` varchar(10) CHARACTER SET utf8 NOT NULL,
  `cardid` varchar(255) CHARACTER SET utf8 NOT NULL,
  `birthday` varchar(50) CHARACTER SET utf8 NOT NULL,
  `xueyuan` varchar(255) CHARACTER SET utf8 NOT NULL,
  `zhuanye` varchar(255) CHARACTER SET utf8 NOT NULL,
  `address` varchar(255) CHARACTER SET utf8 NOT NULL,
  `mingzu` varchar(30) CHARACTER SET utf8 NOT NULL,
  `shengfen` varchar(20) CHARACTER SET utf8 NOT NULL,
  `grade` varchar(100) CHARACTER SET utf8 NOT NULL,
  `type` varchar(100) CHARACTER SET utf8 NOT NULL,
  `youbian` varchar(10) CHARACTER SET utf8 NOT NULL,
  `allexam` text CHARACTER SET utf8 NOT NULL,
  `allscore` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf32 COMMENT='教务个人信息表' AUTO_INCREMENT=1 ;