SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- 表的结构 `amango_account`
--

CREATE TABLE IF NOT EXISTS `amango_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `account_name` varchar(255) NOT NULL COMMENT '公众号名称',
  `account_oldid` varchar(255) NOT NULL COMMENT '公众号原始id',
  `account_weixin` varchar(255) NOT NULL COMMENT '公众号[微信号]',
  `account_default` char(10) NOT NULL DEFAULT 'on' COMMENT '默认公众号',
  `account_appid` varchar(255) NOT NULL COMMENT 'AppId',
  `account_secret` varchar(255) NOT NULL COMMENT 'Secret',
  `account_nickname` varchar(255) NOT NULL COMMENT '公众号花名',
  `account_qq` varchar(255) NOT NULL COMMENT '公众号客服',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `account_token` varchar(255) NOT NULL COMMENT 'TOKEN',
  `account_sub` varchar(255) NOT NULL COMMENT '关注链接',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_action`
--

CREATE TABLE IF NOT EXISTS `amango_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text NOT NULL COMMENT '行为规则',
  `log` text NOT NULL COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统行为表' AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `amango_action`
--

INSERT INTO `amango_action` (`id`, `name`, `title`, `remark`, `rule`, `log`, `type`, `status`, `update_time`) VALUES
(1, 'user_login', '用户登录', '积分+10，每天一次', 'table:member|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;', '[user|get_nickname]在[time|time_format]登录了后台', 1, 1, 1387181220),
(2, 'add_article', '发布文章', '积分+5，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:5', '', 2, 0, 1380173180),
(3, 'review', '评论', '评论积分+1，无限制', 'table:member|field:score|condition:uid={$self}|rule:score+1', '', 2, 1, 1383285646),
(4, 'add_document', '发表文档', '积分+10，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+10|cycle:24|max:5', '[user|get_nickname]在[time|time_format]发表了一篇文章。\r\n表[model]，记录编号[record]。', 2, 0, 1386139726),
(5, 'add_document_topic', '发表讨论', '积分+5，每天上限10次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:10', '', 2, 0, 1383285551),
(6, 'update_config', '更新配置', '新增或修改或删除配置', '', '', 1, 1, 1383294988),
(7, 'update_model', '更新模型', '新增或修改模型', '', '', 1, 1, 1383295057),
(8, 'update_attribute', '更新属性', '新增或更新或删除属性', '', '', 1, 1, 1383295963),
(9, 'update_channel', '更新导航', '新增或修改或删除导航', '', '', 1, 1, 1383296301),
(10, 'update_menu', '更新菜单', '新增或修改或删除菜单', '', '', 1, 1, 1383296392),
(11, 'update_category', '更新分类', '新增或修改或删除分类', '', '', 1, 1, 1383296765);

-- --------------------------------------------------------

--
-- 表的结构 `amango_action_log`
--

CREATE TABLE IF NOT EXISTS `amango_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `amango_addons`
--

CREATE TABLE IF NOT EXISTS `amango_addons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `weixin` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `logo` varchar(255) DEFAULT NULL,
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  `has_profile` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否含有用户中心',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='插件表' AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `amango_addons`
--

INSERT INTO `amango_addons` (`id`, `name`, `title`, `description`, `status`, `weixin`, `logo`, `config`, `author`, `version`, `create_time`, `has_adminlist`, `has_profile`) VALUES
(1, 'SnatchTieba', '贴吧获取数据', '通过配置参数,自动抓取参数', 1, 1, 'logo.jpg', '{"tieba_name":"\\u96c6\\u7f8e\\u5927\\u5b66\\u5427","tieba_nums":"6","tieba_jinghua":"1","tieba_extra":"1","tieba_cache":"0"}', '拉开让哥单打', '0.1', 1411022095, 0, 0),
(18, 'EditorForAdmin', '后台编辑器', '用于增强整站长文本的输入和显示', 1, 0, NULL, '{"editor_type":"2","editor_wysiwyg":"2","editor_markdownpreview":"1","editor_height":"500px","editor_resize_type":"1"}', 'thinkphp', '0.2', 1413558510, 0, 0),
(4, 'Editor', '前台编辑器', '用于增强整站长文本的输入和显示', 1, 0, NULL, '{"editor_type":"2","editor_wysiwyg":"1","editor_height":"300px","editor_resize_type":"1"}', 'thinkphp', '0.1', 1411022111, 0, 0),
(19, 'DevTeam', '开发团队信息', '开发团队成员信息', 1, 0, NULL, '{"title":"OneThink\\u5f00\\u53d1\\u56e2\\u961f","width":"2","display":"1"}', 'thinkphp', '0.1', 1413606703, 0, 0),
(16, 'SiteStat', '站点统计信息', '统计站点的基础信息', 1, 0, NULL, '{"title":"\\u7cfb\\u7edf\\u4fe1\\u606f","width":"2","display":"1"}', 'thinkphp', '0.1', 1412501114, 0, 0),
(13, 'Xitedu', '微信教务', '集大微信教务', 1, 1, 'logo.jpg', '{"random":"1"}', '陈登禄', '0.1', 1411129052, 1, 1),
(8, 'Neighbours', '隔壁', '芒果高校的特色功能', 0, 1, 'logo.jpg', '{"name":"","title":"","nums":"3","is_share":null,"is_at":null,"dsfjr":"","jr_cache":"60","dsfsq":"","sq_cache":"60"}', '拉开让哥单打', '0.1', 1411022119, 1, 1),
(9, 'Sharephotos', '图吧', '随手分享图片插件', 1, 1, 'logo.jpg', '{"title":"\\u8292\\u679c\\u56fe\\u5427","pagenums":"8","shareitmes":"2","random":"1"}', '陈登禄', '0.1', 1411022120, 1, 0),
(10, 'Attachment', '附件', '用于文档模型上传附件', 1, 0, NULL, 'null', 'thinkphp', '0.1', 1411022148, 1, 0),
(11, 'SystemInfo', '系统环境信息', '用于显示一些服务器的信息', 1, 0, NULL, '{"title":"\\u7cfb\\u7edf\\u4fe1\\u606f","width":"2","display":"1"}', 'thinkphp', '0.1', 1411022156, 0, 0),
(12, 'SocialComment', '通用社交化评论', '集成了各种社交化评论插件，轻松集成到系统中。', 0, 0, NULL, '{"comment_type":"1","comment_uid_youyan":"90040","comment_short_name_duoshuo":"","comment_form_pos_duoshuo":"buttom","comment_data_list_duoshuo":"10","comment_data_order_duoshuo":"asc"}', 'thinkphp', '0.1', 1411093995, 0, 0),
(14, 'Placement', '信息植入', '信息植入插件,使用方法:在tag界面选择该插件,在参数中输入相应的id:调用ID', 1, 1, 'logo.jpg', '{"excel_type":"excel","excel_path":"","excel_tablename":"tablename","excel_readxls":"1","excel_currentrow":"2","excel_parxhtml":"0","excel_param":"ziduan|A"}', '拉开让哥单打', '0.1', 1411740044, 1, 0),
(15, 'Excelimport', 'Excel导入', '该插件主要用于简单的Excel表格导入进网站数据库，自动生成数据表，自定义相关字段主键等等......', 1, 1, 'logo.jpg', '{"excel_type":"excel","excel_path":"","excel_tablename":"tablename","excel_readxls":"1","excel_currentrow":"2","excel_parxhtml":"0","excel_param":"ziduan|A"}', '拉开让哥单打', '0.1', 1412070502, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `amango_addonsclass`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='教务个人课表';

-- --------------------------------------------------------

--
-- 表的结构 `amango_addonsdiychaxun`
--

CREATE TABLE IF NOT EXISTS `amango_addonsdiychaxun` (
  `title` varchar(255) NOT NULL,
  `tableid` int(11) NOT NULL,
  `rules` text NOT NULL,
  `replytype` varchar(250) NOT NULL,
  `tpl` text NOT NULL,
  `cache` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `amango_addonseduinfo`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `amango_addonsexcel`
--

CREATE TABLE IF NOT EXISTS `amango_addonsexcel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tablename` varchar(250) DEFAULT NULL,
  `fileds` text,
  `rows` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_addonsneighbours`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `amango_addonsneighbours`
--

INSERT INTO `amango_addonsneighbours` (`id`, `from`, `location`, `creattime`, `sharetype`, `content`, `to`, `school`, `view`) VALUES
(1, '4', '', 1411198222, 'text', '谁在隔壁', '', '', 21);

-- --------------------------------------------------------

--
-- 表的结构 `amango_addonsplacement`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='任意位置信息植入表' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `amango_addonsplacement`
--

INSERT INTO `amango_addonsplacement` (`id`, `placement_start`, `placement_end`, `placement_keygroup`, `placement_usergroup`, `placement_add`, `placement_addtype`, `placement_status`) VALUES
(3, '13:10', '23:55', '1', 'general', 67, 'Dantw', 1),
(4, '00:0', '23:55', '', 'general', 137, 'Dantw', 1);

-- --------------------------------------------------------

--
-- 表的结构 `amango_addonssharepics`
--

CREATE TABLE IF NOT EXISTS `amango_addonssharepics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `picurl` varchar(255) NOT NULL,
  `sharetime` int(13) unsigned NOT NULL,
  `content` text NOT NULL,
  `views` int(13) unsigned NOT NULL,
  `setgood` text NOT NULL,
  `status` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='图片分享插件' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_attachment`
--

CREATE TABLE IF NOT EXISTS `amango_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '附件显示名',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件类型',
  `source` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资源ID',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联记录ID',
  `download` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '附件大小',
  `dir` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '上级目录ID',
  `sort` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `idx_record_status` (`record_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_attribute`
--

CREATE TABLE IF NOT EXISTS `amango_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '字段注释',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段定义',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '数据类型',
  `value` varchar(100) NOT NULL DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `homeremark` varchar(100) NOT NULL,
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `home_show` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reply_show` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '参数',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型id',
  `is_must` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必填',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `validate_rule` varchar(255) NOT NULL,
  `validate_time` tinyint(1) unsigned NOT NULL,
  `error_info` varchar(100) NOT NULL,
  `validate_type` varchar(25) NOT NULL,
  `auto_rule` varchar(100) NOT NULL,
  `auto_time` tinyint(1) unsigned NOT NULL,
  `auto_type` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模型属性表' AUTO_INCREMENT=196 ;

--
-- 转存表中的数据 `amango_attribute`
--

INSERT INTO `amango_attribute` (`id`, `name`, `title`, `field`, `type`, `value`, `remark`, `homeremark`, `is_show`, `home_show`, `reply_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`) VALUES
(1, 'uid', '用户ID', 'int(10) unsigned NOT NULL ', 'num', '0', '', '', 0, 0, 0, '', 1, 0, 1, 1384508362, 1383891233, '', 0, '', '', '', 0, ''),
(2, 'name', '标识', 'char(40) NOT NULL ', 'string', '', '同一根节点下标识不重复', '', 0, 0, 0, '', 1, 0, 1, 1398133144, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(3, 'title', '标题', 'char(80) NOT NULL ', 'string', '', '文档标题', '标题', 1, 1, 0, '', 1, 0, 1, 1402562024, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(4, 'category_id', '所属分类', 'int(10) unsigned NOT NULL ', 'string', '', '', '', 0, 0, 0, '', 1, 0, 1, 1384508336, 1383891233, '', 0, '', '', '', 0, ''),
(5, 'description', '内容摘要', 'char(140) NOT NULL ', 'textarea', '', '', '', 1, 0, 0, '', 1, 0, 1, 1398133173, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(6, 'root', '根节点', 'int(10) unsigned NOT NULL ', 'num', '0', '该文档的顶级文档编号', '', 0, 0, 0, '', 1, 0, 1, 1384508323, 1383891233, '', 0, '', '', '', 0, ''),
(7, 'pid', '所属ID', 'int(10) unsigned NOT NULL ', 'num', '0', '父文档编号', '', 0, 0, 0, '', 1, 0, 1, 1384508543, 1383891233, '', 0, '', '', '', 0, ''),
(8, 'model_id', '内容模型ID', 'tinyint(3) unsigned NOT NULL ', 'num', '0', '该文档所对应的模型', '', 0, 0, 0, '', 1, 0, 1, 1384508350, 1383891233, '', 0, '', '', '', 0, ''),
(9, 'type', '内容类型', 'tinyint(3) unsigned NOT NULL ', 'select', '2', '', '', 1, 0, 0, '1:目录\r\n2:主题\r\n3:段落', 1, 0, 1, 1384511157, 1383891233, '', 0, '', '', '', 0, ''),
(10, 'position', '推荐位', 'smallint(5) unsigned NOT NULL ', 'checkbox', '0', '多个推荐则将其推荐值相加', '', 1, 0, 0, '1:列表推荐\r\n2:频道页推荐\r\n4:首页推荐', 1, 0, 1, 1383895640, 1383891233, '', 0, '', '', '', 0, ''),
(11, 'link_id', '外链', 'int(10) unsigned NOT NULL ', 'num', '0', '0-非外链，大于0-外链ID,需要函数进行链接与编号的转换', '', 1, 0, 0, '', 1, 0, 1, 1383895757, 1383891233, '', 0, '', '', '', 0, ''),
(12, 'cover_id', '图文封面', 'varchar(255) NOT NULL', 'kingpicture', '0', '0-无封面，大于0-封面图片ID，需要函数处理', '', 1, 0, 0, '', 1, 0, 1, 1400330445, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(13, 'display', '可见性', 'tinyint(3) unsigned NOT NULL ', 'radio', '1', '', '', 0, 0, 0, '0:不可见\r\n1:所有人可见', 1, 0, 1, 1398142718, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(14, 'deadline', '截止日期', 'int(10) unsigned NOT NULL ', 'datetime', '0', '0-永久有效', '', 1, 1, 0, '', 1, 0, 1, 1402570936, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(15, 'attach', '附件数量', 'tinyint(3) unsigned NOT NULL ', 'num', '0', '', '', 0, 0, 0, '', 1, 0, 1, 1387260355, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(16, 'view', '浏览量', 'int(10) unsigned NOT NULL ', 'num', '0', '', '', 0, 0, 0, '', 1, 0, 1, 1398131821, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(17, 'comment', '评论数', 'int(10) unsigned NOT NULL ', 'num', '0', '', '', 0, 0, 0, '', 1, 0, 1, 1398132821, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(18, 'extend', '扩展统计字段', 'int(10) unsigned NOT NULL ', 'num', '0', '根据需求自行使用', '', 0, 0, 0, '', 1, 0, 1, 1384508264, 1383891233, '', 0, '', '', '', 0, ''),
(19, 'level', '优先级', 'int(10) unsigned NOT NULL ', 'num', '0', '越高排序越靠前', '', 1, 0, 0, '', 1, 0, 1, 1383895894, 1383891233, '', 0, '', '', '', 0, ''),
(20, 'create_time', '定时发布', 'int(10) unsigned NOT NULL ', 'datetime', '0', '', '', 1, 0, 0, '', 1, 0, 1, 1398139950, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(21, 'update_time', '更新时间', 'int(10) unsigned NOT NULL ', 'datetime', '0', '', '', 0, 0, 0, '', 1, 0, 1, 1384508277, 1383891233, '', 0, '', '', '', 0, ''),
(22, 'status', '审核状态', 'tinyint(4) NOT NULL ', 'radio', '1', '', '', 1, 0, 0, '-1:删除\r\n0:禁用\r\n1:正常\r\n2:待审核\r\n3:草稿', 1, 1, 1, 1398142700, 1383891233, '', 0, '', 'regex', '', 0, 'function'),
(23, 'parse', '内容解析类型', 'tinyint(3) unsigned NOT NULL ', 'select', '0', '', '', 0, 0, 0, '0:html\r\n1:ubb\r\n2:markdown', 2, 0, 1, 1384511049, 1383891243, '', 0, '', '', '', 0, ''),
(24, 'content', '文章内容', 'text NOT NULL ', 'editor', '', '', '', 1, 0, 0, '', 2, 0, 1, 1383896225, 1383891243, '', 0, '', '', '', 0, ''),
(25, 'template', '模板风格', 'varchar(100) NOT NULL ', 'string', '', '参照display方法参数的定义', '', 1, 0, 0, '', 2, 0, 1, 1398133317, 1383891243, '', 0, '', 'regex', '', 0, 'function'),
(26, 'bookmark', '收藏数', 'int(10) unsigned NOT NULL ', 'num', '0', '', '', 1, 0, 0, '', 2, 0, 1, 1383896103, 1383891243, '', 0, '', '', '', 0, ''),
(27, 'parse', '内容解析类型', 'tinyint(3) unsigned NOT NULL ', 'select', '0', '', '', 0, 0, 0, '0:html\r\n1:ubb\r\n2:markdown', 3, 0, 1, 1387260461, 1383891252, '', 0, '', 'regex', '', 0, 'function'),
(28, 'content', '下载详细描述', 'text NOT NULL ', 'editor', '', '', '', 1, 0, 0, '', 3, 0, 1, 1383896438, 1383891252, '', 0, '', '', '', 0, ''),
(29, 'template', '详情页显示模板', 'varchar(100) NOT NULL ', 'string', '', '', '', 1, 0, 0, '', 3, 0, 1, 1383896429, 1383891252, '', 0, '', '', '', 0, ''),
(30, 'file_id', '文件ID', 'int(10) unsigned NOT NULL ', 'file', '0', '需要函数处理', '', 1, 0, 0, '', 3, 0, 1, 1383896415, 1383891252, '', 0, '', '', '', 0, ''),
(31, 'download', '下载次数', 'int(10) unsigned NOT NULL ', 'num', '0', '', '', 1, 0, 0, '', 3, 0, 1, 1383896380, 1383891252, '', 0, '', '', '', 0, ''),
(32, 'size', '文件大小', 'bigint(20) unsigned NOT NULL ', 'num', '0', '单位bit', '', 1, 0, 0, '', 3, 0, 1, 1383896371, 1383891252, '', 0, '', '', '', 0, ''),
(114, 'denyuser', '黑名单组', 'varchar(255) NOT NULL', 'laiyuanbox', '', '该用户组将无法阅览该内容', '', 1, 0, 0, '调用【会员组】', 1, 0, 1, 1398141339, 1398141339, '', 3, '', 'regex', '', 3, 'function'),
(35, 'shopname', '店铺名称', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 4, 1, 1, 1397445169, 1397445169, '', 3, '该商家已被注册', 'unique', '', 3, 'function'),
(36, 'keyword_start', '开始日期', 'int(10) NOT NULL', 'datetime', '', '', '', 1, 0, 0, '', 5, 1, 1, 1398355472, 1397469832, '', 3, '', 'regex', 'time', 3, 'function'),
(37, 'keyword_end', '截止日期', 'int(10) UNSIGNED NOT NULL', 'datetime', '', '', '', 1, 0, 0, '', 5, 1, 1, 1397470278, 1397469893, '', 3, '', 'regex', '', 3, 'function'),
(38, 'keyword_click', '激活次数', 'int(10) UNSIGNED NOT NULL', 'num', '0', '', '', 0, 0, 0, '', 5, 0, 1, 1397470038, 1397470038, '', 3, '', 'regex', '', 3, 'function'),
(39, 'keyword_top', '上文词汇', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '', 1, 0, 0, '', 5, 0, 1, 1397470354, 1397470098, '', 3, '', 'regex', '', 3, 'function'),
(40, 'keyword_down', '下文继承', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:开启\r\n0:关闭', 5, 0, 1, 1397470244, 1397470244, '', 3, '', 'regex', '', 3, 'function'),
(41, 'keyword_group', '关键词组', 'int(10) UNSIGNED NOT NULL', 'num', '', '', '', 1, 0, 0, '', 5, 0, 1, 1397470455, 1397470455, '', 3, '', 'regex', '', 3, 'function'),
(42, 'keyword_cache', '缓存时间', 'int(10) UNSIGNED NOT NULL', 'num', '0', '(单位:秒)', '', 1, 0, 0, '', 5, 0, 1, 1397470555, 1397470555, '', 3, '', 'regex', '', 3, 'function'),
(43, 'keyword_post', '请求类型', 'varchar(100) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 5, 1, 1, 1397470976, 1397470808, '', 3, '', 'regex', '', 3, 'function'),
(120, 'response_name', '响应体简称', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 17, 0, 1, 1398355632, 1398355632, '', 3, '', 'regex', '', 3, 'function'),
(119, 'response_reply', '回复体类型', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 17, 0, 1, 1398355298, 1398355243, '', 3, '', 'regex', '', 3, 'function'),
(118, 'response_compos', '响应结构体', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 17, 1, 1, 1398355131, 1398355131, '', 3, '', 'regex', '', 3, 'function'),
(47, 'keyword_content', '关键词内容', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 5, 0, 1, 1397662779, 1397472612, '', 3, '', 'regex', '', 3, 'function'),
(48, 'keyword_rules', '匹配语句', 'text NOT NULL', 'textarea', '', '', '', 0, 0, 0, '', 5, 0, 1, 1397662767, 1397472777, '', 3, '', 'regex', '', 3, 'function'),
(49, 'denytag_keyword', '禁止标识', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 5, 0, 1, 1397657159, 1397657069, '', 3, '', 'regex', '', 3, 'function'),
(50, 'before_keyword', '激活前操作', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 5, 0, 1, 1397657145, 1397657128, '', 3, '', 'regex', '', 3, 'function'),
(51, 'after_keyword', '激活后操作', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 5, 0, 1, 1397657215, 1397657215, '', 3, '', 'regex', '', 3, 'function'),
(52, 'click_model', '菜单模式', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 5, 0, 1, 1397657351, 1397657351, '', 3, '', 'regex', '', 3, 'function'),
(53, 'lock_model', '锁定模块', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 5, 0, 1, 1397657393, 1397657393, '', 3, '', 'regex', '', 3, 'function'),
(54, 'rules_title', '规则标识', 'varchar(255) NOT NULL', 'string', '', '字母数字组合，规则标识不能为空，', '', 1, 0, 0, '', 6, 1, 1, 1397896341, 1397815832, 'require', 3, '该规则标识已存在，请换个标识', 'unique', '', 3, 'function'),
(55, 'rules_content', '规则内容', 'varchar(255) NOT NULL', 'string', '', '请填写合法的正则语句(纯静态正则，半静态正则，动态正则)', '', 1, 0, 0, '', 6, 1, 1, 1397815969, 1397815895, '', 3, '', 'regex', '', 3, 'function'),
(56, 'sort', '规则排序', 'int(13) UNSIGNED NOT NULL', 'num', '0', '', '', 1, 0, 0, '', 6, 0, 1, 1397840580, 1397816097, '', 3, '', 'length', '', 3, 'function'),
(59, 'rules_description', '规则描述', 'text NOT NULL', 'textarea', '', '描述规则如何使用', '', 1, 0, 0, '', 6, 0, 1, 1397816237, 1397816237, '', 3, '', 'regex', '', 3, 'function'),
(60, 'status', '规则状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:开启\r\n0:关闭', 6, 1, 1, 1397882013, 1397816376, '', 3, '', 'regex', '', 3, 'function'),
(61, 'rules_name', '规则名称', 'text NOT NULL', 'string', '', '方便添加关键词选择规则', '', 1, 0, 0, '', 6, 1, 1, 1397816955, 1397816454, '', 3, '', 'regex', '', 3, 'function'),
(62, 'posts_name', '请求名称', 'text NOT NULL', 'string', '', '便于关键词添加时候选择', '', 1, 0, 0, '', 7, 1, 1, 1397882361, 1397881541, '', 3, '', 'regex', '', 3, 'function'),
(63, 'posts_title', '请求标识', 'varchar(255) NOT NULL', 'string', '', '建议仅限英文字母作为标识', '', 1, 0, 0, '', 7, 1, 1, 1397896328, 1397881614, 'require', 3, '请求类型标识重复', 'unique', '', 3, 'function'),
(64, 'posts_fields', '附带字段', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 7, 0, 1, 1397881660, 1397881660, '', 3, '', 'regex', '', 3, 'function'),
(65, 'status', '请求状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:开启\r\n0:关闭', 7, 0, 1, 1397882054, 1397881836, '', 3, '', 'regex', '', 3, 'function'),
(66, 'sort', '请求排序', 'int(10) UNSIGNED NOT NULL', 'num', '1', '', '', 1, 0, 0, '', 7, 0, 1, 1397881882, 1397881882, '', 3, '', 'regex', '', 3, 'function'),
(67, 'posts_description', '请求描述', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 7, 0, 1, 1397881924, 1397881924, '', 3, '', 'regex', '', 3, 'function'),
(68, 'tagscate_name', 'TAG组名称', 'varchar(50) NOT NULL', 'string', '', '分组标签名称', '', 1, 0, 0, '', 8, 1, 1, 1397895485, 1397895485, '', 3, '', 'regex', '', 3, 'function'),
(69, 'tagscate_title', 'TAG组标识', 'varchar(50) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 8, 1, 1, 1397896215, 1397895526, 'require', 3, 'TAG组标识重复', 'unique', '', 3, 'function'),
(70, 'tagscate_description', 'TAG组描述', 'text NOT NULL', 'textarea', '', '简要说明该标签组的作用', '', 1, 0, 0, 'ddd', 8, 0, 1, 1397907070, 1397895583, '', 3, '', 'regex', '', 3, 'function'),
(71, 'status', 'TAG组状态', 'tinyint(2) NOT NULL', 'bool', '1', '控制该TAG组是否解析', '', 1, 0, 0, '1:开启\r\n0:关闭', 8, 1, 1, 1397895654, 1397895654, '', 3, '', 'regex', '', 3, 'function'),
(72, 'tagslists_title', 'TAG标识', 'varchar(50) NOT NULL', 'string', '', '已植入微信回复的标签', '', 1, 0, 0, '', 9, 0, 1, 1397908554, 1397908554, 'equal', 3, '该TAG标识已被使用', 'unique', '', 3, 'function'),
(73, 'tagslists_group', 'TAG分组', 'char(50) NOT NULL', 'laiyuan', '', 'TAG所属分组', '', 1, 0, 0, '调用【TAG分组】', 9, 1, 1, 1399342968, 1397908975, '', 3, '', 'regex', '', 3, 'function'),
(74, 'tagslists_type', 'TAG类型', 'char(50) NOT NULL', 'select', 'static', '支持：【静态内容】【函数调用】【模块操作方法】', '', 1, 0, 0, 'static:静态内容\r\nfunc:调用函数\r\naction:模块操作\r\n', 9, 1, 1, 1397909202, 1397909202, '', 3, '', 'regex', '', 3, 'function'),
(76, 'tagslists_action', 'TAG操作', 'varchar(255) NOT NULL', 'string', 'static', '【静态内容：static】【函数调用：函数名】【模块操作：分为两种】(1.插件模块:Addons://插件名称/插件控制器/插件操作)(2.系统模块:分组名称/控制器/操作)', '', 1, 0, 0, '', 9, 1, 1, 1397909407, 1397909407, '', 3, '', 'regex', '', 3, 'function'),
(77, 'tagslists_param', 'TAG操作参数', 'text NOT NULL', 'textarea', '', '禁止换行！格式:键名=>键值;(格式:键名=>键值;注意有;符号)', '', 1, 0, 0, '', 9, 0, 1, 1398000001, 1397909532, '', 3, '', 'regex', '', 3, 'function'),
(78, 'tagslists_description', 'TAG描述', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 9, 0, 1, 1397909687, 1397909687, '', 3, '', 'regex', '', 3, 'function'),
(79, 'status', '状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:开启\r\n0::关闭', 9, 1, 1, 1397920164, 1397920164, '', 3, '', 'regex', '', 3, 'function'),
(80, 'sort', 'TAG排序', 'int(10) UNSIGNED NOT NULL', 'num', '0', '', '', 1, 0, 0, '', 9, 0, 1, 1397920294, 1397920193, '', 3, '', 'regex', '', 3, 'function'),
(81, 'data_name', '模型名称', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 10, 1, 1, 1397985203, 1397985203, '', 3, '', 'regex', '', 3, 'function'),
(82, 'data_type', '模型类型', 'varchar(20) NOT NULL', 'string', 'local', '', '', 1, 0, 0, '', 10, 1, 1, 1397985238, 1397985238, '', 3, '', 'regex', '', 3, 'function'),
(83, 'data_table', '数据表名', 'varchar(100) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 10, 1, 1, 1397993083, 1397985280, '', 3, '', 'regex', '', 3, 'function'),
(84, 'data_fields', '读取字段', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 10, 1, 1, 1397985319, 1397985319, '', 3, '', 'regex', '', 3, 'function'),
(85, 'data_condition', '读取条件', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 10, 0, 1, 1397985348, 1397985348, '', 3, '', 'regex', '', 3, 'function'),
(86, 'status', '状态', 'tinyint(2) NOT NULL', 'bool', '', '', '', 1, 0, 0, '1:开启\r\n0:关闭', 10, 1, 1, 1397985413, 1397985413, '', 3, '', 'regex', '', 3, 'function'),
(87, 'keywordcate_name', '关键词分组名', 'varchar(150) NOT NULL', 'string', '', '简单易懂的关键词组便于关键词管理', '', 1, 0, 0, '', 11, 0, 1, 1398038514, 1398000864, '', 3, '', 'unique', '', 3, 'function'),
(88, 'keywordcate_denyuser', '黑名单组', 'varchar(255) NOT NULL', 'laiyuanbox', '', '该用户组的关注者将无法激活该组关键词', '', 1, 0, 0, '调用【会员组】', 11, 0, 1, 1398039628, 1398001923, '', 3, '', 'regex', '', 3, 'function'),
(89, 'status', '状态', 'tinyint(2) NOT NULL', 'bool', '1', '控制分组状态', '', 1, 0, 0, '1:开启\r\n0:关闭', 11, 0, 1, 1398002561, 1398001991, '', 3, '', 'regex', '', 3, 'function'),
(90, 'followercate_title', '会员组标识', 'varchar(50) NOT NULL', 'string', '', '纯英文和数字组合', '', 1, 0, 0, '', 13, 1, 1, 1398003603, 1398003544, 'equal', 3, '', 'unique', '', 3, 'function'),
(91, 'followercate_name', '会员组名', 'varchar(100) NOT NULL', 'string', '', '中文名称', '', 1, 0, 0, '', 13, 1, 1, 1398003693, 1398003587, '', 3, '', 'regex', '', 3, 'function'),
(92, 'followercate_des', '会员组特权说明', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 13, 0, 1, 1398003648, 1398003635, '', 3, '', 'regex', '', 3, 'function'),
(93, 'status', '状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:开启\r\n0:关闭', 13, 1, 1, 1398003682, 1398003682, '', 3, '', 'regex', '', 3, 'function'),
(176, 'huodonextra', '限制说明', 'text NOT NULL', 'string', '', '人数限制，消费金额等等一些额外限制条件', '人数限制', 1, 1, 0, '', 21, 0, 1, 1402643557, 1402303842, '', 3, '', 'regex', '', 3, 'function'),
(175, 'huodonaddress', '举办地点', 'varchar(255) NOT NULL', 'string', '', '活动举办地点', '地址', 1, 1, 0, '', 21, 1, 1, 1402564522, 1402294138, '', 3, '', 'regex', '', 3, 'function'),
(172, 'huodondesc', '活动介绍', 'text NOT NULL', 'editor', '', '活动内容描述', '活动简介', 1, 1, 0, '', 21, 1, 1, 1402564468, 1402293787, '', 3, '', 'regex', '', 3, 'function'),
(173, 'huodonchenban', '承办方', 'varchar(255) NOT NULL', 'string', '', '活动承办方', '', 1, 0, 0, '', 21, 1, 1, 1402293889, 1402293889, '', 3, '', 'regex', '', 3, 'function'),
(174, 'huodonjuban', '主办方', 'text NOT NULL', 'string', '', '活动举办方的详细描述', '发起人', 1, 1, 0, '', 21, 1, 1, 1402564486, 1402293958, '', 3, '', 'regex', '', 3, 'function'),
(117, 'response_xml', '响应XML', 'text NOT NULL', 'textarea', '', '', '', 1, 0, 0, '', 17, 1, 1, 1398355076, 1398355076, '', 3, '', 'regex', '', 3, 'function'),
(121, 'status', '状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:开启\r\n0:关闭', 17, 0, 1, 1398385511, 1398385511, '', 3, '', 'regex', '', 3, 'function'),
(122, 'keyword_reaponse', '响应体ID', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 5, 0, 1, 1398401306, 1398401306, '', 3, '', 'regex', '', 3, 'function'),
(123, 'status', '状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '', 5, 0, 1, 1398407701, 1398407701, '', 3, '', 'regex', '', 3, 'function'),
(124, 'sort', '排序', 'int(10) UNSIGNED NOT NULL', 'num', '0', '', '', 1, 0, 0, '', 5, 0, 1, 1398414912, 1398414912, '', 3, '', 'regex', '', 3, 'function'),
(125, 'keyword_reply', '匹配规则ID', 'varchar(100) NOT NULL ', 'string', '', '', '', 1, 0, 0, '', 5, 0, 1, 1398497035, 1398495640, '', 3, '', 'regex', '', 3, 'function'),
(126, 'webuntil_name', '接口名称', 'varchar(255) NOT NULL', 'string', '', '建议采用中文便于识别', '', 1, 0, 0, '', 18, 1, 1, 1398529122, 1398524951, '', 3, '', 'regex', '', 3, 'function'),
(127, 'webuntil_title', '接口标识', 'varchar(100) NOT NULL', 'string', '', '建议采用纯英文字符', '', 1, 0, 0, '', 18, 1, 1, 1398529143, 1398525068, 'unique', 3, '', 'unique', '', 3, 'function'),
(128, 'webuntil_param', '附属参数', 'text NOT NULL', 'textarea', '', '格式按照|键名:键名|需要额外发送的参数', '', 1, 0, 0, '', 18, 0, 1, 1398528269, 1398525114, '', 3, '', 'regex', '', 3, 'function'),
(170, 'webuntil_type', '请求类型', 'char(50) NOT NULL', 'select', 'post', '', '', 1, 0, 0, 'get:GET请求\r\npost:POST请求', 18, 1, 1, 1399792119, 1399792119, '', 3, '', 'regex', '', 3, 'function'),
(130, 'webuntil_backtype', '返回类型', 'char(50) NOT NULL', 'select', 'xml', '接口返回数据类型', '', 1, 0, 0, 'string:标准字符串\r\nxml:标准微信XML\r\njson:标准JSON', 18, 1, 1, 1398525645, 1398525645, '', 3, '', 'regex', '', 3, 'function'),
(131, 'webuntil_sigtype', '关键词处理', 'char(50) NOT NULL', 'select', 'no', '关键词处理方式', '', 1, 0, 0, 'yes:过滤后关键词\r\nno:不过滤关键词', 18, 1, 1, 1398526159, 1398526159, '', 3, '', 'regex', '', 3, 'function'),
(132, 'webuntil_cache', '数据缓存', 'int(10) UNSIGNED NOT NULL', 'num', '0', '缓存单位:秒', '', 1, 0, 0, '', 18, 0, 1, 1398528599, 1398526287, '', 3, '', 'regex', '', 3, 'function'),
(133, 'webuntil_tag', '植入TAG', 'tinyint(2) NOT NULL', 'bool', '0', '是否植入芒果TAG', '', 1, 0, 0, '1:植入\r\n0:不植入', 18, 1, 1, 1398526969, 1398526969, '', 3, '', 'regex', '', 3, 'function'),
(134, 'webuntil_url', '请求URL', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 18, 1, 1, 1398527509, 1398527509, '', 3, '', 'regex', '', 3, 'function'),
(135, 'webuntil_token', 'TOKEN值', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 18, 0, 1, 1398527578, 1398527578, '', 3, '', 'regex', '', 3, 'function'),
(136, 'status', '状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:启用\r\n0:关闭', 18, 1, 1, 1398527621, 1398527621, '', 3, '', 'regex', '', 3, 'function'),
(137, 'account_name', '公众号名称', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 19, 1, 1, 1398531438, 1398531438, '', 3, '', 'regex', '', 3, 'function'),
(138, 'account_oldid', '公众号原始id', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 19, 1, 1, 1398578575, 1398531466, 'unique', 3, '', 'unique', '', 3, 'function'),
(139, 'account_weixin', '公众号[微信号]', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 19, 1, 1, 1398578588, 1398531520, 'unique', 3, '', 'unique', '', 3, 'function'),
(140, 'account_default', '默认公众号', 'char(10) NOT NULL', 'radio', 'on', '', '', 1, 0, 0, 'default:默认\r\non:可用\r\noff:禁用\r\n', 19, 1, 1, 1398532093, 1398532093, '', 3, '', 'regex', '', 3, 'function'),
(141, 'account_appid', 'AppId', 'varchar(255) NOT NULL', 'string', '', '认证号的AppId', '', 1, 0, 0, '', 19, 0, 1, 1398532164, 1398532164, '', 3, '', 'regex', '', 3, 'function'),
(142, 'account_secret', 'Secret', 'varchar(255) NOT NULL', 'string', '', '认证号的Secret', '', 1, 0, 0, '', 19, 0, 1, 1398532202, 1398532202, '', 3, '', 'regex', '', 3, 'function'),
(143, 'account_nickname', '公众号花名', 'varchar(255) NOT NULL', 'string', '', '面向用户的拟人化称呼', '', 1, 0, 0, '', 19, 0, 1, 1398532473, 1398532473, '', 3, '', 'regex', '', 3, 'function'),
(144, 'account_qq', '公众号客服', 'varchar(255) NOT NULL', 'string', '', '建议填写客服QQ,手机号码,链接地址', '', 1, 0, 0, '', 19, 0, 1, 1398571140, 1398571140, '', 3, '', 'regex', '', 3, 'function'),
(148, 'status', '状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:开启\r\n0:关闭', 19, 1, 1, 1398574604, 1398574604, '', 3, '', 'regex', '', 3, 'function'),
(150, 'response_static', 'XML状态', 'tinyint(2) NOT NULL', 'bool', '0', '', '', 1, 0, 0, '1:静态\r\n0:动态', 17, 0, 1, 1398685642, 1398685642, '', 3, '', 'regex', '', 3, 'function'),
(151, 'account_token', 'TOKEN', 'varchar(255) NOT NULL', 'string', '', '任意非中文字符串', '', 1, 0, 0, '', 19, 1, 1, 1399009356, 1399009356, '', 3, '', 'regex', '', 3, 'function'),
(152, 'fromusername', '用户openid', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 20, 1, 1, 1399119909, 1399119909, '', 3, '', 'regex', '', 3, 'function'),
(167, 'cate_group', '用户分组', 'varchar(255) NOT NULL', 'string', 'general', '', '', 1, 0, 0, '', 20, 0, 1, 1399190896, 1399190896, '', 3, '', 'regex', '', 3, 'function'),
(154, 'nickname', '用户昵称', 'varchar(255) NOT NULL', 'string', '游客', '', '', 1, 0, 0, '', 20, 0, 1, 1399120616, 1399120616, '', 3, '', 'regex', '', 3, 'function'),
(155, 'sex', '性别', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:男\r\n0:女', 20, 1, 1, 1399120727, 1399120727, '', 3, '', 'regex', '', 3, 'function'),
(156, 'birthday', '生日日期', 'varchar(20) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399120856, 1399120856, '', 3, '', 'regex', '', 3, 'function'),
(157, 'qq', 'QQ', 'int(14) UNSIGNED NOT NULL', 'num', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399120911, 1399120911, '', 3, '', 'regex', '', 3, 'function'),
(158, 'follow', '关注状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:关注\r\n0:取消', 20, 1, 1, 1399121381, 1399121381, '', 3, '', 'regex', '', 3, 'function'),
(159, 'status', '状态', 'tinyint(2) NOT NULL', 'bool', '1', '', '', 1, 0, 0, '1:开启\r\n0:关闭', 20, 1, 1, 1399121410, 1399121410, '', 3, '', 'regex', '', 3, 'function'),
(160, 'regtime', '注册日期', 'int(13) UNSIGNED NOT NULL', 'num', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399121861, 1399121861, '', 3, '', 'regex', '', 3, 'function'),
(161, 'lasttime', '活动时间', 'int(13) UNSIGNED NOT NULL', 'num', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399121990, 1399121990, '', 3, '', 'regex', '', 3, 'function'),
(162, 'lastkeyword', '上级关键词', 'varchar(150) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399122167, 1399122167, '', 3, '', 'regex', '', 3, 'function'),
(163, 'lastmodel', '上级模块', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399122386, 1399122386, '', 3, '', 'regex', '', 3, 'function'),
(164, 'ucmember', 'UC注册ID', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399122668, 1399122668, '', 3, '', 'regex', '', 3, 'function'),
(165, 'ucpassword', 'Uc密码', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399183291, 1399183272, '', 3, '', 'regex', '', 3, 'function'),
(166, 'ucusername', 'Uc用户名', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399183350, 1399183350, '', 3, '', 'regex', '', 3, 'function'),
(168, 'tagscate_type', '消息类型', 'char(50) NOT NULL', 'select', 'text', '该TAG适用于指定类型的消息', '', 1, 0, 0, 'text:文本消息\r\ndantw:单图文消息\r\nduotw:多图文消息', 8, 1, 1, 1399340436, 1399340436, '', 3, '', 'regex', '', 3, 'function'),
(169, 'lastclick', '个性菜单', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 20, 0, 1, 1399786313, 1399786313, '', 3, '', 'regex', '', 3, 'function'),
(177, 'joinname', '参加名字', 'varchar(255) NOT NULL', 'string', '', '', '联系人', 0, 0, 1, '', 21, 0, 1, 1402930859, 1402643905, '', 3, '', 'regex', '', 3, 'function'),
(178, 'jointel', '联系方式', 'varchar(255) NOT NULL', 'string', '', '', '联系方式', 0, 0, 1, '', 21, 0, 1, 1402930838, 1402643967, '', 3, '', 'regex', '', 3, 'function'),
(179, 'joinextra', '备注说明', 'text NOT NULL', 'textarea', '', '', '备注说明', 0, 0, 1, '', 21, 0, 1, 1402930825, 1402644007, '', 3, '', 'regex', '', 3, 'function'),
(189, 'fromusername', '用户openid', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 23, 1, 1, 1403153982, 1403153982, '', 3, '', 'regex', '', 3, 'function'),
(188, 'joinextra', '备注说明', 'text NOT NULL', 'textarea', '', '', '备注说明', 0, 0, 1, '', 23, 1, 1, 1403863187, 1403153982, '', 3, '', 'regex', '', 3, 'function'),
(187, 'jointel', '联系方式', 'varchar(255) NOT NULL', 'string', '', '', '联系方式', 0, 0, 1, '', 23, 0, 1, 1403153982, 1403153982, '', 3, '', 'regex', '', 3, 'function'),
(186, 'joinname', '参加名字', 'varchar(255) NOT NULL', 'string', '', '', '联系人', 0, 0, 1, '', 23, 0, 1, 1403153982, 1403153982, '', 3, '', 'regex', '', 3, 'function'),
(190, 'pid', '所属ID', 'int(10) unsigned NOT NULL ', 'num', '0', '父文档编号', '', 0, 0, 0, '', 23, 0, 1, 1403153982, 1403153982, '', 0, '', '', '', 0, ''),
(191, 'replylimit', '限制参与人数', 'int(10) UNSIGNED NOT NULL', 'num', '0', '限制前台参与回复的人数(例如:报名人数)', '', 1, 0, 0, '', 1, 0, 1, 1403852300, 1403852300, '', 3, '', 'regex', '', 3, 'function'),
(192, 'replyunique', '回复限制', 'char(50) NOT NULL', 'select', '1', '限制用户回复(唯一:用户多次回复只保存最后一次；无限:多次回复都保存)', '', 1, 0, 0, '1:唯一\r\n0:无限', 1, 0, 1, 1403853443, 1403853443, '', 3, '', 'regex', '', 3, 'function'),
(193, 'location', '所在位置', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 20, 0, 1, 1404724029, 1404724029, '', 3, '', 'regex', '', 3, 'function'),
(194, 'account_sub', '关注链接', 'varchar(255) NOT NULL', 'string', '', '', '', 1, 0, 0, '', 19, 0, 1, 1412329033, 1412328865, '', 3, '', 'regex', '', 3, 'function'),
(195, 'groups', '文章归档', 'varchar(255) NOT NULL', 'string', '', '将文章分组', '', 1, 0, 0, '', 2, 0, 1, 1413607626, 1413607626, '', 3, '', 'regex', '', 3, 'function');

-- --------------------------------------------------------

--
-- 表的结构 `amango_auth_extend`
--

CREATE TABLE IF NOT EXISTS `amango_auth_extend` (
  `group_id` mediumint(10) unsigned NOT NULL COMMENT '用户id',
  `extend_id` mediumint(8) unsigned NOT NULL COMMENT '扩展表中数据的id',
  `type` tinyint(1) unsigned NOT NULL COMMENT '扩展类型标识 1:栏目分类权限;2:模型权限',
  UNIQUE KEY `group_extend_type` (`group_id`,`extend_id`,`type`),
  KEY `uid` (`group_id`),
  KEY `group_id` (`extend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组与分类的对应关系表';

--
-- 转存表中的数据 `amango_auth_extend`
--

INSERT INTO `amango_auth_extend` (`group_id`, `extend_id`, `type`) VALUES
(1, 1, 1),
(1, 1, 2),
(1, 2, 1),
(1, 2, 2),
(1, 3, 1),
(1, 3, 2),
(1, 4, 1),
(1, 37, 1);

-- --------------------------------------------------------

--
-- 表的结构 `amango_auth_group`
--

CREATE TABLE IF NOT EXISTS `amango_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `amango_auth_group`
--

INSERT INTO `amango_auth_group` (`id`, `module`, `type`, `title`, `description`, `status`, `rules`) VALUES
(1, 'admin', 1, '默认用户组', '', 1, '1,2,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,81,82,83,84,86,87,88,89,90,91,92,93,94,95,96,97,100,102,103,105,106'),
(2, 'admin', 1, '测试用户', '测试用户', 1, '1,2,5,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,82,83,84,88,89,90,91,92,93,96,97,100,102,103,195');

-- --------------------------------------------------------

--
-- 表的结构 `amango_auth_group_access`
--

CREATE TABLE IF NOT EXISTS `amango_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `amango_auth_rule`
--

CREATE TABLE IF NOT EXISTS `amango_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-url;2-主菜单',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=252 ;

--
-- 转存表中的数据 `amango_auth_rule`
--

INSERT INTO `amango_auth_rule` (`id`, `module`, `type`, `name`, `title`, `status`, `condition`) VALUES
(1, 'admin', 2, 'Admin/Index/index', '首页', 1, ''),
(2, 'admin', 2, 'Admin/Article/mydocument', '资讯聚合', 1, ''),
(3, 'admin', 2, 'Admin/User/index', '管理成员', 1, ''),
(4, 'admin', 2, 'Admin/Addons/index', '插件聚合', 1, ''),
(5, 'admin', 2, 'Admin/Config/group', '系统设置', 1, ''),
(7, 'admin', 1, 'Admin/article/add', '新增', 1, ''),
(8, 'admin', 1, 'Admin/article/edit', '编辑', 1, ''),
(9, 'admin', 1, 'Admin/article/setStatus', '改变状态', 1, ''),
(10, 'admin', 1, 'Admin/article/update', '保存', 1, ''),
(11, 'admin', 1, 'Admin/article/autoSave', '保存草稿', 1, ''),
(12, 'admin', 1, 'Admin/article/move', '移动', 1, ''),
(13, 'admin', 1, 'Admin/article/copy', '复制', 1, ''),
(14, 'admin', 1, 'Admin/article/paste', '粘贴', 1, ''),
(15, 'admin', 1, 'Admin/article/permit', '还原', 1, ''),
(16, 'admin', 1, 'Admin/article/clear', '清空', 1, ''),
(17, 'admin', 1, 'Admin/article/index', '文档列表', 1, ''),
(18, 'admin', 1, 'Admin/article/recycle', '回收站', 1, ''),
(19, 'admin', 1, 'Admin/User/addaction', '新增用户行为', 1, ''),
(20, 'admin', 1, 'Admin/User/editaction', '编辑用户行为', 1, ''),
(21, 'admin', 1, 'Admin/User/saveAction', '保存用户行为', 1, ''),
(22, 'admin', 1, 'Admin/User/setStatus', '变更行为状态', 1, ''),
(23, 'admin', 1, 'Admin/User/changeStatus?method=forbidUser', '禁用会员', 1, ''),
(24, 'admin', 1, 'Admin/User/changeStatus?method=resumeUser', '启用会员', 1, ''),
(25, 'admin', 1, 'Admin/User/changeStatus?method=deleteUser', '删除会员', 1, ''),
(26, 'admin', 1, 'Admin/User/index', '用户信息', 1, ''),
(27, 'admin', 1, 'Admin/User/action', '用户行为', 1, ''),
(28, 'admin', 1, 'Admin/AuthManager/changeStatus?method=deleteGroup', '删除', 1, ''),
(29, 'admin', 1, 'Admin/AuthManager/changeStatus?method=forbidGroup', '禁用', 1, ''),
(30, 'admin', 1, 'Admin/AuthManager/changeStatus?method=resumeGroup', '恢复', 1, ''),
(31, 'admin', 1, 'Admin/AuthManager/createGroup', '新增', 1, ''),
(32, 'admin', 1, 'Admin/AuthManager/editGroup', '编辑', 1, ''),
(33, 'admin', 1, 'Admin/AuthManager/writeGroup', '保存用户组', 1, ''),
(34, 'admin', 1, 'Admin/AuthManager/group', '授权', 1, ''),
(35, 'admin', 1, 'Admin/AuthManager/access', '访问授权', 1, ''),
(36, 'admin', 1, 'Admin/AuthManager/user', '成员授权', 1, ''),
(37, 'admin', 1, 'Admin/AuthManager/removeFromGroup', '解除授权', 1, ''),
(38, 'admin', 1, 'Admin/AuthManager/addToGroup', '保存成员授权', 1, ''),
(39, 'admin', 1, 'Admin/AuthManager/category', '分类授权', 1, ''),
(40, 'admin', 1, 'Admin/AuthManager/addToCategory', '保存分类授权', 1, ''),
(41, 'admin', 1, 'Admin/AuthManager/index', '权限管理', 1, ''),
(42, 'admin', 1, 'Admin/Addons/create', '创建', 1, ''),
(43, 'admin', 1, 'Admin/Addons/checkForm', '检测创建', 1, ''),
(44, 'admin', 1, 'Admin/Addons/preview', '预览', 1, ''),
(45, 'admin', 1, 'Admin/Addons/build', '快速生成插件', 1, ''),
(46, 'admin', 1, 'Admin/Addons/config', '设置', 1, ''),
(47, 'admin', 1, 'Admin/Addons/disable', '禁用', 1, ''),
(48, 'admin', 1, 'Admin/Addons/enable', '启用', 1, ''),
(49, 'admin', 1, 'Admin/Addons/install', '安装', 1, ''),
(50, 'admin', 1, 'Admin/Addons/uninstall', '卸载', 1, ''),
(51, 'admin', 1, 'Admin/Addons/saveconfig', '更新配置', 1, ''),
(52, 'admin', 1, 'Admin/Addons/adminList', '插件后台列表', 1, ''),
(53, 'admin', 1, 'Admin/Addons/execute', 'URL方式访问插件', 1, ''),
(54, 'admin', 1, 'Admin/Addons/index', '插件管理', 1, ''),
(55, 'admin', 1, 'Admin/Addons/hooks', '钩子管理', 1, ''),
(56, 'admin', 1, 'Admin/model/add', '新增', 1, ''),
(57, 'admin', 1, 'Admin/model/edit', '编辑', 1, ''),
(58, 'admin', 1, 'Admin/model/setStatus', '改变状态', 1, ''),
(59, 'admin', 1, 'Admin/model/update', '保存数据', 1, ''),
(60, 'admin', 1, 'Admin/Model/index', '模型管理', 1, ''),
(61, 'admin', 1, 'Admin/Config/edit', '编辑', 1, ''),
(62, 'admin', 1, 'Admin/Config/del', '删除', 1, ''),
(63, 'admin', 1, 'Admin/Config/add', '新增', 1, ''),
(64, 'admin', 1, 'Admin/Config/save', '保存', 1, ''),
(65, 'admin', 1, 'Admin/Config/group', '网站设置', 1, ''),
(66, 'admin', 1, 'Admin/Config/index', '配置管理', 1, ''),
(67, 'admin', 1, 'Admin/Channel/add', '新增', 1, ''),
(68, 'admin', 1, 'Admin/Channel/edit', '编辑', 1, ''),
(69, 'admin', 1, 'Admin/Channel/del', '删除', 1, ''),
(70, 'admin', 1, 'Admin/Channel/index', '导航管理', 1, ''),
(71, 'admin', 1, 'Admin/Category/edit', '编辑', 1, ''),
(72, 'admin', 1, 'Admin/Category/add', '新增', 1, ''),
(73, 'admin', 1, 'Admin/Category/remove', '删除', 1, ''),
(74, 'admin', 1, 'Admin/Category/index', '分类管理', 1, ''),
(75, 'admin', 1, 'Admin/file/upload', '上传控件', -1, ''),
(76, 'admin', 1, 'Admin/file/uploadPicture', '上传图片', -1, ''),
(77, 'admin', 1, 'Admin/file/download', '下载', -1, ''),
(94, 'admin', 1, 'Admin/AuthManager/modelauth', '模型授权', 1, ''),
(79, 'admin', 1, 'Admin/article/batchOperate', '导入', 1, ''),
(80, 'admin', 1, 'Admin/Database/index?type=export', '备份数据库', 1, ''),
(81, 'admin', 1, 'Admin/Database/index?type=import', '还原数据库', 1, ''),
(82, 'admin', 1, 'Admin/Database/export', '备份', 1, ''),
(83, 'admin', 1, 'Admin/Database/optimize', '优化表', 1, ''),
(84, 'admin', 1, 'Admin/Database/repair', '修复表', 1, ''),
(86, 'admin', 1, 'Admin/Database/import', '恢复', 1, ''),
(87, 'admin', 1, 'Admin/Database/del', '删除', 1, ''),
(88, 'admin', 1, 'Admin/User/add', '新增用户', 1, ''),
(89, 'admin', 1, 'Admin/Attribute/index', '属性管理', 1, ''),
(90, 'admin', 1, 'Admin/Attribute/add', '新增', 1, ''),
(91, 'admin', 1, 'Admin/Attribute/edit', '编辑', 1, ''),
(92, 'admin', 1, 'Admin/Attribute/setStatus', '改变状态', 1, ''),
(93, 'admin', 1, 'Admin/Attribute/update', '保存数据', 1, ''),
(95, 'admin', 1, 'Admin/AuthManager/addToModel', '保存模型授权', 1, ''),
(96, 'admin', 1, 'Admin/Category/move', '移动', -1, ''),
(97, 'admin', 1, 'Admin/Category/merge', '合并', -1, ''),
(98, 'admin', 1, 'Admin/Config/menu', '后台菜单管理', -1, ''),
(99, 'admin', 1, 'Admin/Article/mydocument', '内容', -1, ''),
(100, 'admin', 1, 'Admin/Menu/index', '菜单管理', 1, ''),
(101, 'admin', 1, 'Admin/other', '其他', -1, ''),
(102, 'admin', 1, 'Admin/Menu/add', '新增', 1, ''),
(103, 'admin', 1, 'Admin/Menu/edit', '编辑', 1, ''),
(104, 'admin', 1, 'Admin/Think/lists?model=article', '文章管理', -1, ''),
(105, 'admin', 1, 'Admin/Think/lists?model=download', '下载管理', 1, ''),
(106, 'admin', 1, 'Admin/Think/lists?model=config', '配置管理', 1, ''),
(107, 'admin', 1, 'Admin/Action/actionlog', '行为日志', 1, ''),
(108, 'admin', 1, 'Admin/User/updatePassword', '修改密码', 1, ''),
(109, 'admin', 1, 'Admin/User/updateNickname', '修改昵称', 1, ''),
(110, 'admin', 1, 'Admin/action/edit', '查看行为日志', 1, ''),
(205, 'admin', 1, 'Admin/think/add', '新增数据', 1, ''),
(111, 'admin', 2, 'Admin/article/index', '文档列表', -1, ''),
(112, 'admin', 2, 'Admin/article/add', '新增', -1, ''),
(113, 'admin', 2, 'Admin/article/edit', '编辑', -1, ''),
(114, 'admin', 2, 'Admin/article/setStatus', '改变状态', -1, ''),
(115, 'admin', 2, 'Admin/article/update', '保存', -1, ''),
(116, 'admin', 2, 'Admin/article/autoSave', '保存草稿', -1, ''),
(117, 'admin', 2, 'Admin/article/move', '移动', -1, ''),
(118, 'admin', 2, 'Admin/article/copy', '复制', -1, ''),
(119, 'admin', 2, 'Admin/article/paste', '粘贴', -1, ''),
(120, 'admin', 2, 'Admin/article/batchOperate', '导入', -1, ''),
(121, 'admin', 2, 'Admin/article/recycle', '回收站', -1, ''),
(122, 'admin', 2, 'Admin/article/permit', '还原', -1, ''),
(123, 'admin', 2, 'Admin/article/clear', '清空', -1, ''),
(124, 'admin', 2, 'Admin/User/add', '新增用户', -1, ''),
(125, 'admin', 2, 'Admin/User/action', '用户行为', -1, ''),
(126, 'admin', 2, 'Admin/User/addAction', '新增用户行为', -1, ''),
(127, 'admin', 2, 'Admin/User/editAction', '编辑用户行为', -1, ''),
(128, 'admin', 2, 'Admin/User/saveAction', '保存用户行为', -1, ''),
(129, 'admin', 2, 'Admin/User/setStatus', '变更行为状态', -1, ''),
(130, 'admin', 2, 'Admin/User/changeStatus?method=forbidUser', '禁用会员', -1, ''),
(131, 'admin', 2, 'Admin/User/changeStatus?method=resumeUser', '启用会员', -1, ''),
(132, 'admin', 2, 'Admin/User/changeStatus?method=deleteUser', '删除会员', -1, ''),
(133, 'admin', 2, 'Admin/AuthManager/index', '权限管理', -1, ''),
(134, 'admin', 2, 'Admin/AuthManager/changeStatus?method=deleteGroup', '删除', -1, ''),
(135, 'admin', 2, 'Admin/AuthManager/changeStatus?method=forbidGroup', '禁用', -1, ''),
(136, 'admin', 2, 'Admin/AuthManager/changeStatus?method=resumeGroup', '恢复', -1, ''),
(137, 'admin', 2, 'Admin/AuthManager/createGroup', '新增', -1, ''),
(138, 'admin', 2, 'Admin/AuthManager/editGroup', '编辑', -1, ''),
(139, 'admin', 2, 'Admin/AuthManager/writeGroup', '保存用户组', -1, ''),
(140, 'admin', 2, 'Admin/AuthManager/group', '授权', -1, ''),
(141, 'admin', 2, 'Admin/AuthManager/access', '访问授权', -1, ''),
(142, 'admin', 2, 'Admin/AuthManager/user', '成员授权', -1, ''),
(143, 'admin', 2, 'Admin/AuthManager/removeFromGroup', '解除授权', -1, ''),
(144, 'admin', 2, 'Admin/AuthManager/addToGroup', '保存成员授权', -1, ''),
(145, 'admin', 2, 'Admin/AuthManager/category', '分类授权', -1, ''),
(146, 'admin', 2, 'Admin/AuthManager/addToCategory', '保存分类授权', -1, ''),
(147, 'admin', 2, 'Admin/AuthManager/modelauth', '模型授权', -1, ''),
(148, 'admin', 2, 'Admin/AuthManager/addToModel', '保存模型授权', -1, ''),
(149, 'admin', 2, 'Admin/Addons/create', '创建', -1, ''),
(150, 'admin', 2, 'Admin/Addons/checkForm', '检测创建', -1, ''),
(151, 'admin', 2, 'Admin/Addons/preview', '预览', -1, ''),
(152, 'admin', 2, 'Admin/Addons/build', '快速生成插件', -1, ''),
(153, 'admin', 2, 'Admin/Addons/config', '设置', -1, ''),
(154, 'admin', 2, 'Admin/Addons/disable', '禁用', -1, ''),
(155, 'admin', 2, 'Admin/Addons/enable', '启用', -1, ''),
(156, 'admin', 2, 'Admin/Addons/install', '安装', -1, ''),
(157, 'admin', 2, 'Admin/Addons/uninstall', '卸载', -1, ''),
(158, 'admin', 2, 'Admin/Addons/saveconfig', '更新配置', -1, ''),
(159, 'admin', 2, 'Admin/Addons/adminList', '插件后台列表', -1, ''),
(160, 'admin', 2, 'Admin/Addons/execute', 'URL方式访问插件', -1, ''),
(161, 'admin', 2, 'Admin/Addons/hooks', '钩子管理', -1, ''),
(162, 'admin', 2, 'Admin/Model/index', '模型管理', -1, ''),
(163, 'admin', 2, 'Admin/model/add', '新增', -1, ''),
(164, 'admin', 2, 'Admin/model/edit', '编辑', -1, ''),
(165, 'admin', 2, 'Admin/model/setStatus', '改变状态', -1, ''),
(166, 'admin', 2, 'Admin/model/update', '保存数据', -1, ''),
(167, 'admin', 2, 'Admin/Attribute/index', '属性管理', -1, ''),
(168, 'admin', 2, 'Admin/Attribute/add', '新增', -1, ''),
(169, 'admin', 2, 'Admin/Attribute/edit', '编辑', -1, ''),
(170, 'admin', 2, 'Admin/Attribute/setStatus', '改变状态', -1, ''),
(171, 'admin', 2, 'Admin/Attribute/update', '保存数据', -1, ''),
(172, 'admin', 2, 'Admin/Config/index', '配置管理', -1, ''),
(173, 'admin', 2, 'Admin/Config/edit', '编辑', -1, ''),
(174, 'admin', 2, 'Admin/Config/del', '删除', -1, ''),
(175, 'admin', 2, 'Admin/Config/add', '新增', -1, ''),
(176, 'admin', 2, 'Admin/Config/save', '保存', -1, ''),
(177, 'admin', 2, 'Admin/Menu/index', '菜单管理', -1, ''),
(178, 'admin', 2, 'Admin/Channel/index', '导航管理', -1, ''),
(179, 'admin', 2, 'Admin/Channel/add', '新增', -1, ''),
(180, 'admin', 2, 'Admin/Channel/edit', '编辑', -1, ''),
(181, 'admin', 2, 'Admin/Channel/del', '删除', -1, ''),
(182, 'admin', 2, 'Admin/Category/index', '分类管理', -1, ''),
(183, 'admin', 2, 'Admin/Category/edit', '编辑', -1, ''),
(184, 'admin', 2, 'Admin/Category/add', '新增', -1, ''),
(185, 'admin', 2, 'Admin/Category/remove', '删除', -1, ''),
(186, 'admin', 2, 'Admin/Category/move', '移动', -1, ''),
(187, 'admin', 2, 'Admin/Category/merge', '合并', -1, ''),
(188, 'admin', 2, 'Admin/Database/index?type=export', '备份数据库', -1, ''),
(189, 'admin', 2, 'Admin/Database/export', '备份', -1, ''),
(190, 'admin', 2, 'Admin/Database/optimize', '优化表', -1, ''),
(191, 'admin', 2, 'Admin/Database/repair', '修复表', -1, ''),
(192, 'admin', 2, 'Admin/Database/index?type=import', '还原数据库', -1, ''),
(193, 'admin', 2, 'Admin/Database/import', '恢复', -1, ''),
(194, 'admin', 2, 'Admin/Database/del', '删除', -1, ''),
(195, 'admin', 2, 'Admin/other', '其他', 1, ''),
(196, 'admin', 2, 'Admin/Menu/add', '新增', -1, ''),
(197, 'admin', 2, 'Admin/Menu/edit', '编辑', -1, ''),
(198, 'admin', 2, 'Admin/Think/lists?model=article', '应用', -1, ''),
(199, 'admin', 2, 'Admin/Think/lists?model=download', '下载管理', -1, ''),
(200, 'admin', 2, 'Admin/Think/lists?model=config', '应用', -1, ''),
(201, 'admin', 2, 'Admin/Action/actionlog', '行为日志', -1, ''),
(202, 'admin', 2, 'Admin/User/updatePassword', '修改密码', -1, ''),
(203, 'admin', 2, 'Admin/User/updateNickname', '修改昵称', -1, ''),
(204, 'admin', 2, 'Admin/action/edit', '查看行为日志', -1, ''),
(206, 'admin', 1, 'Admin/think/edit', '编辑数据', 1, ''),
(207, 'admin', 1, 'Admin/Menu/import', '导入', 1, ''),
(208, 'admin', 1, 'Admin/Model/generate', '生成', 1, ''),
(209, 'admin', 1, 'Admin/Addons/addHook', '新增钩子', 1, ''),
(210, 'admin', 1, 'Admin/Addons/edithook', '编辑钩子', 1, ''),
(211, 'admin', 1, 'Admin/Article/sort', '文档排序', 1, ''),
(212, 'admin', 1, 'Admin/Config/sort', '排序', 1, ''),
(213, 'admin', 1, 'Admin/Menu/sort', '排序', 1, ''),
(214, 'admin', 1, 'Admin/Channel/sort', '排序', 1, ''),
(215, 'admin', 1, 'Admin/Category/operate/type/move', '移动', 1, ''),
(216, 'admin', 1, 'Admin/Category/operate/type/merge', '合并', 1, ''),
(217, 'admin', 1, 'Admin/think/lists?model=shopmanage', '商家列表', 1, ''),
(218, 'admin', 1, 'Admin/Shop/cate', '商家分组', 1, ''),
(219, 'admin', 1, 'Admin/Shop/vipcardlists', '会员卡列表', 1, ''),
(220, 'admin', 1, 'Admin/Wxuser/lists', '关注者列表', 1, ''),
(221, 'admin', 1, 'Admin/Think/lists?model=followercate', '关注者分组', 1, ''),
(222, 'admin', 1, 'Admin/Wxuser/action', '微信行为', 1, ''),
(223, 'admin', 1, 'Admin/Wxuser/message', '关注者消息', 1, ''),
(224, 'admin', 1, 'Admin/Flycloud/edit', '数据模型编辑', 1, ''),
(225, 'admin', 1, 'Admin/Flycloud/get_tablefields', '动态显示表字段', 1, ''),
(226, 'admin', 1, 'Admin/Flycloud/add', '添加本地模型', 1, ''),
(227, 'admin', 1, 'Admin/Flycloud/lists', '本地模型列表', 1, ''),
(228, 'admin', 1, 'Admin/Wxuser/add', '添加用户分组', 1, ''),
(229, 'admin', 1, 'Admin/Keywordview/postlists', '请求列表库', 1, ''),
(230, 'admin', 1, 'Admin/Keywordview/responselists', '微信响应库', 1, ''),
(231, 'admin', 1, 'Admin/Keywordview/edit_posts', '编辑用户请求', 1, ''),
(232, 'admin', 1, 'Admin/Think/lists?model=webuntil', '平台接口', 1, ''),
(233, 'admin', 1, 'Admin/Think/lists?model=account', '添加公众号', 1, ''),
(234, 'admin', 1, 'Admin/Keywordview/click_list', '模式列表', 1, ''),
(235, 'admin', 1, 'Admin/Keywordview/click_add', '模式添加', 1, ''),
(236, 'admin', 1, 'Admin/Action/delcache', '清空缓存', 1, ''),
(237, 'admin', 1, 'Admin/Keywordview/default_reply', '默认回复', 1, ''),
(238, 'admin', 1, 'Admin/Keywordview/edit', '系统参数', 1, ''),
(239, 'admin', 2, 'Admin/Shop/lists', '商户聚合', 1, ''),
(240, 'admin', 1, 'Admin/Think/lists?model=tagslists', '标签列表', 1, ''),
(241, 'admin', 2, 'Admin/Wxuser/lists', '关注者', 1, ''),
(242, 'admin', 1, 'Admin/Think/lists?model=tagscate', '标签分组', 1, ''),
(243, 'admin', 2, 'Admin/Keywordview/lists', '关键词', 1, ''),
(244, 'admin', 1, 'Admin/think/lists?model=posts', '请求类型', 1, ''),
(245, 'admin', 1, 'Admin/think/lists?model=rules', '匹配规则', 1, ''),
(246, 'admin', 2, 'Admin/Flycloud/lists', '数据聚合', 1, ''),
(247, 'admin', 1, 'Admin/Keywordview/lists', '关键词列表', 1, ''),
(248, 'admin', 1, 'Admin/think/lists?model=keywordcate', '关键词分组', 1, ''),
(249, 'admin', 1, 'Admin/Keywordview/addkeyword', '关键词添加', 1, ''),
(250, 'admin', 1, 'Admin/Wxuser/edit', '用户编辑', 1, ''),
(251, 'admin', 1, 'Admin/Addons/addonsshop', '芒果应用商店', 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `amango_category`
--

CREATE TABLE IF NOT EXISTS `amango_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `name` varchar(30) NOT NULL COMMENT '标志',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `list_row` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '列表每页行数',
  `meta_title` varchar(50) NOT NULL DEFAULT '' COMMENT 'SEO的网页标题',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `template_index` varchar(100) NOT NULL COMMENT '频道页模板',
  `template_lists` varchar(100) NOT NULL COMMENT '列表页模板',
  `template_detail` varchar(100) NOT NULL COMMENT '详情页模板',
  `template_edit` varchar(100) NOT NULL COMMENT '编辑页模板',
  `model` varchar(100) NOT NULL DEFAULT '' COMMENT '关联模型',
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT '允许发布的内容类型',
  `link_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '外链',
  `allow_publish` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许发布内容',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '可见性',
  `reply` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许回复',
  `reply_show` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `check` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发布的文章是否需要审核',
  `reply_model` varchar(100) NOT NULL DEFAULT '',
  `extend` text NOT NULL COMMENT '扩展设置',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据状态',
  `icon` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类图标',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='分类表' AUTO_INCREMENT=50 ;

--
-- 转存表中的数据 `amango_category`
--

INSERT INTO `amango_category` (`id`, `name`, `title`, `pid`, `sort`, `list_row`, `meta_title`, `keywords`, `description`, `template_index`, `template_lists`, `template_detail`, `template_edit`, `model`, `type`, `link_id`, `allow_publish`, `display`, `reply`, `reply_show`, `check`, `reply_model`, `extend`, `create_time`, `update_time`, `status`, `icon`) VALUES
(1, 'blog', '资讯分类', 0, 0, 10, '', '', '', '', '', '', '', '2', '2', 0, 1, 1, 0, 0, 0, '1', '', 1379474947, 1402304528, 1, 0),
(42, 'news', '芒果公告', 1, 1, 10, '', '', '', '', '', '', '', '2', '1,3', 0, 2, 1, 1, 0, 0, '', '', 1398149314, 1412324471, 1, 0),
(40, 'wxarticle', '芒果日报', 1, 2, 10, '', '', '', '', '', '', '', '2', '2,1,3', 0, 1, 1, 1, 0, 0, '', '', 1398083293, 1412324471, 1, 0),
(43, 'huodon', '活动分类', 0, 0, 10, '', '', '', '', '', '', '', '2', '2,1,3', 0, 1, 1, 1, 0, 0, '', '', 1398164805, 1402304533, 1, 0),
(46, 'schoolhuodon', '校园活动', 43, 0, 10, '校园活动', '', '', '', 'huodonlist', '', '', '21', '2,1,3', 0, 2, 1, 1, 0, 0, '', '', 1402304635, 1407253259, 1, 0),
(48, 'jdhuodon', '精彩活动', 1, 3, 10, '', '', '', '', '', '', '', '2', '1,3', 0, 1, 1, 1, 0, 0, '', '', 1409217013, 1412324472, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `amango_channel`
--

CREATE TABLE IF NOT EXISTS `amango_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '频道ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级频道ID',
  `title` char(30) NOT NULL COMMENT '频道标题',
  `url` char(100) NOT NULL COMMENT '频道连接',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '导航排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `target` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '新窗口打开',
  `iconurl` text,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `amango_channel`
--

INSERT INTO `amango_channel` (`id`, `pid`, `title`, `url`, `sort`, `create_time`, `update_time`, `status`, `target`, `iconurl`) VALUES
(1, 0, '首页', 'Index/index', 1, 1379475111, 1409218083, 0, 1, '/Uploads/Picture/2014-10-15/543e7739ec82e.png'),
(2, 0, '芒果日报', 'Article/lists?category=wxarticle', 2, 1379475131, 1413379900, 1, 0, '/Uploads/Picture/2014-10-15/543e7739ec82e.png'),
(7, 0, '精彩活动', 'Article/lists?category=jdhuodon', 50, 1409218006, 1409218057, 0, 0, '/Uploads/Picture/2014-10-15/543e7739ec82e.png');

-- --------------------------------------------------------

--
-- 表的结构 `amango_clickmenu`
--

CREATE TABLE IF NOT EXISTS `amango_clickmenu` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `postmenu` text NOT NULL,
  `sqlmenu` text NOT NULL,
  `circletime` varchar(255) DEFAULT NULL,
  `update_time` int(11) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `amango_clickmenu`
--

INSERT INTO `amango_clickmenu` (`id`, `title`, `postmenu`, `sqlmenu`, `circletime`, `update_time`, `status`) VALUES
(1, '便捷查询|今日看点|更多精彩', '{"button":[{"name":"便捷查询","sub_button":[{"type":"click","name":"今日课程","key":"LEFT0"},{"type":"click","name":"成绩查询","key":"LEFT1"},{"type":"click","name":"图书借阅","key":"LEFT2"},{"type":"click","name":"餐卡余额","key":"LEFT3"}]},{"name":"今日看点","sub_button":[{"type":"click","name":"芒果日报","key":"CENTER0"},{"type":"click","name":"内测申请","key":"CENTER1"},{"type":"click","name":"调戏小编","key":"CENTER2"}]},{"name":"更多精彩","sub_button":[{"type":"view","name":"百宝袋","url":"http://xit.amango.net/mgbox/"},{"type":"view","name":"帮助","url":"http://mp.weixin.qq.com/s?__biz=MjM5NzI2ODMyOA==&mid=200837515&idx=1&sn=38c64ac8e2c3e1a19ba42a2645c78113#rd"}]}]}', '{"LEFT0":"99","LEFT1":"101","LEFT2":"98","LEFT3":"115","CENTER0":"117","CENTER1":"110","CENTER2":"111"}', '', 1412645786, 0);

-- --------------------------------------------------------

--
-- 表的结构 `amango_config`
--

CREATE TABLE IF NOT EXISTS `amango_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text NOT NULL COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- 转存表中的数据 `amango_config`
--

INSERT INTO `amango_config` (`id`, `name`, `type`, `title`, `group`, `extra`, `remark`, `create_time`, `update_time`, `status`, `value`, `sort`) VALUES
(1, 'WEB_SITE_TITLE', 1, '网站标题', 1, '', '网站标题前台显示标题', 1378898976, 1379235274, 1, '芒果集大', 0),
(2, 'WEB_SITE_DESCRIPTION', 2, '网站描述', 1, '', '网站搜索引擎描述', 1378898976, 1379235841, 1, '芒果，是一个校园生活方式。', 1),
(3, 'WEB_SITE_KEYWORD', 2, '网站关键字', 1, '', '网站搜索引擎关键字', 1378898976, 1381390100, 1, 'Amango.芒果集大', 8),
(4, 'WEB_SITE_CLOSE', 4, '关闭站点', 1, '0:关闭,1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', 1378898976, 1379235296, 1, '1', 1),
(9, 'CONFIG_TYPE_LIST', 3, '配置类型列表', 4, '', '主要用于数据解析和页面表单的生成', 1378898976, 1379235348, 1, '0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举', 2),
(10, 'WEB_SITE_ICP', 1, '网站备案号', 1, '', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2', 1378900335, 1379235859, 1, '', 9),
(11, 'DOCUMENT_POSITION', 3, '文档推荐位', 2, '', '文档推荐位，推荐到多个位置KEY值相加即可', 1379053380, 1379235329, 1, '1:列表页推荐\r\n2:频道页推荐\r\n4:网站首页推荐', 3),
(12, 'DOCUMENT_DISPLAY', 3, '文档可见性', 2, '', '文章可见性仅影响前台显示，后台不收影响', 1379056370, 1379235322, 1, '0:所有人可见\r\n1:仅注册会员可见\r\n2:仅管理员可见', 4),
(13, 'COLOR_STYLE', 4, '后台色系', 1, 'default_color:默认\r\nblue_color:紫罗兰', '后台颜色风格', 1379122533, 1379235904, 1, 'default_color', 10),
(20, 'CONFIG_GROUP_LIST', 3, '配置分组', 4, '', '配置分组', 1379228036, 1384418383, 1, '1:基本\r\n2:内容\r\n3:用户\r\n4:系统\r\n5:微信', 4),
(21, 'HOOKS_TYPE', 3, '钩子的类型', 4, '', '类型 1-用于扩展显示内容，2-用于扩展业务处理', 1379313397, 1379313407, 1, '1:视图\r\n2:控制器', 6),
(22, 'AUTH_CONFIG', 3, 'Auth配置', 4, '', '自定义Auth.class.php类配置', 1379409310, 1379409564, 1, 'AUTH_ON:1\r\nAUTH_TYPE:2', 8),
(23, 'OPEN_DRAFTBOX', 4, '是否开启草稿功能', 2, '0:关闭草稿功能\r\n1:开启草稿功能\r\n', '新增文章时的草稿功能配置', 1379484332, 1379484591, 1, '0', 1),
(24, 'DRAFT_AOTOSAVE_INTERVAL', 0, '自动保存草稿时间', 2, '', '自动保存草稿的时间间隔，单位：秒', 1379484574, 1386143323, 1, '60', 2),
(25, 'LIST_ROWS', 0, '后台每页记录数', 2, '', '后台数据每页显示记录数', 1379503896, 1380427745, 1, '20', 10),
(26, 'USER_ALLOW_REGISTER', 4, '是否允许用户注册', 3, '0:关闭注册\r\n1:允许注册', '是否开放用户注册', 1379504487, 1379504580, 1, '0', 3),
(27, 'CODEMIRROR_THEME', 4, '预览插件的CodeMirror主题', 4, '3024-day:3024 day\r\n3024-night:3024 night\r\nambiance:ambiance\r\nbase16-dark:base16 dark\r\nbase16-light:base16 light\r\nblackboard:blackboard\r\ncobalt:cobalt\r\neclipse:eclipse\r\nelegant:elegant\r\nerlang-dark:erlang-dark\r\nlesser-dark:lesser-dark\r\nmidnight:midnight', '详情见CodeMirror官网', 1379814385, 1384740813, 1, '3024-night', 3),
(28, 'DATA_BACKUP_PATH', 1, '数据库备份根路径', 4, '', '路径必须以 / 结尾', 1381482411, 1381482411, 1, './Data/', 5),
(29, 'DATA_BACKUP_PART_SIZE', 0, '数据库备份卷大小', 4, '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', 1381482488, 1381729564, 1, '20971520', 7),
(30, 'DATA_BACKUP_COMPRESS', 4, '数据库备份文件是否启用压缩', 4, '0:不压缩\r\n1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', 1381713345, 1381729544, 1, '1', 9),
(31, 'DATA_BACKUP_COMPRESS_LEVEL', 4, '数据库备份文件压缩级别', 4, '1:普通\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', 1381713408, 1381713408, 1, '9', 10),
(32, 'DEVELOP_MODE', 4, '开启开发者模式', 4, '0:关闭\r\n1:开启', '是否开启开发者模式', 1383105995, 1383291877, 1, '1', 11),
(33, 'ALLOW_VISIT', 3, '不受限控制器方法', 0, '', '', 1386644047, 1386644741, 1, '0:article/draftbox\r\n1:article/mydocument\r\n2:Category/tree\r\n3:Index/verify\r\n4:file/upload\r\n5:file/download\r\n6:user/updatePassword\r\n7:user/updateNickname\r\n8:user/submitPassword\r\n9:user/submitNickname\r\n10:file/uploadpicture', 0),
(34, 'DENY_VISIT', 3, '超管专限控制器方法', 0, '', '仅超级管理员可访问的控制器方法', 1386644141, 1386644659, 1, '0:Addons/addhook\r\n1:Addons/edithook\r\n2:Addons/delhook\r\n3:Addons/updateHook\r\n4:Admin/getMenus\r\n5:Admin/recordList\r\n6:AuthManager/updateRules\r\n7:AuthManager/tree', 0),
(35, 'REPLY_LIST_ROWS', 0, '回复列表每页条数', 2, '', '', 1386645376, 1387178083, 1, '10', 0),
(36, 'ADMIN_ALLOW_IP', 2, '后台允许访问IP', 4, '', '多个用逗号分隔，如果不配置表示不限制IP访问', 1387165454, 1387165553, 1, '', 12),
(37, 'SHOW_PAGE_TRACE', 4, '是否显示页面Trace', 4, '0:关闭\r\n1:开启', '是否显示页面Trace信息', 1387165685, 1387165685, 1, '0', 1),
(38, 'AMANGO_FACTORY_ADMIN', 3, '关键词回复处理工厂', 5, '', '关键词回复处理工厂,用户控制关键词回复内容创建,工厂的指引', 1397710465, 1398754238, 1, '0:Text\r\n1:Dantw\r\n2:Duotw\r\n3:Api', 0),
(39, 'AMANGO_DEFAULT_REPLY', 1, '微信默认回复', 5, '', '微信默认回复，例如:默认回复、超时回复、关注回复', 1400389718, 1400899549, 1, '129,129,93', 0),
(40, 'WEB_SITE_LOGO', 1, '网站LOGO', 1, '', '请将LOGO放置根目录/Public/logo图片，填写logo图片名称即可,无需填写路径', 1401717600, 1401718271, 1, 'logo.jpg', 0),
(41, 'WEB_SITE_QR', 1, '微信二维码', 1, '', '请将二维码图片放置根目录/Public/二维码图片，填写二维码图片名称即可,无需填写路径', 1401717834, 1401718278, 1, 'qr.jpg', 0);

-- --------------------------------------------------------

--
-- 表的结构 `amango_document`
--

CREATE TABLE IF NOT EXISTS `amango_document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `name` char(40) NOT NULL COMMENT '标识',
  `title` char(80) NOT NULL COMMENT '标题',
  `category_id` int(10) unsigned NOT NULL COMMENT '所属分类',
  `description` char(140) NOT NULL COMMENT '内容摘要',
  `root` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '根节点',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属ID',
  `model_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '内容类型',
  `position` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '推荐位',
  `link_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '外链',
  `cover_id` varchar(255) NOT NULL DEFAULT '0' COMMENT '图文封面',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '可见性',
  `deadline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截止日期',
  `attach` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件数量',
  `view` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `comment` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `extend` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '扩展统计字段',
  `level` int(10) NOT NULL DEFAULT '0' COMMENT '优先级',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '定时发布',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '审核状态',
  `denyuser` varchar(255) NOT NULL COMMENT '黑名单组',
  `replylimit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '限制参与人数',
  `replyunique` char(50) NOT NULL DEFAULT '1' COMMENT '回复限制',
  PRIMARY KEY (`id`),
  KEY `idx_category_status` (`category_id`,`status`),
  KEY `idx_status_type_pid` (`status`,`uid`,`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文档模型基础表' AUTO_INCREMENT=69 ;

--
-- 转存表中的数据 `amango_document`
--

INSERT INTO `amango_document` (`id`, `uid`, `name`, `title`, `category_id`, `description`, `root`, `pid`, `model_id`, `type`, `position`, `link_id`, `cover_id`, `display`, `deadline`, `attach`, `view`, `comment`, `extend`, `level`, `create_time`, `update_time`, `status`, `denyuser`, `replylimit`, `replyunique`) VALUES
(20, 1, '', '欢迎关注芒果集大！', 42, '点击进入了解更多。', 0, 0, 2, 2, 0, 0, '/Uploads/Picture/2014-09-01/54041e6bad033.jpg', 1, 1574920440, 0, 159, 0, 0, 0, 1409550600, 1410342265, 0, '', 0, '1'),
(68, 1, '', '芒果日报上线啦~', 42, '芒果日报上线啦~撒花撒花~❃~❃~❃', 0, 0, 2, 2, 0, 0, 'https://mmbiz.qlogo.cn/mmbiz/TiahI1OlwM0tsCDMoyibRDIIlLJxeBqPhMQVVa5DNPdojUu3OpT3SKYYIbJ5tOa0DrGyyiaUAnbjMh7Ysib7OpCeYg/0', 1, 1569304800, 0, 9, 0, 0, 0, 1412040120, 1412083436, 1, '', 0, '1'),
(67, 1, '', '如何迅速定位某个领域的最佳入门书籍', 40, '工作中或多或少都需要接触一些全新的领域，也不少用户提了些自学上的问题，今天就总结下如何利用互联网工具，快速找到你所要学习领域的最佳入门书籍。', 0, 0, 2, 2, 0, 0, '/Uploads/Picture/2014-09-30/542aa629a037a.jpg', 1, 1569304800, 0, 109, 0, 0, 1, 1412037960, 1412081898, 1, '', 0, '1'),
(60, 1, '', '加入内测群，分享你绝妙的Idea。', 42, 'I want U.', 0, 0, 2, 2, 0, 0, '/Uploads/Picture/2014-09-25/5423c176c65d4.png', 1, 1577514900, 0, 218, 0, 0, 0, 1411585920, 1411721934, 1, '', 0, '1'),
(62, 1, '', '晚安，芒果学长倾情献唱，快戳进来听吧！', 42, '', 0, 0, 2, 2, 0, 0, '/Uploads/Picture/2014-09-27/5426cbdacdfe6.jpg', 1, 1411869840, 0, 565, 0, 0, 0, 1411783440, 1411960609, 1, '', 0, '1'),
(63, 1, '', '获奖名单及一点小风波', 42, '为毛说好的一辆自行车现在两个人中奖？！！ 额，其实，问题全在于那短短的三分钟。', 0, 0, 2, 2, 0, 0, 'https://mmbiz.qlogo.cn/mmbiz/TiahI1OlwM0t5rO6zpVicO2tuALo9fGicqDgjTS59pFG1tQC37hKDYHticT2FXp9Mcyw2Tk0AGwkDBpNCpRlibmicsIQ/0', 1, 1569304800, 0, 19, 0, 0, 0, 1411956420, 1411999701, 1, '', 0, '1'),
(66, 1, '', '陌路的人，你永远不必等', 40, '已经分手了的人，双方都不必再等下去了。', 0, 0, 2, 2, 0, 0, '/Uploads/Picture/2014-09-30/542aa5f744aa2.jpg', 1, 1569304800, 0, 90, 0, 0, 2, 1412037840, 1412081892, 1, '', 0, '1'),
(64, 1, '', '原创漫画 ▏国庆出游，行李箱密码忘了咋办？', 40, '本教程将教你如何徒手破解密码，赶紧来get吧~', 0, 0, 2, 2, 0, 0, '/Uploads/Picture/2014-09-30/542a9f95aba95.jpg', 1, 1569304800, 0, 595, 0, 0, 4, 1412036040, 1412081879, 1, '', 0, '1'),
(65, 1, '', '秋菊男的故事', 40, '十四年前......这是一个真实的故事。', 0, 0, 2, 2, 0, 0, '/Uploads/Picture/2014-09-30/542aa566d9701.jpg', 1, 1569304800, 0, 130, 0, 0, 3, 1412037720, 1412081886, 1, '', 0, '1');

-- --------------------------------------------------------

--
-- 表的结构 `amango_document_article`
--

CREATE TABLE IF NOT EXISTS `amango_document_article` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `parse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容解析类型',
  `content` text NOT NULL COMMENT '文章内容',
  `template` varchar(100) NOT NULL COMMENT '模板风格',
  `bookmark` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  `groups` varchar(255) NOT NULL COMMENT '文章归档',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档模型文章表';

--
-- 转存表中的数据 `amango_document_article`
--

INSERT INTO `amango_document_article` (`id`, `parse`, `content`, `template`, `bookmark`, `groups`) VALUES
(10, 0, '<p>\r\n	【钛媒综合】<span class="wp_keywordlink_affiliate"><a href="http://www.tmtpost.com/tag/wanda" target="_blank">万达</a></span>集团内部8月27日对外表示，<span class="wp_keywordlink_affiliate"><a href="http://www.tmtpost.com/tag/baidu" target="_blank">百度</a></span>、<span class="wp_keywordlink_affiliate"><a href="http://www.tmtpost.com/tag/%e8%85%be%e8%ae%af" target="_blank">腾讯</a></span>以及万达将于本周五共同宣布，成立一家新电子商务公司。\r\n</p>\r\n<p>\r\n	消息称，三方对新电子商务公司的总投资额为人民币50亿元(约合8.10亿美元)。万达持股70%，腾讯和百度各持股15%。管理层和企业结构细节目前还不清楚。\r\n</p>\r\n<p>\r\n	其实早在7月份的时候，万达集团董事长王健林就已经放出豪言称计划豪掷50亿元发展万达<span class="wp_keywordlink_affiliate"><a href="http://www.tmtpost.com/tag/electronic%ef%bc%8dbusiness" target="_blank">电商</a></span>，并将集团所有网上资源全部给电商公司，未来要将万达电商打造成为万达集团的五大业务板块之一。\r\n</p>\r\n<p>\r\n	王健林做出如此大的手笔，下如此大的决心，不禁让人疑惑，万达电商的真正目的是什么？\r\n</p>\r\n<p>\r\n	不妨参考<span class="wp_keywordlink"><a href="http://www.tmtpost.com">钛媒体</a></span>之前的解读：《<a href="http://www.tmtpost.com/123985.html">解剖万达电商：50亿人民币、上亿张卡怎么砸出个O2O</a>》\r\n</p>\r\n<p>\r\n	确实，随着互联网技术已经深入影响并变革传统产业，传统商业巨头纷纷向互联网转型，包括国美、苏宁、银泰百货、万达集团等巨头近年来皆投入巨资进军\r\n互联网，然而所取得的效果却难达预期，目前为止还尚未有比较成功的巨头转型案例。事实上，万达一年多来一直在探索O2O的电商模式，这也是最令王健林头疼\r\n的。\r\n</p>\r\n<p>\r\n	如今探索一种能实现线上线下融合发展的互联网商业模式，已成为包括万达在内的各商业巨头摆在眼前的头等大事。所以万达将携手百度腾讯成立新电商公司的传言，也并不是毫无根据的。\r\n</p>\r\n<p>\r\n	<strong>而百度、腾讯作为此次传言中的合作方，在电商O2O领域，一直与阿里较劲，但却也苦于没大招可用，此次一旦与万达合资成立一家新电商公司，百度、腾讯就是在抱团阻击阿里的节奏啊。</strong> \r\n</p>\r\n<p>\r\n	此外，万达不缺资金，不缺线下资源，从最简单的逻辑上去分析，万达要做电商需要有流可引，必须要有海量的线上用户。所以，万达如果能够通过资金吸纳\r\n百度、腾讯接地气的互联网技术及运营人才，再配合自由宽松的协调机制，是很有机会打造出一个成功的万达电商网络平台，真正实现线上给线下引流，推动万达广\r\n场线下商圈产业的发展。与此同时，百度、腾讯也自然达到了阻击阿里的目的。\r\n</p>\r\n<p>\r\n	不过，针对此传言，百度和万达发言人均不予置评，腾讯则是尚未置评。\r\n</p>', '', 0, ''),
(18, 0, '<p align="left">\r\n	<strong><span style="font-size:32px;">第N届集美大学校园篮球赛</span></strong>\r\n</p>\r\n<p>\r\n	<img src="/Uploads/Editor/2014-08-28/53fec08caa889.jpg" alt="" />\r\n</p>\r\n<p>\r\n	<strong>\r\n	<p>\r\n		<strong>啦啦啦</strong>\r\n	</p>\r\n	<p>\r\n		<strong>啦啦啦</strong>\r\n	</p>\r\n	<p>\r\n		<strong>啦啦啦</strong>\r\n	</p>\r\n	<p>\r\n		<strong>啦啦啦</strong>\r\n	</p>\r\n	<p>\r\n		<strong>啦啦啦</strong>\r\n	</p>\r\n	<p>\r\n		<strong>啦啦啦</strong>\r\n	</p>\r\n	<p>\r\n		<strong>啦啦啦</strong>\r\n	</p>\r\n</strong>\r\n</p>', '', 0, ''),
(19, 0, '<p>\r\n	<span style="font-size:32px;"><strong>首届集美大学阿鲁巴大赛即将火热开展，三位神秘嘉宾将与同学们一同参赛！</strong></span>\r\n</p>\r\n<p>\r\n	<span style="font-size:32px;"><strong><img src="/Uploads/Editor/2014-08-28/53fec15f686dc.jpg" alt="" /></strong></span>\r\n</p>\r\n<p>\r\n	<span style="font-size:32px;"><strong><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><span style="font-size:32px;"><strong>阿鲁巴</strong></span><br />\r\n</strong></span>\r\n</p>', '', 0, ''),
(7, 0, '企鹅去大四大大是', '', 0, ''),
(8, 0, '的方法松岛枫是的发生的', '', 0, ''),
(9, 0, '放松放松', '', 0, ''),
(11, 0, '<p>\r\n	<em>“学弟看过来，学姐请你吃甜甜圈！”，“学妹不要怕，学长请你吃甜甜圈！”对，你没有看错，军训完肚子饿你们就可以过来啦<img src="http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6e/shamea_org.gif" title="[害羞]" alt="[害羞]" />微小协纳新啦～今天万人食堂门口，我们不见不散！<img src="http://img.t.sinajs.cn/t4/appstyle/expression/emimage/ee909f.png" height="20px" width="20px" /></em>\r\n</p>\r\n<p>\r\n	<img src="/Uploads/Editor/2014-08-28/53ff11589b945.jpg" alt="" />\r\n</p>\r\n<p>\r\n	<img src="/Uploads/Editor/2014-08-28/53ff116790bc9.jpg" alt="" />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<a target="_blank" href="http://weibo.com/3443938212/BkmzjmODz?mod=weibotime">http://weibo.com/3443938212/BkmzjmODz?mod=weibotime</a><br />\r\n<em></em>\r\n</p>', '', 0, ''),
(12, 0, '<p style="background:white;">\r\n	<strong><span style="font-size:14px;font-family:微软雅黑, sans-serif;color:#E36C0A;"> 活动简介：</span></strong>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 你看过机械战警吗？</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 一个会走路的机器人，能拐弯，能拿东西，还能无线遥控</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="color:#111111;font-family:微软雅黑, sans-serif;font-size:12px;"> 你想动手给自己做一个这样的机器人吗</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="color:#111111;font-family:微软雅黑, sans-serif;font-size:12px;"> 在一天之内学会机器人的控制和制作</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="color:#111111;font-family:微软雅黑, sans-serif;font-size:12px;"> 不懂编程，不懂电路都没关系，机器人制作比你想象的要简单的多</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 也许我们的简介没有那么华丽，但是我们的机器人可是真的机器人呢</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> <span style="color:#111111;font-family:微软雅黑, sans-serif;font-size:12px;background-color:#FFFFFF;"> 迈向机器人领域的第一步就在火星人俱乐部</span>。</span>\r\n</p>\r\n<p style="background:white;">\r\n	<strong><span style="font-size:14px;font-family:微软雅黑, sans-serif;color:#E36C0A;"> 活动说明：</span></strong>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 本次课程我们只收取材料费，不收取课程费用，做好的东西还能拿回家。</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 我们也欢迎只来听课的同学。</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 【名额】10人</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 【年龄】12岁以上</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 【费用】大机器人 材料费1300元（此为补贴后价格，原2700）&nbsp; 小机器人\r\n材料费650（此为补贴后价格，原1300）</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 【时间】 9月13日14:00-18:30&nbsp;</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 【地点】北京海淀区中关村学院一分院112教室（北京林业大学对面）</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 有任何问题请关注我们的公众号咨询：公众号 huoxing2100</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> &nbsp;</span>\r\n</p>\r\n<p style="background:white;">\r\n	<strong><span style="font-size:14px;font-family:微软雅黑, sans-serif;color:#E36C0A;"> 火星人俱乐部简介</span></strong>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 我们是一群来自北大的学生，我们对物理着迷，希望成为物理帝。</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 我们希望中小学生在这里学习有趣的物理知识，创造智能。</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 我们希望青年在这里扩宽视野，发挥创意。</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 我们希望长者在这里学习手工，增添生活乐趣。</span>\r\n</p>\r\n<p style="background:white;">\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 我们将要形成物联网，让人们生活更智能。&nbsp;</span>\r\n</p>\r\n<p>\r\n	<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"> 火星人智能俱乐部，期待你的到来。</span>\r\n</p>\r\n<p>\r\n	<img src="/Uploads/Editor/2014-08-28/53ff4434c2653.jpg" alt="" /><br />\r\n<span style="font-size:12px;font-family:微软雅黑, sans-serif;color:#111111;"></span>\r\n</p>\r\n<br />', '', 0, ''),
(20, 0, '<p>\r\n	<strong><span style="font-size:16px;">嘿~小伙伴们，芒果集大感谢您的关注！</span></strong><strong><span style="font-size:16px;">向我们发送“</span></strong><img src="/Public/static/kindeditor/plugins/emoticons/images/0.gif" alt=":微笑" /><strong><span style="font-size:16px;">”就可以获得芒果账号咯！</span></strong> \r\n</p>\r\n<p align="center">\r\n	<img src="/Uploads/Editor/2014-09-01/54041d43357ba.png" alt="" height="108" width="115" /> \r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<div>\r\n	芒果集大，集美大学最具潜力的微信公众服务号，提供强大的信息查询、校园资讯、活动通知、互动交友等服务，让芒果成为你的校园生活助手吧！<br />\r\n	<p>\r\n		除了菜单提供的查询服务，您还可以通过发送以下关键词使用更多的服务！\r\n	</p>\r\n<br />\r\n</div>\r\n<p align="left">\r\n	<span style="font-size:14px;line-height:2.5;"><span style="line-height:1.5;">——————————————————<br />\r\n【天气】：发送“城市+天气”<br />\r\n例如：厦门天气。<br />\r\n——————————————————<br />\r\n【快递】：发送“XX快递+单号”<br />\r\n例如：顺丰快递592807974462<br />\r\n——————————————————<br />\r\n【公交】：发送“公交”<br />\r\n——————————————————<br />\r\n【课表】：发送“课表|周一|周二|周三|周四|周五|周六|周日|今天|明天”等关键词，均能查询课表<br />\r\n——————————————————<br />\r\n【馆藏】：发送“馆藏”<br />\r\n——————————————————<br />\r\n【一卡通】：发送“一卡通”<br />\r\n——————————————————<br />\r\n【人品】：发送“人品+姓名”<br />\r\n例如：人品张三<br />\r\n——————————————————<br />\r\n【电影】：发送“电影”</span><span style="line-height:1.5;"></span></span> \r\n</p>\r\n<p align="left">\r\n	<span style="font-size:14px;line-height:2.5;"></span> \r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<strong><span style="font-size:14px;">还有更多好玩的功能正在开发中，芒果的成长需要您的关注与等待~~</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style="font-size:14px;">您的关注是对我们的鼓励，您的</span></strong><strong><span style="font-size:14px;">等待是我们成长的动力！</span></strong> \r\n</p>\r\n<br />', '', 0, ''),
(64, 0, '<p style="text-align:center"><img src="/Uploads/Editor/2014-09-30/542aa23c94c5f.jpg" title="11.jpg"/></p><p style="text-align:center"><img src="/Uploads/Editor/2014-09-30/542aa09600000.jpg" title="2.jpg"/></p><p style="text-align:center"><img src="/Uploads/Editor/2014-09-30/542aa01540d99.jpg" title="3.jpg"/></p><p style="text-align:center"><img src="/Uploads/Editor/2014-09-30/542aa0156ea05.jpg" title="4.jpg"/></p><p style="text-align:center"><img src="/Uploads/Editor/2014-09-30/542aa015b34a7.jpg" title="5.jpg"/></p><p style="text-align:center"><img src="/Uploads/Editor/2014-09-30/542aa01600000.jpg" title="6.jpg"/></p><p style="text-align:center"><img src="/Uploads/Editor/2014-09-30/542aa0167de29.jpg" title="7.jpg"/></p><fieldset class="tn-Powered-by-XIUMI" style="border:0;margin: 0.5em 0;"><section class="tn-Powered-by-XIUMI" style="height: 1em; box-sizing: border-box;"><section class="tn-Powered-by-XIUMI" style="height: 100%; width: 1.5em; float: left; border-top: 0.4em solid rgb(249, 110, 87); border-left: 0.4em solid rgb(249, 110, 87); border-color: rgb(249, 110, 87);"></section><section class="tn-Powered-by-XIUMI" style="height: 100%; width: 1.5em; float: right; border-top: 0.4em solid rgb(249, 110, 87); border-right: 0.4em solid rgb(249, 110, 87); border-color: rgb(249, 110, 87);"></section><section class="tn-Powered-by-XIUMI" style="display: inline-block; color: transparent; clear: both;">test</section></section><section class="tn-Powered-by-XIUMI" style="margin: -0.8em 0.1em -0.8em 0.2em; padding: 0.8em; border: 1px solid rgb(249, 110, 87); border-radius: 0.3em; box-sizing: border-box;"><section class="tn-Powered-by-XIUMI" style="display: block; padding: 0px; margin: 0px; border: medium none; color: rgb(51, 51, 51); font-size: 1em; line-height: 1.4; word-break: break-all; word-wrap: break-word; background: none repeat scroll 0% 0% transparent; text-align: inherit; font-family: inherit; font-style: normal; font-weight: inherit; text-decoration: inherit;">感谢陈呛呛学姐供稿，喜欢的同学可以关注微博<a title="@陈呛呛" target="_blank" href="http://weibo.com/venussssss">@陈呛呛</a></section></section><section class="tn-Powered-by-XIUMI" style="height: 1em; box-sizing: border-box;"><section class="tn-Powered-by-XIUMI" style="height: 100%; width: 1.5em; float: left; border-bottom: 0.4em solid rgb(249, 110, 87); border-left: 0.4em solid rgb(249, 110, 87); border-color: rgb(249, 110, 87);"></section><section class="tn-Powered-by-XIUMI" style="height: 100%; width: 1.5em; float: right; border-bottom: 0.4em solid rgb(249, 110, 87); border-right: 0.4em solid rgb(249, 110, 87); border-color: rgb(249, 110, 87);"></section></section></fieldset>', '', 0, ''),
(65, 0, '<p>十四年前......这是一个真实的故事。十四年前，我在东北老家延吉市的一个外语培训机构学过一段时间的许国璋英语。这是一个韩国人开的私立学校，名字\r\n很土，叫三育。学校的水准很糟糕，国内教师通常是本地的大学或中学教师出来兼职的，外教大都是些口音诡异的菲律宾人和马来西亚人。经常能看到的场面是，一\r\n些学生在“外教口语班”开课后，纷纷赶到前台表示愤怒，工作人员则浓眉大眼地解释说，菲律宾和马来西亚的官方语言确实是英语。有时候，他们还会笨拙地拿出\r\n一本脏乎乎的介绍菲律宾的旅游小册子，“咋还不信呢？自个儿看看吧。”<br/><br/>那时候我刚好失恋，又赶上一个阴冷的冬天，为了缓解负面情绪带来的\r\n压力，我恶学了二十来天英语，在那个初级班结课考试的时候，考了个班里的第一名。按照事先的约定，我去学校领取数额为几百元的奖金（我不记得具体数字了，\r\n好像是三百元）。一个正方形脸蛋的中年韩国校长告诉我说，这个奖金我们不能给你钱，只能从你学习中级班时的学费里减免。我说那叫优惠，或者是打折，不叫奖\r\n金，你们承诺的是给奖金。何况，我也没答应过你们我一定会继续学习你们的中级班。韩国校长说，我们就是为了让你们努力学习才设立的这个奖学金，不是为了让\r\n你们得到钱，你们拿了钱去喝酒抽烟什么的，就违背了我们设立这个奖学金的目的。我说我对你们的目的不感兴趣，我只知道你们说了给奖金就不能在考完了之后改\r\n成优惠打折，至于这个钱我拿到了之后是抽烟喝酒还是大鱼大肉，都跟你们没关系。韩国校长把脸拉成长方形，然后说，年轻人，在我们韩国，你要是对长辈这样没\r\n有礼貌，早就挨打了。<br/><br/>和我无能的前半生的大部分时候一样，我拿这些西装革履的流氓完全没有办法，我不能抑制地又说了脏话，“我操，你们他\r\n \r\n妈的怎么这么流氓啊？” \r\n和那个时代所有受了刺激的“善良市民”一样，我想到了找报社。我怯生生地生平第一次走进报社，在门口登记的时候，我学着从电视里看到的，对门卫说，我是一\r\n个“市民”，我是来“反应情况”的。非常走运的是，接待我的报社记者竟然是我的初中同学，她仔细听了我的“反应情况”之后，充满了想来是因为对老同学热心\r\n所以产生的愤怒。她对我说，我一定彻底揭发揭发他们，下午我就去他们学校采访一下，核实完情况以后，争取几天之内就让它见报。<br/><br/>出了报社的大门想了想，我觉得我还可以再做点什么，于是又去了市教委“反映情况”。一个教委的中年马脸男斜叼着烟，皱着眉头，时不时喝口茶，听了半天后说，好，我们都知道了，你留个联系方式等我们通知你吧。<br/><br/>就像我从他的表情里预感到的那样，这个人始终都没有跟我联系。而且后来我试图再去找他的时候，也被门卫挡在了外面。一周后的坏消息是，延吉晚报的同学告诉我说，这个三育学校是和市教委合作办学的一个机构，延吉晚报是市委办的报纸，因此她写的稿子被总编毙掉了。<br/><br/>我\r\n下了很大的决心，才敢走进延吉市法院。在那之前的一个星期里，我每天都对着自己念叨，“傻逼，你总得有第一次吧”。在一九九五年的中国，我不知道有多少人\r\n像我这样对于第一次尝试用法律保护自己的权益感到兴奋、紧张和好奇，但我想这些跃跃欲试的人里，很多都是受了《秋菊打官司》的影响（无论从哪个角度看，这\r\n部一九九三年红遍全国的电影都是一部了不起的作品）。<br/><br/>在法院的大厅前台，一个胖胖的中年接待男听完了我来的目的之后，直接把我轰到了门\r\n外，“去去去！你这个小同志以为法院是啥地方？！这种鸡毛蒜皮的屁事儿也来捣乱！”我头脑一片空白，在法院门口愣了半天，然后发现法院对面全都是挂着简陋\r\n牌子的律师事务所。我犹豫了一下，还是硬着头皮敲开了其中的一个门，很尴尬地对里面的人表示我没有钱付给他，但是很希望他能给我一些建议。一个笑眯眯的李\r\n姓律师给我耐心讲解了半天，并且对我表示了鼓励和钦佩。在九五年的中国，在人口不到三十万的边陲小城延吉，一个决定用法律手段解决这类问题的小伙子在他看\r\n来，是一个“了不起的年轻人”，“观念很超前啊”，他这样说。当然我也由衷地表示，他肯这样花时间热心地，无偿地帮助一个陌生人，“真是一位了不起的律师\r\n啊”。<br/><br/>两个了不起的中国男人依依惜别后，年轻人重新杀进了法院。按照律师指点的那样，气势汹汹的要求中年接待男，“少啰嗦，给我拿一份表\r\n格（我忘了是叫民事诉讼立案表还是什么）来！”接待男根据这个年轻人的狰狞嘴脸，看出他已经成了一个诉讼常识方面的暴发户，于是乖乖地摸出了一份表格。填\r\n完表格之后，在法院的二楼，一个非常客气但又明显冷漠的女法官接待了我，或者准确地说，是打发了我。她让我到河南（就是把延吉市劈成两半的那条河的南面）\r\n的民事诉讼立案庭（民事调解办公室？）去“试试”，我试图再多请教两句，“你去那边问吧”，她说，接着她又说了中国人都很熟悉的那句公务员用语，“这事儿\r\n不归我们管。”<br/><br/>跟膀大腰圆的市法院不一样，河南的那个民事诉讼立案庭在一栋灰头土脸的二层小楼里。我在一群神情愁苦的乡下群众后面排了将\r\n近三个多小时的队，听到前面的人申述的都是真刀真枪的冤情，比如自己家的地被强占了，比如自己家的媳妇被强占了，比如自己家的地和媳妇一起被强占了。这使\r\n得我在排队的过程中感到越来越没底气，除非我申述的时候他们能给我清场，要不然我实在没有勇气在这样一群不幸的人当中把我那点“鸡毛蒜皮的屁事儿”坦然说\r\n出来。何况，每一个老乡说完之后，立案庭的中年妇女都会用让人彻底绝望的口气重复同一句话，“哎呀，同志，你这个事情很难办啊。”终于，到了还差两个人就\r\n轮到我的时候，我逃离了这个鬼地方。<br/><br/>最后，我想到了游行示威。几乎可以肯定这个选择是受了一些文艺作品的影响，应该是掺杂了一个年轻人在生命的某个阶段产生的自我戏剧化的需要（那时候我还没有接触过这类唬人的名词，我只是模糊地意识到了一些不纯粹的东西）。<br/><br/>初\r\n步设想的方案大概是这样的：我穿着“反映情况”的详情的T恤衫，斜挎着大功率的收录机（口号提前录好），设法把两根竹竿子斜着捆在背上并在脑袋上方用它们\r\n撑起一个较大的口号条幅，比如“倒也谈不上天理难容！”。胸前再挂上一个仪仗队用的鼓就可以上路了。我还可以发动我所有的狐朋狗友们都去远远地围观，免得\r\n真的出现冷场（如果他们不敢和我一起游行的话）。事实上后来他们都兴奋地表示一定会去，至少会去围观。除了对我的做法确实很支持之外，这种事情毕竟也是平\r\n淡生活里难得一见的调剂，这解释了为什么他们在电话里表示要去的时候，夹杂了大量兴奋的，音色失真的“我操！”<br/><br/>计划中的路线是从延边医院\r\n门口出发，放着录音口号、敲着鼓经过市公安局、市委（在市委门口会多待一会儿，可能还应该呼唤马脸男出来对个话什么的，当然，这个比较没有创意^_^）、\r\n州委、州政府，最后到达位于铁南（即铁路之南）的三育学校门口。这个倒霉学校刚好在一条大马路的边上，所以基本上造型醒目的我走到门口后（这时候录音机里\r\n可以短暂地改放一会儿roxette的“look \r\nsharp!”），只要往那儿一杵，就会引起足够的围观了。我想如果我能坚持上一个星期，这个手巴掌大的城市里的所有市民就都该知道这件事了。<br/><br/>我\r\n找来1992年颁布实施的“中华人民共和国xxxxxx法实施条例”简单学习了一下，然后就写了一份书面申请去公安局。窗口的小同志显然没见过来申请游行\r\n示威的，甚至不知道该如何处理，埋头焦虑地不停拨打电话询问。我想了想，就给在市公安局工作的老同学李神探打了个电话，李神探神情凝重地出来把我拽到他的\r\n办公室，“我操，你不想活了？”—— \r\n和所有体制内谋生的人一样，他会把做这类事情直接看成是寻短见。<br/><br/>因为担心劝阻无效，李神探索性就把 \r\n这件事告诉了我的父母，结果可想而知…… \r\n我是说，我的父母和那个时代的绝大多数中国父母没有本质区别（其实我很能理解他们，年轻的时候就能。我只是不同意他们而已）。开春的时候，我和一些朋友包\r\n括我的表哥到一个郊区的网球场去打球。突然，我们看到那个韩国校长也和几个人走进了场地。大家亢奋起来，七嘴八舌地出主意，最后我们决定主动去招惹他，逼\r\n他先发作，然后大伙就围上去群殴。<br/><br/>那时候我还很年轻，也很幼稚，没有意识到这种做法的软弱本质。我只是迟疑了一下，就兴冲冲地跟着大家在\r\n场地外边围成了半个圈子，然后大家一起恶狠狠地看着韩国校长。这小子明显慌了，假装不经意地在场内转来转去。最后，他终于发现，无论转到哪个方向，基本上\r\n都有至少一双兽兽的眼睛盯着他。<br/><br/>大家渐渐按捺不住了，于是开始冲着他做侮辱性的手势。由于不确定一个韩国人是否能看懂，我们很体贴地做了\r\n两个本地传统的，一个美国电影里学的（当然就是竖中指了，那时候这在中国还不太流行），和一个最近刚从俄罗斯流传过来的。这时候韩国校长有些狼狈地朝场边\r\n的长椅处看了一眼，我顺着他的眼光瞄过去，看到一个神色慌张的韩国女人手里拉着两个孩子站了起来。小一点的孩子朝我们这个方向看了一会儿，然后抬头看妈\r\n妈，没有得到反应之后，他拉了一下妈妈的袖子。<br/><br/>即使是在我的道德感相对模糊的青年时代，我也能感觉到，当着一个男人的老婆孩子的面羞辱他，是一件令人非常不安的事情。于是我突然没了兴致，招呼大家走掉了。<br/><br/>在回家的路上，在表哥的车里，在推推搡搡的打闹和七嘴八舌吹牛逼的声音中，我感到巨大的委屈像童年时一样，铺天盖地的压了下来。</p>', '', 0, ''),
(66, 0, '<p>文/沈善书<br/> &nbsp;<br/>已经分手了的人，双方都不必再等下去了。毕竟两人曾经深深爱过，在一起生活有过回忆也是好的。所谓的等待，只是耗费双方的时间互相绊住对方。既然在一起时都不珍惜，分手后再谈等待又有何用。不必等了，缘分自有天意。<br/><br/>2013年8月18日，女生小宇认识了男生大鹏，那一年，小宇23岁，两人都是青岛胶州市人。由于小宇刚和前任男友分手3个月，正处于感情空巢期，所以这时候很容易接受爱情。<br/><br/>小\r\n宇和大鹏是经朋友介绍认识的。彼时，小宇准备买车，和朋友去车展看车的时候认识了男生大鹏。大鹏虽然长相一般，但他很会“扬长避短”，穿衣得体，外形硬\r\n朗，酷爱运动，尤其是打篮球，个头也有一米八。由于男生是女生的销售顾问，给她详细的介绍各种车型，挑完车后，两人又互相留了电话。不过，由于只是萍水相\r\n逢的交情，两人聊的话题无非是与车相关的内容。<br/><br/>有一次，小宇发朋友圈表示想吃水煮鱼，大鹏看见后说请她吃。经过那顿晚餐后，小宇对大鹏印象不错，因为大鹏很幽默，说话也不绕弯，直来直去，聊天内容偶尔“荤素搭配”。也因此，小宇对大鹏愈加倾心。<br/><br/>大\r\n抵是感情空壳吧，小宇慢慢地开始依赖大鹏，只是双方都没表白，就这样一直聊天，内容暧昧，却止于捅破关系这层纸。小宇曾问过大鹏有没有自己喜欢的女孩，大\r\n鹏便发来一张黑色图片说，这个人就是我喜欢的女生。小宇知道大鹏喜欢自己，可是她迟疑的原因是因为她还未能从失恋阴影中走出。<br/><br/>由于大鹏喜欢打篮球，有一次跟外国朋友打比赛时，想邀请小宇为自己助威，然而小宇正在胶州老家的一个小镇上，不方便来。大鹏的性格本来就横冲直撞，竟直接开车去镇上接她。<br/><br/>那\r\n天看完比赛后，大鹏又送小宇回家。在车上的时候，小宇表达了让大鹏如此麻烦接送自己的歉意，而大鹏很坚定的说了这样一句话，他喜欢自己爱的人在旁边看自己\r\n比赛。小宇听后，没有做声。两人都沉默了几分钟，大鹏忽然对小宇说，希望她能接受自己给她不一样的幸福。男生就这样表白，女生恩了一声同意，两人就在一起\r\n了。<br/><br/>然而好景不长，2014年6月，小宇和大鹏决绝分手。<br/><br/>他们分手的原因是前期很多因素累积导致的。比如小宇想要有人陪\r\n的时候，大鹏都在应酬。又比如小宇带大鹏认识自己的亲朋好友，可是大鹏却从未带女生小宇接触自己的朋友圈。再比如男生很喜欢检查女生所有的通讯方式，目的\r\n是担心女生出轨，这让小宇觉得大鹏对待这份感情太过敏感，患得患失。<br/><br/>逛街，看电影，上班应酬，这是大鹏跟小宇在一起后的生活，过得和所有\r\n情侣大同小异。起初虽然平淡，但也有过美好甜蜜的时光。只是时间久了，大鹏的缺点突显。大抵两人都是摩羯座的关系，不仅是工作狂，而且还很爱面子，又喜欢\r\n为琐碎的事情争吵。有一次，小宇半夜打电话给大鹏说自己胃痛，而大鹏却说自己上班很累，让小宇吃药好好休息，未曾想过立马带女生去医院，这让女生觉得男生\r\n不在乎他。<br/><br/>和大鹏在一起久了，小宇总觉得大鹏很少懂过自己，或者说大鹏根本不懂女生的心思。<br/>小宇记得有一次她打电话问大鹏在哪\r\n儿，大鹏说在两人经常吃馄饨的地方跟哥们喝酒，小宇打算去找他，而大鹏却强力推辞，不准小宇去。因为两人之间总是出现小吵小闹，再加上小宇也听别人说大鹏\r\n有家室，而且大鹏也没带小宇见过自己家人并看过身份证，这让她更加怀疑，便开车去找大鹏。可是到了馄饨店后，大鹏根本没在。小宇拍了照片发微信给大鹏，大\r\n鹏这才说自己在家里。<br/>小宇找到大鹏，大鹏试图解释，小宇却执拗要走，大鹏见状竟下跪希望小宇别走，小宇忽地眼泪哗哗流下。她想这么一个爱面子的男生在女生面前下跪，就原谅了他。<br/>两人和好以后，大鹏给小宇的感觉仍是很神秘，小宇仍觉得大鹏还是不懂恋爱，因为有一次大鹏的姐姐给大鹏介绍女朋友，由于大鹏实诚，就和小宇说了，小宇听后很生气，想着两人在一起那么久了大鹏姐姐还不知道有自己的存在。<br/><br/>14年新年前，小宇怀孕了，但双方家人都不知道。小宇问大鹏，大鹏说尊重小宇的意见。小宇试探性的说自己很喜欢宝宝想生下来，大鹏转换看法和小宇说了很多，千言万语无非是希望能拿掉孩子。大鹏的态度让小宇心凉。无奈之下，小宇打掉了孩子。<br/><br/>孩子拿掉后，为了调养好身体，小宇想有人照顾，而大鹏说自己工作忙，抽不出时间陪她，这又加剧了小宇对大鹏不靠谱的印象，又闹了分手。<br/>这时，大鹏才告诉了一个令小宇哭笑不得的秘密，小宇是他的初恋。原来大鹏读书的时候一直没有谈恋爱，高中毕业以后便去当兵了。回来后，忙着事业没心思谈恋爱。后来，又和之前聊过天有家室的女人勾搭的火热，而大鹏认为，这种网恋也属于恋爱。<br/><br/>两人分分合合，小吵小闹，既让大鹏觉得女生不懂事，也让小宇觉得大鹏不会爱。但又如何，小宇爱得大鹏还是太深太深了。<br/><br/>当小宇和大鹏再次闹分手时，一个默默暗恋小宇的男生通过小宇朋友那得知两人闹矛盾要分手了，男生便向小宇求婚。小宇觉得这么多年了男生一直默默关心她，受到男生的感动后，她觉得他值得托付一生，便接受了男生的求婚戒指。<br/>男\r\n生称等办理好自己手上工作问题就带女生去韩国结婚。小宇想着既然要结婚了，就发短信给大鹏。大鹏看见短信，说自己会去婚礼上抢走她。听到大鹏这样说，小宇\r\n再次动摇了。那时候也因为小宇妈妈心脏病犯了，小宇想着自己也只是一时冲动接受了男生的戒指，为了长远考虑，也为了大鹏，小宇把戒指还给了男生。几天后，\r\n她与大鹏又和好了。<br/><br/>14年5月，小宇因工作原因去青岛学习，尽管如此，她也会选择每周回胶州陪大鹏。然而大鹏却莫名其妙的问小宇有没有做\r\n对不起他的事，小宇说没有，大鹏不相信，小宇也彻底爆发了，她哭诉着说大鹏不会爱，不懂得体谅了解女生的心思，因为小宇一直耿耿于怀打胎那件事。她觉得，\r\n如果男人给不起女生未来，就不要轻易的与一个女人发生关系。<br/><br/>这一次，两人狠下心决绝分手。<br/><br/>小宇删除了大鹏所有的联系方式。只是分手一周后，大鹏按捺不住，又加小宇微信，附上验证试图和好。<br/>小宇看着大鹏说和好的请求，心中酸酸的。正当小宇纠结的时候，她忽然发现大鹏的微信账号有点不对劲，因为那一串数字应该是一个人的出生年月，加上大鹏曾经不让小宇见其家人，神神秘秘的感觉让小宇开门见山的问大鹏是不是87年的，大鹏也承认了。<br/>大鹏一直不和小宇坦白的原因是怕小宇觉得两人年龄差距大不跟自己在一起，更怕小宇觉得自己27岁的年龄了才是初恋，怕被笑话，故而隐瞒小宇没让她见其家人。<br/><br/>大鹏27岁才初恋，难怪让小宇觉得和他相处的时候，他总是不懂得关心照顾体谅女生。<br/><br/>姑娘，对于感情，你彷徨、你顾虑、你感性、你多想，是因为你把过客当成了自己的深爱、依靠。姑娘，生命中来来往往的人那么多，有些人只能是朋友，他们是来教会你爱，让你越挫越勇。而你呢，却偏偏喜欢把那些“朋友”当成自己一辈子的归宿。<br/><br/>姑娘，真正值得你依靠一生的人即使你大哭大闹拳打脚踢对方仍旧死皮赖脸的说乖乖我错了，回家我洗碗洗衣跪搓衣板，你别生气了。<br/><br/>今年七夕节那天，小宇收到一束蓝色妖姬，她很惊讶，猜到应是大鹏送的，因为只有他知道，她最喜欢的花是蓝色妖姬。那天，经过反复的思想斗争，小宇发短信给大鹏，问大鹏在干吗。大鹏回复说自己在家。而后小宇发图片给大鹏称自己在吃饭，问他要不要来，大鹏便答应了。<br/>吃饭的时候，大鹏表达了自己想复合的意思。而小宇说两人都还不成熟，就提到了一个约定，希望2015年的七夕节，如果两人都未曾恋爱，仍旧单身，那么就在一起一辈子，永不分离。<br/><br/>现在，小宇仍旧以那个约定麻痹自己，仍旧耽溺回忆。可是，既然两人都已经分手了，又何必以等待这样一件不值得的事阻碍自己的幸福。现在，大鹏和小宇虽然还有联系，但只是寥寥的问你在干嘛，我在忙。好，恩。<br/><br/>那么。<br/><br/>如果相爱的两个人背道而驰，没机会没可能了，那么你就不必再傻傻地等对方。也许你在痴痴等候，而对方却过得欢乐。对于没有缘分已经离开了的人，哭过之后就忘记吧，因为老天不想把不好的送给你，老天要送给你的，是下一份美好的感情。</p>', '', 0, ''),
(60, 0, '<p>\r\n	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;芒果集大刚刚上线<img src="/Public/static/kindeditor/plugins/emoticons/images/21.gif" alt=":愉快" />，功能虽然不多，但是实用，好玩的还在后头呢<img src="/Public/static/kindeditor/plugins/emoticons/images/51.gif" alt=":阴险" />！我们的大牛正在没日没夜地开发各种好玩的功能，希望能给大家带来更多的欢乐。\r\n</p>\r\n<p>\r\n	现在的芒果集大就像小树苗一样，需要大家的呵护，等它长大后大家就可以采摘果实了<img src="/Public/static/kindeditor/plugins/emoticons/images/28.gif" alt=":憨笑" />。\r\n</p>\r\n<p>\r\n	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 怎么呵护他？扫码进群你懂的。\r\n</p>\r\n<p align="center">\r\n	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <img src="/Uploads/Editor/2014-09-25/5423c3a23d090.png" alt="" /> \r\n</p>\r\n<p align="center">\r\n	<strong>&nbsp;</strong><strong>(长按二维码&gt;保存到手机&gt;打开微信"扫一扫"&gt;点击右上角"相册"&gt;即可扫描)</strong> \r\n</p>', '', 0, ''),
(62, 0, '<fieldset class="tn-Powered-by-XIUMI" style="border:0;text-align: right;"><section class="tn-Powered-by-XIUMI" style="display: inline-block; width: 65%; margin-top: 0.7em; margin-right: -0.4em; padding: 1em; background-color: rgb(188, 227, 249); border-radius: 1em; text-align: left;"><section style="font-size: 16px; font-family: inherit; font-style: normal; font-weight: inherit; text-align: inherit; text-decoration: inherit; color: inherit;" class="tn-Powered-by-XIUMI">哈喽大家好，我是芒果学长。下面我准备了一首清唱的歌献给大家，希望大家喜欢，也一起支持芒果集大咯~</section></section><img class="tn-Powered-by-XIUMI" style="vertical-align: top; margin-top: 1.8em; background-color: rgb(188, 227, 249);" src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE093ib7g5EqbD9Clv61dibhn2tYJGuyCsydwem9cQamQkGQWTEtFIHZlgfg/640"/>\r\n &nbsp;<section class="tn-Powered-by-XIUMI" style="display: inline-block; vertical-align: top; width: 40px; height: 40px; border-radius: 40px; background-image: url(&quot;http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0EmGultAbZy6tmUIxPWnw9iaia9Qbm8Bwp9Eribfk1rscovFOkJSD0NOD8M36LkR0dQBrJeBgFTPEhww/640&quot;); background-repeat: no-repeat; background-size: cover;"></section></fieldset><fieldset class="tn-Powered-by-XIUMI" style="border:0;text-align: left; margin: 0.8em 0 0.5em 0; white-space: nowrap; overflow: hidden; box-sizing: border-box!important;"><section class="tn-Powered-by-XIUMI" style="height: 2em; display: inline-block; padding: 0.3em 0.5em; color: white; text-align: center; font-size: 1em; line-height: 1.4; vertical-align: top; box-sizing: border-box ! important; background-color: rgb(255, 202, 0);"><span style="font-size: 1em; font-family: inherit; font-style: normal; font-weight: inherit; text-align: inherit; text-decoration: inherit; color: rgb(0, 0, 0);" class="tn-Powered-by-XIUMI">点击下方播放按钮，开始播放。</span></section><section class="tn-Powered-by-XIUMI" style="height: 2em; width: 0.5em; display: inline-block; vertical-align: top; border-left: 0.5em solid rgb(255, 202, 0); border-top-width: 1em ! important; border-top-style: solid ! important; border-color: rgb(255, 202, 0); border-bottom-width: 1em ! important; border-bottom-style: solid ! important; box-sizing: border-box ! important;"></section></fieldset><p><br/></p><p><audio controls="true"><source src="vedio/20140927_220803.mp3"/></audio></p>', '', 0, '');
INSERT INTO `amango_document_article` (`id`, `parse`, `content`, `template`, `bookmark`, `groups`) VALUES
(63, 0, '<fieldset style="white-space: normal; border: 0px; text-align: center;"><section style="display: inline-block;"><section style="margin: 0.2em 0.5em 0.1em; max-width: 100%; line-height: 1; font-size: 1.8em; font-family: inherit; font-weight: inherit; text-align: inherit; text-decoration: inherit; color: rgb(95, 156, 239);">获奖名单</section><section style="width: 144.78125px; border-top-style: solid; border-top-width: 1px; border-top-color: black; line-height: 1;"></section><section style="margin: 0.5em 1em; line-height: 1; font-size: 1em; font-family: inherit; font-weight: inherit; text-align: inherit; text-decoration: inherit; color: rgb(255, 129, 36);">人品大神</section></section></fieldset><fieldset style="white-space: normal; border: 0px; margin: 0.5em 0px;"><section style="height: 1em; box-sizing: border-box;"><section style="height: 16px; width: 1.5em; float: right; border-top-width: 0.4em; border-top-style: solid; border-right-width: 0.4em; border-right-style: solid; border-color: rgb(95, 156, 239);"></section><section style="display: inline-block; color: transparent; clear: both;">test</section></section><section style="margin: -0.8em 0.1em -0.8em 0.2em; padding: 0.8em; border: 1px solid rgb(95, 156, 239); border-top-left-radius: 0.3em; border-top-right-radius: 0.3em; border-bottom-right-radius: 0.3em; border-bottom-left-radius: 0.3em; box-sizing: border-box;"><section style="padding: 0px; margin: 0px; border: none; color: rgb(51, 51, 51); font-size: 1em; line-height: 1.4; word-break: break-all; word-wrap: break-word; text-align: inherit; font-family: inherit; font-weight: inherit; text-decoration: inherit; background-image: none; background-attachment: initial; background-size: initial; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: initial;"><strong><span style="color: rgb(84, 141, 212);">大大大奖</span></strong><br style="text-align: left;"/>鱼玺 &nbsp;130****0309<br style="text-align: left;"/>吴飞 &nbsp;132****9802<br style="text-align: left;"/><br style="text-align: left;"/> &nbsp;<strong><span style="color: rgb(84, 141, 212);">大大奖</span></strong><br style="text-align: left;"/>张锶* &nbsp;156****6929<br style="text-align: left;"/>黄炜* &nbsp;130****9549<br style="text-align: left;"/>黄筱* &nbsp;180****3385<br style="text-align: left;"/><br style="text-align: left;"/> &nbsp;<strong><span style="color: rgb(84, 141, 212);">大奖</span></strong><br style="text-align: left;"/>中大奖的人太多了就不一一列举啦。<br style="text-align: left;"/></section></section><section style="height: 1em; box-sizing: border-box;"><section style="height: 16px; width: 1.5em; float: left; border-bottom-width: 0.4em; border-bottom-style: solid; border-left-width: 0.4em; border-left-style: solid; border-color: rgb(95, 156, 239);"></section><section style="height: 16px; width: 1.5em; float: right; border-bottom-width: 0.4em; border-bottom-style: solid; border-right-width: 0.4em; border-right-style: solid; border-color: rgb(95, 156, 239);"></section></section></fieldset><fieldset style="white-space: normal; border-width: 0px 0px 1px; display: inline-block; margin: 0.5em 0px; line-height: 1em; border-bottom-style: solid; overflow: hidden; border-color: rgb(95, 156, 239);"><section style="display: inline-block; height: 2.8em; padding: 0.2em; line-height: 1em; background-color: rgb(95, 156, 239);"><section style="font-size: 2.5em; line-height: 1em; font-family: inherit; font-weight: inherit; text-align: inherit; text-decoration: inherit; color: rgb(255, 255, 255);">所以</section></section><section style="display: inline-block; padding: 0.2em; line-height: 1em; font-size: 1.5em; font-family: inherit; font-weight: inherit; text-align: inherit; text-decoration: inherit; color: rgb(249, 110, 87);"><p style="text-align: left;">我就要问了</p></section></fieldset><fieldset style="white-space: normal; border: 0px; margin: 0.8em 0px 0.5em;"><p style="margin-top: 0px; margin-bottom: 0px; font-family: inherit; font-size: 1em; clear: both; font-weight: bold; text-align: left; text-decoration: inherit; color: rgb(95, 156, 239);"><strong style="font-family: inherit; font-size: 1em; text-decoration: inherit;"><span style="color: rgb(84, 141, 212);">挖掘机技术到底哪家强？</span></strong></p><p style="margin-top: 0px; margin-bottom: 0px; font-family: inherit; font-size: 1em; clear: both; font-weight: bold; text-align: left; text-decoration: inherit; color: rgb(95, 156, 239);"><strong style="font-family: inherit; font-size: 1em; text-decoration: inherit;"><span style="color: rgb(84, 141, 212);">NONONO</span></strong></p><p style="margin-top: 0px; margin-bottom: 0px; font-family: inherit; font-size: 1em; clear: both; font-weight: bold; text-align: left; text-decoration: inherit; color: rgb(95, 156, 239);"><strong style="font-family: inherit; font-size: 1em; text-decoration: inherit;"><span style="color: rgb(84, 141, 212);">为毛说好的一辆自行车现在两个人中奖？！！<br style="text-align: left;"/></span></strong></p><p style="margin-top: 0px; margin-bottom: 0px; font-family: inherit; font-size: 1em; clear: both; font-weight: bold; text-align: left; text-decoration: inherit; color: rgb(95, 156, 239);"><strong style="font-family: inherit; font-size: 1em; text-decoration: inherit;"><span style="color: rgb(84, 141, 212);">额，其实，问题全在于那短短的三分钟。</span></strong><br style="text-align: left;"/></p><p style="margin-top: 0px; margin-bottom: 0px; font-family: inherit; font-size: 1em; clear: both; font-weight: bold; text-align: justify; text-decoration: inherit; color: rgb(95, 156, 239);"><br style="text-align: left;"/></p><p style="text-align: left;">三分钟，你能干嘛？</p><p style="text-align: left;">不要告诉小编那是吹一瓶酒的功夫，更不要告诉我那是一泡尿的功夫。</p><p style="text-align:center"><img src="http://mmbiz.qpic.cn/mmbiz/TiahI1OlwM0t5rO6zpVicO2tuALo9fGicqDxNzMNEJayl9IfOLth1jviamz4M9JQLIXvjHwZjxsJ5Let0DRr4siaiayg/640" style="width: auto ! important; visibility: visible ! important; height: auto ! important;" data-s="300,640" data-src="http://mmbiz.qpic.cn/mmbiz/TiahI1OlwM0t5rO6zpVicO2tuALo9fGicqDxNzMNEJayl9IfOLth1jviamz4M9JQLIXvjHwZjxsJ5Let0DRr4siaiayg/0" data-ratio="1.2019230769230769" data-w="208"/></p><p style="text-align: left;">　　既然你丫的不告诉我，那我就告诉你，三分钟，就是2014年9月28日八点的某三分钟，我们芒果集大在短短的三分钟内送出了<strong><span style="color: rgb(247, 150, 70);">两辆</span></strong>自行车。</p><p style="text-align:center"><img src="http://mmbiz.qpic.cn/mmbiz/TiahI1OlwM0t5rO6zpVicO2tuALo9fGicqDIh9VV9RvfEwuY8iaWzmJImZ6YPKgUTdrNBFnribadD3ibhcT9uRxvJicrA/640" style="width: auto ! important; visibility: visible ! important; height: auto ! important;" data-s="300,640" data-src="http://mmbiz.qpic.cn/mmbiz/TiahI1OlwM0t5rO6zpVicO2tuALo9fGicqDIh9VV9RvfEwuY8iaWzmJImZ6YPKgUTdrNBFnribadD3ibhcT9uRxvJicrA/0" data-ratio="1" data-w="220"/></p><p style="text-align: left;">　　问题是小编在去约会前明明记得整个活动只有<strong><span style="color: rgb(247, 150, 70);">一辆</span></strong>自行车啊！</p><p><br style="text-align: left;"/></p><p style="text-align: left;">　　面对这么个情况，起初，小编们带着怀疑的心态祭出好几双24K的钛金眼对两张中奖界面的截图进行最大倍率的观察，直到对着屏幕泪流满面时才明白：</p><p><br style="text-align: left;"/></p><p style="text-align: left;">　　后来，终于在眼泪中才明白，有些事一旦来了就认了吧。</p><p><br style="text-align: left;"/></p><p style="text-align: left;">　　最后经过深入调查，终于发现，原来是由于在第一位中奖同学在填写个人信息时系统仍处于“大大大奖”未被抽走的状态，说时迟那时快，这时第二位同学也抽中了大大大奖。于是，机缘巧合促成了这个小概率的乌龙事件。</p><p><br style="text-align: left;"/></p><p style="text-align: left;">　　说到这，小编也是醉了。</p><p style="text-align:center"><img src="http://mmbiz.qpic.cn/mmbiz/TiahI1OlwM0t5rO6zpVicO2tuALo9fGicqDDMvznC86lhsWJ6blWEPnaSeNazAkun05yw01L2iclQ7wCGUOz6MSiatg/640" style="width: auto ! important; visibility: visible ! important; height: auto ! important;" data-s="300,640" data-src="http://mmbiz.qpic.cn/mmbiz/TiahI1OlwM0t5rO6zpVicO2tuALo9fGicqDDMvznC86lhsWJ6blWEPnaSeNazAkun05yw01L2iclQ7wCGUOz6MSiatg/0" data-ratio="0.5238095238095238" data-w="420"/></p><p style="text-align: left;"><strong>　　</strong>对于此次小概率的乌龙事件，芒果集大全体人员深感抱歉。<strong><span style="color: rgb(84, 141, 212);">两位“大大大奖”得主都会获得我们芒果集大送出的自行车一辆。</span></strong></p><p><br style="text-align: left;"/></p><p style="text-align: left;">　　好了，认也认了，醉也醉了，小编就不跪了，希望大家继续支持芒果集大，继续参与到我们的活动中。</p></fieldset><p><br style="text-align: left;"/></p>', '', 0, ''),
(67, 0, '<p>@洛彦轩：如何最快掌握PS基本知识。</p><p>@阿巨：如何快速学习c++ 等一些学习上的问题。</p><p>　　工作中或多或少都需要接触一些全新的领域，也不少用户提了些自学上的问题，今天就总结下如何利用互联网工具，快速找到你所要学习领域的最佳入门书籍。</p><p>　　从豆瓣讲起，路过知乎、果壳，最后到Mooc、Amazon、Google……希望对你们有帮助。</p><h2><span style="color: rgb(84, 141, 212);">1、豆瓣</span></h2><p>　　豆瓣找书最重要的有四个工具：<span style="color: rgb(227, 108, 9);"><strong>评分</strong>、<strong>豆列</strong>、<strong>书评</strong>、<strong>读书笔记</strong></span>（豆瓣也有图书榜，但不可全信），通过这四个工具综合判断，有几条重要的原则：</p><ul class=" list-paddingleft-2"><li><p><span style="color: rgb(227, 108, 9);"><strong>评分</strong></span>：打分的书一定要出版2年以上，500、1000人以上这个分数的水份才小一点，一般的说，出版不到1年、重印次数还没有的书，也就不用考虑了，特别是豆瓣上现在营销的人越来越多，书托满地……</p></li><li><p><span style="color: rgb(227, 108, 9);"><strong>豆列</strong></span>：\r\n豆瓣用户贡献了大量的时间创造了许多高质量的豆列，像“程序员最应该读的图书”、“各领域入门书籍推荐”等热门豆列，这些豆列一般都有特定的主题，比如经\r\n济学、教育学，心理学等。一般来说，豆列推荐人数&gt;500，关注人数&gt;1000人的质量才比较可靠，如果创建该豆列的人关注人数还能破10k\r\n人次，那可靠度就更高了。豆列旁边有评论的地方，如果某个优质的豆列加入几本特别烂的书，会有很多人留言的，这点需要留意。</p></li><li><p><span style="color: rgb(227, 108, 9);"><strong>书评</strong></span>：\r\n在豆瓣读书频道翻上10几20本书，总能看些一些人重复出现，你可以看看他们写的书评、打分、关注的友邻、写的日记、发的广播、参加的小组，总体确定一下\r\n他们混迹的圈子，如果和你想看的书对的上口的话，在仔细翻翻他们的的豆瓣、博客或者微博，你总能找到一些蛛丝马迹。胆子大点的话，直接发豆邮求推荐吧。</p></li><li><p><span style="color: rgb(227, 108, 9);"><strong>读书笔记</strong></span>：豆列推荐的书单，经典的书读书笔记破1k是很常见的事情，看看读书笔记的内容，是偏专业了，还是通俗的，抓住个认真写笔记的豆友，留言说说你自己的情况，问下这本书的适合群体之类的。</p></li></ul><h2><span style="color: rgb(84, 141, 212);">2、知乎</span></h2><p>　　知乎连接着各行各业的精英，他们分享着彼此的专业知识、经验和见解，年龄层次也相对较高，所以可信度很高。在知乎定位最佳入门书籍方法有<span style="color: rgb(227, 108, 9);"><strong>搜索</strong>、<strong>提问</strong>、<strong>邀请</strong>、<strong>私信</strong></span>：</p><ul class=" list-paddingleft-2"><li><p><span style="color: rgb(227, 108, 9);"><strong>关键词搜索</strong></span>：首先你可以在搜索框输入关键词，找到与你的主题相关帖子，很多得票率高的精华回答下面都有书籍推荐。</p></li><li><p><span style="color: rgb(227, 108, 9);"><strong>提问</strong></span>：在知乎，大部分领域的问题能得到很有效的回答，尤其是互联网知识相关的。这时就需要知道如何提个好问题，首先要认真思考，在搜索完确认别人没提过，这时所提问题要具体，添加相关话题，提交后虚心接受别人修改。这样你才能获得想要的答案。</p></li><li><p><span style="color: rgb(227, 108, 9);"><strong>站内邀请</strong></span>：提完问题，这时要提高问题解决效率，你可以使用右侧的「站内邀请」，邀请专业人士来回答，大部分人都乐于分享。</p></li><li><p><span style="color: rgb(227, 108, 9);"><strong>私信</strong></span>：找到对应主题下的精华回答对应的“最佳回答者”，直接私信询问。比如，如果你想找时间管理这个专业，你会发现“Walfacon”“李开复”“采铜”等人是这个领域的最佳回答者，点击他们的头像，直接私信，放心吧，总有一个会回答你的。</p></li></ul><h2><span style="color: rgb(84, 141, 212);">3、果壳</span></h2><p>　　果壳的Slogan叫：科技，有意思。像是生物、化学、电子类领域，推荐的入门书籍可能会比知乎更加专业一点，毕竟知乎一开始里头都是些互联网的产品经理，可能在计算机、心理学、还有一些社会科学的领域专家比较多。</p><p>　　同样，上面提到的知乎里检索技巧同样适用于果壳，你们可以多逛逛问答、小组频道，两个网站的差别可能多是在用户类型上而已。（特别是“性情”小组）</p><h2><span style="color: rgb(84, 141, 212);">4、Mooc公开课</span></h2><p>　　上MOOC类的网站，比如Coursera,Edx,Udacity等等，或者一些大学公开课程（如MIT courseware,Yale Open Course等)，找到对应学科的入门视频课程。</p><p>　　开这些课程的老师一般都会出一个叫“Syllabus”的东西（类似课程表），从里面能看到一些推荐阅读的书单。因为这些名校的老师都是各自所在领域的权威，他们推荐的书籍基本也是该领域内公认的重要的内容。</p><p>　　Ps:前3条技巧对定位入门中文书籍比较实用，定位英文书籍出了第四条之外，还可通过Amazon书评、Google Books、或者像NY Times books 、Book Review Index这类渠道查找。</p><p><br/></p><p>本文转自/半撇有道儿</p><p><br/></p>', '', 0, ''),
(68, 0, '<fieldset class="tn-Powered-by-XIUMI" style="border: 0px currentColor; border-image: none; text-align: center;"><section class="tn-Powered-by-XIUMI" style="text-align: center; display: inline-block;"><section class="tn-Powered-by-XIUMI" style="margin: 0.2em 0.5em 0.1em; text-align: inherit; color: rgb(95, 156, 239); line-height: 1; font-family: inherit; font-size: 1.8em; font-style: normal; font-weight: inherit; text-decoration: inherit; max-width: 100%;">芒果日报</section><section class="tn-Powered-by-XIUMI" style="width: 100%; line-height: 1; border-top-color: black; border-top-width: 1px; border-top-style: solid;"></section><section class="tn-Powered-by-XIUMI" style="margin: 0.5em 1em; text-align: inherit; color: rgb(255, 129, 36); line-height: 1; font-family: inherit; font-size: 1em; font-style: normal; font-weight: inherit; text-decoration: inherit;">❃上线啦~撒花~❃</section></section></fieldset><fieldset class="tn-Powered-by-XIUMI" style="border: 0px currentColor; border-image: none; text-align: left;"><section class="tn-Powered-by-XIUMI" style="background-position: center; border-radius: 40px; width: 40px; height: 40px; vertical-align: top; display: inline-block; background-image: url(&quot;http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0EmGultAbZy6tmUIxPWnw9iaLEvswCQUUK0k4rPOicLo8SMm0pJOkgsVHg5qDgb0paoeAmW1YTgQRcQ/640&quot;); background-repeat: no-repeat; background-size: cover; -webkit-border-radius: 40px; -moz-border-radius: 40px;"></section><img src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE09eaaY5ibibeWTzWLUe1lytercv3oQYnOebKXHMkpXhYBYPvKhse6ibeY4A/640" class="tn-Powered-by-XIUMI" style="margin-top: 1.8em; vertical-align: top; background-color: rgb(255, 228, 200); width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE09eaaY5ibibeWTzWLUe1lytercv3oQYnOebKXHMkpXhYBYPvKhse6ibeY4A/640" data-w="20" data-ratio="0.85"/><section class="tn-Powered-by-XIUMI" style="padding: 1em; border-radius: 1em; width: 65%; margin-top: 0.7em; display: inline-block; background-color: rgb(255, 228, 200);"><section class="tn-Powered-by-XIUMI" style="text-align: inherit; color: inherit; font-family: inherit; font-size: 1em; font-style: normal; font-weight: inherit; text-decoration: inherit;">什么是芒果日报呀？</section></section></fieldset><fieldset class="tn-Powered-by-XIUMI" style="border: 0px currentColor; border-image: none; text-align: right;"><section class="tn-Powered-by-XIUMI" style="padding: 1em; border-radius: 1em; width: 65%; text-align: left; margin-top: 0.7em; display: inline-block; background-color: rgb(188, 227, 249);"><section class="tn-Powered-by-XIUMI" style="text-align: inherit; color: inherit; font-family: inherit; font-size: 1em; font-style: normal; font-weight: inherit; text-decoration: inherit;">芒果日报简单的说就是芒果学长为大家做的微信杂志啦~</section></section><img src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE093ib7g5EqbD9Clv61dibhn2tYJGuyCsydwem9cQamQkGQWTEtFIHZlgfg/640" class="tn-Powered-by-XIUMI" style="margin-top: 1.8em; vertical-align: top; background-color: rgb(188, 227, 249); width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE093ib7g5EqbD9Clv61dibhn2tYJGuyCsydwem9cQamQkGQWTEtFIHZlgfg/640" data-w="20" data-ratio="0.85"/> &nbsp;<section class="tn-Powered-by-XIUMI" style="background-position: center; border-radius: 40px; width: 40px; height: 40px; vertical-align: top; display: inline-block; background-image: url(&quot;http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0EmGultAbZy6tmUIxPWnw9iaia9Qbm8Bwp9Eribfk1rscovFOkJSD0NOD8M36LkR0dQBrJeBgFTPEhww/640&quot;); background-repeat: no-repeat; background-size: cover; -webkit-border-radius: 40px; -moz-border-radius: 40px;"></section></fieldset><fieldset class="tn-Powered-by-XIUMI" style="border: 0px currentColor; border-image: none; text-align: left;"><section class="tn-Powered-by-XIUMI" style="background-position: center; border-radius: 40px; width: 40px; height: 40px; vertical-align: top; display: inline-block; background-image: url(&quot;http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0EmGultAbZy6tmUIxPWnw9iaLEvswCQUUK0k4rPOicLo8SMm0pJOkgsVHg5qDgb0paoeAmW1YTgQRcQ/640&quot;); background-repeat: no-repeat; background-size: cover; -webkit-border-radius: 40px; -moz-border-radius: 40px;"></section><img src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE09eaaY5ibibeWTzWLUe1lytercv3oQYnOebKXHMkpXhYBYPvKhse6ibeY4A/640" class="tn-Powered-by-XIUMI" style="margin-top: 1.8em; vertical-align: top; background-color: rgb(255, 228, 200); width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE09eaaY5ibibeWTzWLUe1lytercv3oQYnOebKXHMkpXhYBYPvKhse6ibeY4A/640" data-w="20" data-ratio="0.85"/><section class="tn-Powered-by-XIUMI" style="padding: 1em; border-radius: 1em; width: 65%; margin-top: 0.7em; display: inline-block; background-color: rgb(255, 228, 200);"><section class="tn-Powered-by-XIUMI" style="text-align: inherit; color: inherit; font-family: inherit; font-size: 1em; font-style: normal; font-weight: inherit; text-decoration: inherit;">那芒果日报包括哪些内容呢？</section></section></fieldset><fieldset class="tn-Powered-by-XIUMI" style="border: 0px currentColor; border-image: none; text-align: right;"><section class="tn-Powered-by-XIUMI" style="padding: 1em; border-radius: 1em; width: 65%; text-align: left; margin-top: 0.7em; display: inline-block; background-color: rgb(188, 227, 249);"><section class="tn-Powered-by-XIUMI" style="text-align: inherit; color: inherit; font-family: inherit; font-size: 1em; font-style: normal; font-weight: inherit; text-decoration: inherit;">内容很多哦~<br class="tn-Powered-by-XIUMI"/>1.校内校外新鲜事，学习娱乐齐分享。<br class="tn-Powered-by-XIUMI"/>2.江湖趣事神吐槽，社会热点深评论。<br class="tn-Powered-by-XIUMI"/>3.情感职场话人生，工作生活小技能。<br class="tn-Powered-by-XIUMI"/>4.芒果神兽，原创漫画，更多精彩，敬请期待。</section></section><img src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE093ib7g5EqbD9Clv61dibhn2tYJGuyCsydwem9cQamQkGQWTEtFIHZlgfg/640" class="tn-Powered-by-XIUMI" style="margin-top: 1.8em; vertical-align: top; background-color: rgb(188, 227, 249); width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE093ib7g5EqbD9Clv61dibhn2tYJGuyCsydwem9cQamQkGQWTEtFIHZlgfg/640" data-w="20" data-ratio="0.85"/> &nbsp;<section class="tn-Powered-by-XIUMI" style="background-position: center; border-radius: 40px; width: 40px; height: 40px; vertical-align: top; display: inline-block; background-image: url(&quot;http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0EmGultAbZy6tmUIxPWnw9iaia9Qbm8Bwp9Eribfk1rscovFOkJSD0NOD8M36LkR0dQBrJeBgFTPEhww/640&quot;); background-repeat: no-repeat; background-size: cover; -webkit-border-radius: 40px; -moz-border-radius: 40px;"></section></fieldset><fieldset class="tn-Powered-by-XIUMI" style="border: 0px currentColor; border-image: none; text-align: left;"><section class="tn-Powered-by-XIUMI" style="background-position: center; border-radius: 40px; width: 40px; height: 40px; vertical-align: top; display: inline-block; background-image: url(&quot;http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0EmGultAbZy6tmUIxPWnw9iaLEvswCQUUK0k4rPOicLo8SMm0pJOkgsVHg5qDgb0paoeAmW1YTgQRcQ/640&quot;); background-repeat: no-repeat; background-size: cover; -webkit-border-radius: 40px; -moz-border-radius: 40px;"></section><img src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE09eaaY5ibibeWTzWLUe1lytercv3oQYnOebKXHMkpXhYBYPvKhse6ibeY4A/640" class="tn-Powered-by-XIUMI" style="margin-top: 1.8em; vertical-align: top; background-color: rgb(255, 228, 200); width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE09eaaY5ibibeWTzWLUe1lytercv3oQYnOebKXHMkpXhYBYPvKhse6ibeY4A/640" data-w="20" data-ratio="0.85"/><section class="tn-Powered-by-XIUMI" style="padding: 1em; border-radius: 1em; width: 65%; margin-top: 0.7em; display: inline-block; background-color: rgb(255, 228, 200);"><section class="tn-Powered-by-XIUMI" style="text-align: inherit; color: inherit; font-family: inherit; font-size: 1em; font-style: normal; font-weight: inherit; text-decoration: inherit;">那么芒果日报多久出一期呢？</section></section></fieldset><fieldset class="tn-Powered-by-XIUMI" style="border: 0px currentColor; border-image: none; text-align: right;"><section class="tn-Powered-by-XIUMI" style="padding: 1em; border-radius: 1em; width: 65%; text-align: left; margin-top: 0.7em; display: inline-block; background-color: rgb(188, 227, 249);"><section class="tn-Powered-by-XIUMI" style="text-align: inherit; color: inherit; font-family: inherit; font-size: 1em; font-style: normal; font-weight: inherit; text-decoration: inherit;">由于目前芒果学长精力有限，芒果日报2~3天才能出一期哦~往后尽量做到每天一期。</section></section><img src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE093ib7g5EqbD9Clv61dibhn2tYJGuyCsydwem9cQamQkGQWTEtFIHZlgfg/640" class="tn-Powered-by-XIUMI" style="margin-top: 1.8em; vertical-align: top; background-color: rgb(188, 227, 249); width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE093ib7g5EqbD9Clv61dibhn2tYJGuyCsydwem9cQamQkGQWTEtFIHZlgfg/640" data-w="20" data-ratio="0.85"/> &nbsp;<section class="tn-Powered-by-XIUMI" style="background-position: center; border-radius: 40px; width: 40px; height: 40px; vertical-align: top; display: inline-block; background-image: url(&quot;http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0EmGultAbZy6tmUIxPWnw9iaia9Qbm8Bwp9Eribfk1rscovFOkJSD0NOD8M36LkR0dQBrJeBgFTPEhww/640&quot;); background-repeat: no-repeat; background-size: cover; -webkit-border-radius: 40px; -moz-border-radius: 40px;"></section></fieldset><fieldset class="tn-Powered-by-XIUMI" style="border: 0px currentColor; border-image: none; text-align: left;"><section class="tn-Powered-by-XIUMI" style="background-position: center; border-radius: 40px; width: 40px; height: 40px; vertical-align: top; display: inline-block; background-image: url(&quot;http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0EmGultAbZy6tmUIxPWnw9iaLEvswCQUUK0k4rPOicLo8SMm0pJOkgsVHg5qDgb0paoeAmW1YTgQRcQ/640&quot;); background-repeat: no-repeat; background-size: cover; -webkit-border-radius: 40px; -moz-border-radius: 40px;"></section><img src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE09eaaY5ibibeWTzWLUe1lytercv3oQYnOebKXHMkpXhYBYPvKhse6ibeY4A/640" class="tn-Powered-by-XIUMI" style="margin-top: 1.8em; vertical-align: top; background-color: rgb(255, 228, 200); width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE09eaaY5ibibeWTzWLUe1lytercv3oQYnOebKXHMkpXhYBYPvKhse6ibeY4A/640" data-w="20" data-ratio="0.85"/><section class="tn-Powered-by-XIUMI" style="padding: 1em; border-radius: 1em; width: 65%; margin-top: 0.7em; display: inline-block; background-color: rgb(255, 228, 200);"><section class="tn-Powered-by-XIUMI" style="text-align: inherit; color: inherit; font-family: inherit; font-size: 1em; font-style: normal; font-weight: inherit; text-decoration: inherit;">那我要怎么样才能看到芒果日报呢？</section></section></fieldset><fieldset class="tn-Powered-by-XIUMI" style="border: 0px currentColor; border-image: none; text-align: right;"><section class="tn-Powered-by-XIUMI" style="padding: 1em; border-radius: 1em; width: 65%; text-align: left; margin-top: 0.7em; display: inline-block; background-color: rgb(188, 227, 249);"><section class="tn-Powered-by-XIUMI" style="text-align: inherit; color: inherit; font-family: inherit; font-size: 1em; font-style: normal; font-weight: inherit; text-decoration: inherit;">由于服务号每个月只能向大家推送4条消息，所以需要大家点击芒果集大的底部菜单中的“芒果日报”来获取哦~</section></section><img src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE093ib7g5EqbD9Clv61dibhn2tYJGuyCsydwem9cQamQkGQWTEtFIHZlgfg/640" class="tn-Powered-by-XIUMI" style="margin-top: 1.8em; vertical-align: top; background-color: rgb(188, 227, 249); width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0HFAP2f2XKYibcoIIfH3FE093ib7g5EqbD9Clv61dibhn2tYJGuyCsydwem9cQamQkGQWTEtFIHZlgfg/640" data-w="20" data-ratio="0.85"/> &nbsp;<section class="tn-Powered-by-XIUMI" style="background-position: center; border-radius: 40px; width: 40px; height: 40px; vertical-align: top; display: inline-block; background-image: url(&quot;http://mmbiz.qpic.cn/mmbiz/MVPvEL7Qg0EmGultAbZy6tmUIxPWnw9iaia9Qbm8Bwp9Eribfk1rscovFOkJSD0NOD8M36LkR0dQBrJeBgFTPEhww/640&quot;); background-repeat: no-repeat; background-size: cover; -webkit-border-radius: 40px; -moz-border-radius: 40px;"></section></fieldset><p><br/></p><p style="text-align: center;"><img src="http://mmbiz.qpic.cn/mmbiz/TiahI1OlwM0tsCDMoyibRDIIlLJxeBqPhMLWtiaZiaP1O11O1O4sGKIialMXOqSRtwnls5iaozyuXhmlNPDgJzXIY8zQ/640" style="width: auto ! important; visibility: visible ! important; height: auto ! important;" data-src="http://mmbiz.qpic.cn/mmbiz/TiahI1OlwM0tsCDMoyibRDIIlLJxeBqPhMLWtiaZiaP1O11O1O4sGKIialMXOqSRtwnls5iaozyuXhmlNPDgJzXIY8zQ/0" data-w="" data-ratio="0.5603960396039604" data-s="300,640"/></p><p>　　各位小伙伴，抽奖活动已经结束啦~大家不要再发送抽奖啦，要不发送“<strong><span style="color: rgb(84, 141, 212);">芒果日报</span></strong>”试试看？</p><p>　　芒果学长很快就会给大家带来新活动啦~敬请期待哈。</p><p><br/></p>', '', 0, '');

-- --------------------------------------------------------

--
-- 表的结构 `amango_document_download`
--

CREATE TABLE IF NOT EXISTS `amango_document_download` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `parse` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容解析类型',
  `content` text NOT NULL COMMENT '下载详细描述',
  `template` varchar(100) NOT NULL DEFAULT '' COMMENT '详情页显示模板',
  `file_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件ID',
  `download` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档模型下载表';

-- --------------------------------------------------------

--
-- 表的结构 `amango_document_huodon`
--

CREATE TABLE IF NOT EXISTS `amango_document_huodon` (
  `id` int(10) unsigned NOT NULL COMMENT '主键',
  `huodondesc` text NOT NULL COMMENT '活动介绍',
  `huodonchenban` varchar(255) NOT NULL COMMENT '承办方',
  `huodonjuban` text NOT NULL COMMENT '主办方',
  `huodonaddress` varchar(255) NOT NULL COMMENT '举办地点',
  `huodonextra` text NOT NULL COMMENT '限制说明',
  `joinname` varchar(255) NOT NULL COMMENT '参加名字',
  `jointel` varchar(255) NOT NULL COMMENT '联系方式',
  `joinextra` text NOT NULL COMMENT '备注说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `amango_document_huodon`
--

INSERT INTO `amango_document_huodon` (`id`, `huodondesc`, `huodonchenban`, `huodonjuban`, `huodonaddress`, `huodonextra`, `joinname`, `jointel`, `joinextra`) VALUES
(8, 'dadada', 'dadada', '陈登陆', 'dadad', '11', '', '', ''),
(9, '大大', '策划的', '策划的', '大大', '11', '', '', ''),
(15, '三人篮球赛三人篮球赛三人篮球赛三人篮球赛三人篮球赛三人篮球赛三人篮球赛三人篮球赛', '集美大学', '集美大学', '集美大学体育馆', '90', '', '', ''),
(16, '假面舞会假面舞会假面舞会假面舞会假面舞会假面舞会', '集美大学', '集美大学', '集美大学体育馆', '90', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `amango_file`
--

CREATE TABLE IF NOT EXISTS `amango_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `savename` char(20) NOT NULL DEFAULT '' COMMENT '保存名称',
  `savepath` char(30) NOT NULL DEFAULT '' COMMENT '文件保存路径',
  `ext` char(5) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mime` char(40) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `location` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件保存位置',
  `create_time` int(10) unsigned NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_md5` (`md5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_flycloud`
--

CREATE TABLE IF NOT EXISTS `amango_flycloud` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `data_name` varchar(255) NOT NULL COMMENT '模型名称',
  `data_type` varchar(20) NOT NULL DEFAULT 'local' COMMENT '模型类型',
  `data_table` varchar(100) NOT NULL COMMENT '数据表名',
  `data_fields` text NOT NULL COMMENT '读取字段',
  `data_condition` text NOT NULL COMMENT '读取条件',
  `status` tinyint(2) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=18 ;

--
-- 转存表中的数据 `amango_flycloud`
--

INSERT INTO `amango_flycloud` (`id`, `data_name`, `data_type`, `data_table`, `data_fields`, `data_condition`, `status`) VALUES
(1, '调用【TAG分组】', 'local', '8', 'tagscate_title,tagscate_name', '', 1),
(2, '调用【用请求类型】', 'local', '7', 'posts_title,posts_name', '', 1),
(3, '调用【会员组】', 'local', '13', 'followercate_title,followercate_name', '', 1),
(4, '调用【匹配规则】', 'local', '6', 'rules_title,rules_name', '', 1),
(5, '调用【关键词分组】', 'local', '11', 'keywordcate_denyuser,keywordcate_name', '', 1),
(7, '调用【响应内容】', 'local', '17', 'response_name', '', 1),
(11, '调用【关键词列表】', 'local', '5', 'keyword_rules', '', 1),
(12, '调用【芒果日报】', 'category', '40', 'title,cover_id,description', '', 1),
(15, '调用【精彩活动】', 'category', '48', 'title,cover_id,description', '', 1),
(17, '调用【系统公告】', 'category', '42', 'title,cover_id,description', '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `amango_followercate`
--

CREATE TABLE IF NOT EXISTS `amango_followercate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `followercate_title` varchar(50) NOT NULL COMMENT '会员组标识',
  `followercate_name` varchar(100) NOT NULL COMMENT '会员组名',
  `followercate_des` text NOT NULL COMMENT '会员组特权说明',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `amango_followercate`
--

INSERT INTO `amango_followercate` (`id`, `followercate_title`, `followercate_name`, `followercate_des`, `status`) VALUES
(1, 'general', '普通用户', '', 1),
(2, 'admin', '管理员组', '', 1),
(3, 'vip1', 'VIP1级', '', 1),
(4, 'black', '黑名单', '该分组下的用户无法获得正常激活权限', 1);

-- --------------------------------------------------------

--
-- 表的结构 `amango_hooks`
--

CREATE TABLE IF NOT EXISTS `amango_hooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text NOT NULL COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addons` varchar(255) NOT NULL DEFAULT '' COMMENT '钩子挂载的插件 ''，''分割',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- 转存表中的数据 `amango_hooks`
--

INSERT INTO `amango_hooks` (`id`, `name`, `description`, `type`, `update_time`, `addons`) VALUES
(1, 'pageHeader', '页面header钩子，一般用于加载插件CSS文件和代码', 1, 0, ''),
(2, 'pageFooter', '页面footer钩子，一般用于加载插件JS文件和JS代码', 1, 0, ''),
(3, 'documentEditForm', '添加编辑表单的 扩展内容钩子', 1, 0, 'Attachment'),
(4, 'documentDetailAfter', '文档末尾显示', 1, 0, 'Attachment,SocialComment'),
(5, 'documentDetailBefore', '页面内容前显示用钩子', 1, 0, ''),
(6, 'documentSaveComplete', '保存文档数据后的扩展钩子', 2, 0, 'Attachment'),
(7, 'documentEditFormContent', '添加编辑表单的内容显示钩子', 1, 0, 'Editor'),
(8, 'adminArticleEdit', '后台内容编辑页编辑器', 1, 1378982734, 'EditorForAdmin'),
(13, 'AdminIndex', '首页小格子个性化显示', 1, 1382596073, 'DevTeam'),
(14, 'topicComment', '评论提交方式扩展钩子。', 1, 1380163518, 'Editor'),
(16, 'app_begin', '应用开始', 2, 1384481614, ''),
(17, 'amango', '芒果默认钩子', 2, 1400305945, 'Excelimport,SnatchTieba,Toolbox,Neighbours,Sharephotos,Xitedu,Placement');

-- --------------------------------------------------------

--
-- 表的结构 `amango_keyword`
--

CREATE TABLE IF NOT EXISTS `amango_keyword` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword_start` int(10) NOT NULL COMMENT '开始日期',
  `keyword_end` int(10) unsigned NOT NULL COMMENT '截止日期',
  `keyword_click` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '激活次数',
  `keyword_top` int(10) unsigned NOT NULL COMMENT '上文词汇',
  `keyword_down` tinyint(2) NOT NULL DEFAULT '1' COMMENT '下文继承',
  `keyword_group` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '关键词组',
  `keyword_cache` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缓存时间',
  `keyword_post` varchar(100) NOT NULL COMMENT '请求类型',
  `keyword_content` text NOT NULL COMMENT '关键词内容',
  `keyword_rules` text NOT NULL COMMENT '匹配语句',
  `denytag_keyword` text NOT NULL COMMENT '禁止标识',
  `before_keyword` text NOT NULL COMMENT '激活前操作',
  `after_keyword` text NOT NULL COMMENT '激活后操作',
  `click_model` varchar(255) NOT NULL COMMENT '菜单模式',
  `lock_model` varchar(255) NOT NULL COMMENT '锁定模块',
  `keyword_reaponse` varchar(255) NOT NULL COMMENT '响应体ID',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `keyword_reply` varchar(100) NOT NULL COMMENT '匹配规则ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=124 ;

--
-- 转存表中的数据 `amango_keyword`
--

INSERT INTO `amango_keyword` (`id`, `keyword_start`, `keyword_end`, `keyword_click`, `keyword_top`, `keyword_down`, `keyword_group`, `keyword_cache`, `keyword_post`, `keyword_content`, `keyword_rules`, `denytag_keyword`, `before_keyword`, `after_keyword`, `click_model`, `lock_model`, `keyword_reaponse`, `status`, `sort`, `keyword_reply`) VALUES
(105, 1411096380, 1537240380, 15, 0, 1, 1, 0, 'text', '图文测试', '/^图文测试$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '122', 1, 1411139737, 'equel'),
(118, 1412630580, 1538774580, 2, 0, 1, 1, 0, 'text', '天气', '/^天气$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '120', 1, 1412673842, 'equel'),
(26, 1408329180, 1534473180, 57, 0, 1, 1, 0, 'text', '隔壁', '/(隔壁)/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '42', 0, 1408329236, 'anywhere'),
(95, 1411011240, 1537155240, 6, 0, 1, 5, 0, 'text', '馆藏', '/^馆藏$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '115', 1, 1411054482, 'equel'),
(88, 1410226320, 1536370320, 26, 0, 1, 1, 0, 'image', '', '', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '107', 1, 1410269606, 'equel'),
(87, 1410225960, 1536369960, 37, 0, 1, 1, 0, 'text', '图吧', '/^图吧$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '107', 1, 1410269244, 'equel'),
(109, 1411584480, 1537728480, 1, 0, 1, 1, 0, 'text', '啦啦啦 啦啦啦 啦啦啦啦啦', '/^啦啦啦 啦啦啦 啦啦啦啦啦$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '126', 1, 1411628156, 'equel'),
(75, 1409345760, 1535489760, 4, 0, 1, 0, 0, 'text', '百宝箱', '/^百宝箱$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '71', 1, 1409388986, 'equel'),
(74, 1409345580, 1535489580, 11, 0, 1, 1, 0, 'text', '贴吧', '/^贴吧$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '41', 1, 1409388825, 'equel'),
(89, 1410226620, 1536370620, 0, 0, 1, 1, 0, 'text', '图吧', '/^图吧$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '', 1, 1410269849, 'equel'),
(108, 1409714160, 1537586160, 2567, 0, 1, 4, 0, 'text', '抽奖', '/^抽奖$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '134', 1, 1411442193, 'equel'),
(101, 1411097640, 1537241640, 5, 0, 1, 5, 0, 'text', '成绩', '/^成绩$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '117', 1, 1411097702, 'equel'),
(119, 1412889840, 1539033840, 2, 0, 1, 1, 0, 'text', '图片测试', '/^图片测试$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '136', 1, 1412933082, 'equel'),
(44, 1408998420, 1535142420, 32, 0, 1, 4, 0, 'text', '快递|单号', '/(快递|单号)/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '94', 1, 1409041676, 'anywhere'),
(45, 1408999200, 1535143200, 5, 0, 1, 4, 0, 'text', '人品', '/^人品/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '63', 1, 1409042435, 'top'),
(48, 1408999800, 1535143800, 33, 0, 1, 1, 0, 'text', '公交', '/^公交$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '67', 1, 1409043133, 'equel'),
(51, 1409022300, 1545882900, 15, 0, 1, 1, 0, 'text', '电影', '/(电影)/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '70', 1, 1409065638, 'anywhere'),
(52, 1409024160, 1535168160, 10, 0, 1, 1, 0, 'text', '百宝袋', '/^百宝袋$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '71', 1, 1409067465, 'equel'),
(107, 1411274460, 1537418460, 0, 0, 1, 1, 0, 'text', '送', '/^送$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '124', 1, 1411231323, 'equel'),
(98, 1411011900, 1537155900, 121, 0, 1, 5, 0, 'text', '借阅', '/^借阅/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '119', 1, 1411055145, 'top'),
(99, 1411097460, 1537241460, 39, 0, 1, 5, 0, 'text', '课表|周一|周二|周三|周四|周五|周六|周日|今天|明天', '/^课表|周一|周二|周三|周四|周五|周六|周日|今天|明天$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '116', 1, 1411097509, 'equel'),
(106, 1411272480, 1537416480, 2, 0, 1, 1, 0, 'text', '日历', '/^日历$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '123', 1, 1411229338, 'equel'),
(72, 1409337540, 1535481540, 16, 0, 1, 3, 0, 'text', '看点', '/^看点$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '87', 1, 1409380827, 'equel'),
(73, 1409338620, 1535482620, 3, 0, 1, 3, 0, 'text', '活动', '/^活动$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '88', 1, 1409381882, 'equel'),
(110, 1411586880, 1537730880, 2, 0, 1, 1, 0, 'text', '内测', '/^内测$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '127', 1, 1411630115, 'equel'),
(91, 1410296220, 1536440220, 18, 0, 1, 5, 0, 'text', '一卡通', '/^一卡通$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '111', 1, 1410339659, 'equel'),
(111, 1411587180, 1537731180, 0, 0, 1, 1, 0, 'text', '调戏小编', '/^调戏小编$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '128', 1, 1411630573, 'equel'),
(114, 1411785600, 1537929600, 283, 0, 1, 1, 0, 'text', '晚安', '/^晚安/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '131', 1, 1411828872, 'top'),
(115, 1411751100, 1538021880, 77, 0, 1, 5, 0, 'text', '余额', '/^余额/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '132', 1, 1411834728, 'top'),
(117, 1411963800, 1538194440, 16, 0, 1, 1, 0, 'text', '芒果日报', '/^芒果日报$/', 'a:1:{i:0;s:0:"";}', '', 'a:2:{i:0;s:12:"Keywordcount";i:1;s:0:"";}', '0', '', '135', 1, 1412007617, 'equel'),
(120, 1413170220, 1539314220, 1, 0, 1, 1, 0, 'text', '芒果日报TAG', '/^芒果日报TAG$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '137', 1, 1413170400, 'equel'),
(122, 1413340200, 1539484200, 4, 0, 1, 1, 0, 'text', '测试', '/^测试$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '139', 1, 1413384805, 'equel'),
(123, 1413606060, 1539750060, 1, 0, 1, 1, 0, 'text', '测试', '/^测试$/', 'a:1:{i:0;s:0:"";}', '', 'a:1:{i:0;s:12:"Keywordcount";}', '0', '', '140', 1, 1413606295, 'equel');

-- --------------------------------------------------------

--
-- 表的结构 `amango_keywordcate`
--

CREATE TABLE IF NOT EXISTS `amango_keywordcate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keywordcate_name` varchar(150) NOT NULL COMMENT '关键词分组名',
  `keywordcate_denyuser` varchar(255) NOT NULL COMMENT '黑名单组',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `amango_keywordcate`
--

INSERT INTO `amango_keywordcate` (`id`, `keywordcate_name`, `keywordcate_denyuser`, `status`) VALUES
(1, '系统关键词', '', 1),
(2, '管理员', 'general,vip1,black', 1),
(6, 'TAG植入', 'black', 1),
(4, '第三方调用', 'black', 1),
(5, '学院特色', 'black', 1);

-- --------------------------------------------------------

--
-- 表的结构 `amango_member`
--

CREATE TABLE IF NOT EXISTS `amango_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员状态',
  PRIMARY KEY (`uid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_menu`
--

CREATE TABLE IF NOT EXISTS `amango_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=165 ;

--
-- 转存表中的数据 `amango_menu`
--

INSERT INTO `amango_menu` (`id`, `title`, `pid`, `sort`, `url`, `hide`, `tip`, `group`, `is_dev`) VALUES
(1, '首页', 0, 1, 'Index/index', 0, '', '', 0),
(2, '资讯聚合', 0, 2, 'Article/mydocument', 0, '', '', 0),
(3, '文档列表', 2, 0, 'article/index', 1, '', '内容', 0),
(4, '新增', 3, 0, 'article/add', 0, '', '', 0),
(5, '编辑', 3, 0, 'article/edit', 0, '', '', 0),
(6, '改变状态', 3, 0, 'article/setStatus', 0, '', '', 0),
(7, '保存', 3, 0, 'article/update', 0, '', '', 0),
(8, '保存草稿', 3, 0, 'article/autoSave', 0, '', '', 0),
(9, '移动', 3, 0, 'article/move', 0, '', '', 0),
(10, '复制', 3, 0, 'article/copy', 0, '', '', 0),
(11, '粘贴', 3, 0, 'article/paste', 0, '', '', 0),
(12, '导入', 3, 0, 'article/batchOperate', 0, '', '', 0),
(13, '回收站', 2, 0, 'article/recycle', 1, '', '内容', 0),
(14, '还原', 13, 0, 'article/permit', 0, '', '', 0),
(15, '清空', 13, 0, 'article/clear', 0, '', '', 0),
(16, '管理成员', 0, 9, 'User/index', 0, '', '', 0),
(17, '用户信息', 16, 0, 'User/index', 0, '', '用户管理', 0),
(18, '新增用户', 17, 0, 'User/add', 0, '添加新用户', '', 0),
(19, '用户行为', 16, 0, 'User/action', 0, '', '行为管理', 0),
(20, '新增用户行为', 19, 0, 'User/addaction', 0, '', '', 0),
(21, '编辑用户行为', 19, 0, 'User/editaction', 0, '', '', 0),
(22, '保存用户行为', 19, 0, 'User/saveAction', 0, '"用户->用户行为"保存编辑和新增的用户行为', '', 0),
(23, '变更行为状态', 19, 0, 'User/setStatus', 0, '"用户->用户行为"中的启用,禁用和删除权限', '', 0),
(24, '禁用会员', 19, 0, 'User/changeStatus?method=forbidUser', 0, '"用户->用户信息"中的禁用', '', 0),
(25, '启用会员', 19, 0, 'User/changeStatus?method=resumeUser', 0, '"用户->用户信息"中的启用', '', 0),
(26, '删除会员', 19, 0, 'User/changeStatus?method=deleteUser', 0, '"用户->用户信息"中的删除', '', 0),
(27, '权限管理', 16, 0, 'AuthManager/index', 0, '', '用户管理', 0),
(28, '删除', 27, 0, 'AuthManager/changeStatus?method=deleteGroup', 0, '删除用户组', '', 0),
(29, '禁用', 27, 0, 'AuthManager/changeStatus?method=forbidGroup', 0, '禁用用户组', '', 0),
(30, '恢复', 27, 0, 'AuthManager/changeStatus?method=resumeGroup', 0, '恢复已禁用的用户组', '', 0),
(31, '新增', 27, 0, 'AuthManager/createGroup', 0, '创建新的用户组', '', 0),
(32, '编辑', 27, 0, 'AuthManager/editGroup', 0, '编辑用户组名称和描述', '', 0),
(33, '保存用户组', 27, 0, 'AuthManager/writeGroup', 0, '新增和编辑用户组的"保存"按钮', '', 0),
(34, '授权', 27, 0, 'AuthManager/group', 0, '"后台 \\ 用户 \\ 用户信息"列表页的"授权"操作按钮,用于设置用户所属用户组', '', 0),
(35, '访问授权', 27, 0, 'AuthManager/access', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"访问授权"操作按钮', '', 0),
(36, '成员授权', 27, 0, 'AuthManager/user', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"成员授权"操作按钮', '', 0),
(37, '解除授权', 27, 0, 'AuthManager/removeFromGroup', 0, '"成员授权"列表页内的解除授权操作按钮', '', 0),
(38, '保存成员授权', 27, 0, 'AuthManager/addToGroup', 0, '"用户信息"列表页"授权"时的"保存"按钮和"成员授权"里右上角的"添加"按钮)', '', 0),
(39, '分类授权', 27, 0, 'AuthManager/category', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"分类授权"操作按钮', '', 0),
(40, '保存分类授权', 27, 0, 'AuthManager/addToCategory', 0, '"分类授权"页面的"保存"按钮', '', 0),
(41, '模型授权', 27, 0, 'AuthManager/modelauth', 0, '"后台 \\ 用户 \\ 权限管理"列表页的"模型授权"操作按钮', '', 0),
(42, '保存模型授权', 27, 0, 'AuthManager/addToModel', 0, '"分类授权"页面的"保存"按钮', '', 0),
(43, '插件聚合', 0, 7, 'Addons/index', 0, '', '', 0),
(44, '插件管理', 43, 1, 'Addons/index', 0, '', '插件扩展', 0),
(45, '创建', 44, 0, 'Addons/create', 0, '服务器上创建插件结构向导', '', 0),
(46, '检测创建', 44, 0, 'Addons/checkForm', 0, '检测插件是否可以创建', '', 0),
(47, '预览', 44, 0, 'Addons/preview', 0, '预览插件定义类文件', '', 0),
(48, '快速生成插件', 44, 0, 'Addons/build', 0, '开始生成插件结构', '', 0),
(49, '设置', 44, 0, 'Addons/config', 0, '设置插件配置', '', 0),
(50, '禁用', 44, 0, 'Addons/disable', 0, '禁用插件', '', 0),
(51, '启用', 44, 0, 'Addons/enable', 0, '启用插件', '', 0),
(52, '安装', 44, 0, 'Addons/install', 0, '安装插件', '', 0),
(53, '卸载', 44, 0, 'Addons/uninstall', 0, '卸载插件', '', 0),
(54, '更新配置', 44, 0, 'Addons/saveconfig', 0, '更新插件配置处理', '', 0),
(55, '插件后台列表', 44, 0, 'Addons/adminList', 0, '', '', 0),
(56, 'URL方式访问插件', 44, 0, 'Addons/execute', 0, '控制是否有权限通过url访问插件控制器方法', '', 0),
(57, '钩子管理', 43, 2, 'Addons/hooks', 0, '', '插件扩展', 0),
(58, '模型管理', 68, 7, 'Model/index', 0, '', '系统设置', 0),
(59, '新增', 58, 0, 'model/add', 0, '', '', 0),
(60, '编辑', 58, 0, 'model/edit', 0, '', '', 0),
(61, '改变状态', 58, 0, 'model/setStatus', 0, '', '', 0),
(62, '保存数据', 58, 0, 'model/update', 0, '', '', 0),
(63, '属性管理', 68, 2, 'Attribute/index', 1, '网站属性配置。', '', 0),
(64, '新增', 63, 0, 'Attribute/add', 0, '', '', 0),
(65, '编辑', 63, 0, 'Attribute/edit', 0, '', '', 0),
(66, '改变状态', 63, 0, 'Attribute/setStatus', 0, '', '', 0),
(67, '保存数据', 63, 0, 'Attribute/update', 0, '', '', 0),
(68, '系统设置', 0, 8, 'Config/group', 0, '', '', 0),
(69, '网站设置', 68, 5, 'Config/group', 0, '', '系统设置', 0),
(70, '配置管理', 68, 8, 'Config/index', 0, '', '系统设置', 0),
(71, '编辑', 70, 0, 'Config/edit', 0, '新增编辑和保存配置', '', 0),
(72, '删除', 70, 0, 'Config/del', 0, '删除配置', '', 0),
(73, '新增', 70, 0, 'Config/add', 0, '新增配置', '', 0),
(74, '保存', 70, 0, 'Config/save', 0, '保存配置', '', 0),
(75, '菜单管理', 68, 9, 'Menu/index', 0, '', '系统设置', 0),
(76, '导航管理', 68, 10, 'Channel/index', 0, '', '系统设置', 0),
(77, '新增', 76, 0, 'Channel/add', 0, '', '', 0),
(78, '编辑', 76, 0, 'Channel/edit', 0, '', '', 0),
(79, '删除', 76, 0, 'Channel/del', 0, '', '', 0),
(80, '分类管理', 68, 6, 'Category/index', 0, '', '系统设置', 0),
(81, '编辑', 80, 0, 'Category/edit', 0, '编辑和保存栏目分类', '', 0),
(82, '新增', 80, 0, 'Category/add', 0, '新增栏目分类', '', 0),
(83, '删除', 80, 0, 'Category/remove', 0, '删除栏目分类', '', 0),
(84, '移动', 80, 0, 'Category/operate/type/move', 0, '移动栏目分类', '', 0),
(85, '合并', 80, 0, 'Category/operate/type/merge', 0, '合并栏目分类', '', 0),
(86, '备份数据库', 68, 3, 'Database/index?type=export', 0, '', '数据备份', 0),
(87, '备份', 86, 0, 'Database/export', 0, '备份数据库', '', 0),
(88, '优化表', 86, 0, 'Database/optimize', 0, '优化数据表', '', 0),
(89, '修复表', 86, 0, 'Database/repair', 0, '修复数据表', '', 0),
(90, '还原数据库', 68, 4, 'Database/index?type=import', 0, '', '数据备份', 0),
(91, '恢复', 90, 0, 'Database/import', 0, '数据库恢复', '', 0),
(92, '删除', 90, 0, 'Database/del', 0, '删除备份文件', '', 0),
(93, '其他', 0, 11, 'other', 1, '', '', 1),
(96, '新增', 75, 0, 'Menu/add', 0, '', '系统设置', 0),
(98, '编辑', 75, 0, 'Menu/edit', 0, '', '', 0),
(104, '下载管理', 102, 0, 'Think/lists?model=download', 0, '', '', 0),
(105, '配置管理', 102, 0, 'Think/lists?model=config', 0, '', '', 0),
(106, '行为日志', 16, 0, 'Action/actionlog', 0, '', '行为管理', 0),
(108, '修改密码', 16, 0, 'User/updatePassword', 1, '', '', 0),
(109, '修改昵称', 16, 0, 'User/updateNickname', 1, '', '', 0),
(110, '查看行为日志', 106, 0, 'action/edit', 1, '', '', 0),
(112, '新增数据', 58, 0, 'think/add', 1, '', '', 0),
(113, '编辑数据', 58, 0, 'think/edit', 1, '', '', 0),
(114, '导入', 75, 0, 'Menu/import', 0, '', '', 0),
(115, '生成', 58, 0, 'Model/generate', 0, '', '', 0),
(116, '新增钩子', 57, 0, 'Addons/addHook', 0, '', '', 0),
(117, '编辑钩子', 57, 0, 'Addons/edithook', 0, '', '', 0),
(118, '文档排序', 3, 0, 'Article/sort', 1, '', '', 0),
(119, '排序', 70, 0, 'Config/sort', 1, '', '', 0),
(120, '排序', 75, 0, 'Menu/sort', 1, '', '', 0),
(121, '排序', 76, 0, 'Channel/sort', 1, '', '', 0),
(123, '关注者', 0, 4, 'Wxuser/lists', 0, '', '', 0),
(124, '关键词', 0, 5, 'Keywordview/lists', 0, '', '', 0),
(126, '商家列表', 122, 0, 'think/lists?model=shopmanage', 0, '', '商家管理', 0),
(127, '商家分组', 122, 0, 'Shop/cate', 0, '', '商家管理', 0),
(128, '会员卡列表', 122, 0, 'Shop/vipcardlists', 0, '', '会员卡管理', 0),
(129, '关注者列表', 123, 0, 'Wxuser/lists', 0, '', '个人资料管理', 0),
(130, '关注者分组', 123, 0, 'Think/lists?model=followercate', 0, '', '个人资料管理', 0),
(131, '微信行为', 123, 0, 'Wxuser/action', 1, '', '微信用户分析', 0),
(132, '关注者消息', 123, 0, 'Wxuser/message', 1, '', '消息管理', 0),
(133, '关键词列表', 124, 7, 'Keywordview/lists', 0, '', '关键词管理', 0),
(134, '关键词分组', 124, 8, 'think/lists?model=keywordcate', 0, '', '关键词管理', 0),
(135, '匹配规则', 124, 6, 'think/lists?model=rules', 0, '', '规则类型配置', 0),
(136, '请求类型', 124, 5, 'think/lists?model=posts', 0, '', '规则类型配置', 0),
(137, '关键词添加', 124, 9, 'Keywordview/addkeyword', 0, '', '关键词管理', 0),
(138, '标签列表', 124, 3, 'Think/lists?model=tagslists', 0, '', 'Tag标签管理', 0),
(139, '标签分组', 124, 4, 'Think/lists?model=tagscate', 0, '', 'Tag标签管理', 0),
(140, '系统参数', 124, 2, 'Keywordview/edit', 1, '', '', 0),
(151, '数据模型编辑', 148, 0, 'Flycloud/edit', 1, '', '', 0),
(150, '动态显示表字段', 148, 0, 'Flycloud/get_tablefields', 1, '', '', 0),
(149, '添加本地模型', 148, 0, 'Flycloud/add', 1, '', '', 0),
(147, '数据聚合', 0, 6, 'Flycloud/lists', 0, '', '', 0),
(148, '本地模型列表', 147, 0, 'Flycloud/lists', 0, '', '本地模型', 0),
(152, '添加用户分组', 130, 0, 'Wxuser/add', 1, '', '', 0),
(154, '请求列表库', 133, 0, 'Keywordview/postlists', 1, '', '', 0),
(155, '微信响应库', 133, 0, 'Keywordview/responselists', 1, '', '', 0),
(156, '编辑用户请求', 133, 0, 'Keywordview/edit_posts', 1, '', '', 0),
(157, '平台接口', 147, 0, 'Think/lists?model=webuntil', 0, '', '第三方接口', 0),
(158, '添加公众号', 147, 0, 'Think/lists?model=account', 0, '', '公众号管理', 0),
(159, '默认回复', 124, 1, 'Keywordview/default_reply', 0, '关注回复,黑名单回复,超时回复', '关键词管理', 0),
(160, '模式列表', 124, 0, 'Keywordview/click_list', 0, '', '菜单模式管理', 0),
(161, '模式添加', 124, 0, 'Keywordview/click_add', 0, '', '菜单模式管理', 0),
(162, '清空缓存', 68, 0, 'Action/delcache', 0, '', '缓存管理', 0),
(163, '用户编辑', 123, 0, 'Wxuser/edit', 1, '', '', 0),
(164, '芒果应用商店', 44, 0, 'Addons/addonsshop', 1, '', '插件管理', 0);

-- --------------------------------------------------------

--
-- 表的结构 `amango_model`
--

CREATE TABLE IF NOT EXISTS `amango_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '模型标识',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  `extend` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '继承的模型',
  `relation` varchar(30) NOT NULL DEFAULT '' COMMENT '继承与被继承模型的关联字段',
  `need_pk` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '新建表时是否需要主键字段',
  `field_sort` text NOT NULL COMMENT '表单字段排序',
  `field_group` varchar(255) NOT NULL DEFAULT '1:基础' COMMENT '字段分组',
  `attribute_list` text NOT NULL COMMENT '属性列表（表的字段）',
  `template_list` varchar(100) NOT NULL DEFAULT '' COMMENT '列表模板',
  `template_add` varchar(100) NOT NULL DEFAULT '' COMMENT '新增模板',
  `template_edit` varchar(100) NOT NULL DEFAULT '' COMMENT '编辑模板',
  `list_grid` text NOT NULL COMMENT '列表定义',
  `list_row` smallint(2) unsigned NOT NULL DEFAULT '10' COMMENT '列表数据长度',
  `search_key` varchar(50) NOT NULL DEFAULT '' COMMENT '默认搜索字段',
  `search_list` varchar(255) NOT NULL DEFAULT '' COMMENT '高级搜索的字段',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `engine_type` varchar(25) NOT NULL DEFAULT 'MyISAM' COMMENT '数据库引擎',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文档模型表' AUTO_INCREMENT=26 ;

--
-- 转存表中的数据 `amango_model`
--

INSERT INTO `amango_model` (`id`, `name`, `title`, `extend`, `relation`, `need_pk`, `field_sort`, `field_group`, `attribute_list`, `template_list`, `template_add`, `template_edit`, `list_grid`, `list_row`, `search_key`, `search_list`, `create_time`, `update_time`, `status`, `engine_type`) VALUES
(1, 'document', '基础文档', 0, '', 1, '{"1":["192","191","114","3","5","9","10","11","12","14","19","20","22"]}', '1:基础', '', '', '', '', 'id:编号\r\ntitle:标题:article/index?cate_id=[category_id]&pid=[id]\r\ntype|get_document_type:类型\r\nlevel:优先级\r\nupdate_time|time_format:最后更新\r\nstatus_text:状态\r\nview:浏览\r\nid:操作:/Home/Article/detail?id=[id]|前台预览,replylists?pid=[id]&modelid=[model_id]|评论,[EDIT]&cate_id=[category_id]|编辑,article/setstatus?status=-1&ids=[id]|删除', 0, '', '', 1383891233, 1412005580, 1, 'MyISAM'),
(2, 'article', '文章', 1, '', 1, '{"1":["3","20","14","195","5","12"],"2":["191","192","10","24","11","25"],"3":["26","19","22","114","9"]}', '1:微信封面,2:详细内容,3:可选参数', '', '', '', '', 'id:编号\r\ntitle:标题:article/edit?cate_id=[category_id]&id=[id]\r\ncontent:内容', 0, '', '', 1383891243, 1413607646, 1, 'MyISAM'),
(3, 'download', '下载', 1, '', 1, '{"1":["3","28","30","32","2","5","31"],"2":["13","10","27","9","12","16","17","19","11","20","14","29"]}', '1:基础,2:扩展', '', '', '', '', 'id:编号\r\ntitle:标题', 0, '', '', 1383891252, 1387260449, 1, 'MyISAM'),
(4, 'shopmanage', '商家店铺', 0, '', 1, '{"1":["35"]}', '1:基础,2:配置,3:图文', '', '', '', '', 'shopname:店铺名称', 10, '', '', 1397443830, 1397447105, 1, 'MyISAM'),
(5, 'keyword', '关键词', 0, '', 1, '{"1":["36","37","39","40","41","42"],"2":["43","44","47"],"3":["45"]}', '1:基本参数,2:关键词激活,3:微信回复体', '', '', '', '', 'id:ID\r\nkeyword_rules:匹配语句\r\nkeyword_post:请求类型\r\nkeyword_reply:回复类型\r\nkeyword_group:所属词组\r\nkeyword_top:上文\r\nkeyword_down:下文\r\nkeyword_click:激活次数\r\n', 10, '', 'keyword_start|keyword_end', 1397469649, 1397477931, 1, 'MyISAM'),
(6, 'rules', '匹配规则', 0, '', 1, '{"1":["61","54","55"],"2":["56","59","60"]}', '1:必填参数,2:选填参数', '', '', '', '', 'id:ID\r\nrules_name:规则名称\r\nrules_title:规则标识\r\nrules_content:规则内容\r\nrules_description:规则说明\r\nsort:排序:[SORT]\r\nstatus:状态:[STATUS]|开启\r\nid:操作:[EDIT]|编辑,[DELETE]|删除@ajax-get', 10, 'rules_title', 'rules_content', 1397815433, 1397889528, 1, 'MyISAM'),
(7, 'posts', '请求类型', 0, '', 1, '{"1":["62","63","65"],"2":["66","64","67"]}', '1:必填参数,2:选填参数', '', '', '', '', 'id:ID\r\nposts_name:请求名称\r\nposts_title:请求标识\r\nposts_fields:附带字段\r\nposts_description:规则说明\r\nsort:排序:[SORT]\r\nstatus:状态:[STATUS]|开启\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', 10, 'posts_title', '', 1397881169, 1397882328, 1, 'MyISAM'),
(8, 'tagscate', 'Tag分组', 0, '', 1, '{"1":["68","69","168","70","71"]}', '1:基本参数', '', '', '', '', 'id:ID\r\ntagscate_name:标签组名称\r\ntagscate_title:标签组标识\r\ntagscate_type:适用消息\r\ntagscate_description:作用描述\r\nstatus:状态:[STATUS]|开启\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', 10, 'tagscate_title,tagscate_name', '', 1397895361, 1399340564, 1, 'MyISAM'),
(9, 'tagslists', 'Tag标签', 0, '', 1, '{"1":["72","73","74","76","77"],"2":["80","78","79"]}', '1:必填参数,2:选填参数', '', '', '', '', 'id:ID\r\ntagslists_title:标识\r\ntagslists_group:所属分组\r\ntagslists_type:类型\r\ntagslists_action:操作\r\ntagslists_param:参数\r\nsort:排序:[SORT]\r\nstatus:状态:[STATUS]|开启\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', 10, '', '', 1397908270, 1397920254, 1, 'MyISAM'),
(10, 'flycloud', '数据模型', 0, '', 1, '{"1":["81","82","83","84","85","86"]}', '1:基础', '', '', '', '', 'id:ID\r\ndata_name:模型名称\r\ndata_type:模型类型\r\ndata_table:数据表名\r\ndata_fields:读取字段\r\ndata_condition:读取条件\r\nstatus:状态:[STATUS]|开启\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', 10, '', '', 1397985161, 1400986800, 1, 'MyISAM'),
(11, 'keywordcate', '关键词分组', 0, '', 1, '{"1":["87","88","89"]}', '1:基本参数', '', '', '', '', 'id:ID\r\nkeywordcate_name:分组名称\r\nkeywordcate_denyuser:黑名单用户组\r\nstatus:状态:[STATUS]|开启\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', 10, '', '', 1398000689, 1398002747, 1, 'MyISAM'),
(13, 'followercate', '会员组', 0, '', 1, '{"1":["90","91","92","93"]}', '1:基本参数', '', '', '', '', 'id:ID\r\nfollowercate_name:会员组名\r\nfollowercate_title:标识\r\nfollowercate_des:特权说明\r\nstatus:状态:[STATUS]|开启\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', 10, '', '', 1398003473, 1398003865, 1, 'MyISAM'),
(21, 'huodon', '活动', 1, '', 1, '{"1":["3","20","14","5","12"],"2":["174","173","176","175","172","11"],"3":["192","191","10","19","22","114","9"]}', '1:微信封面,2:活动内容,3:可选参数', '', '', '', '', 'id:ID\r\nfromusername:openid\r\njoinname:参加名字\r\njointel:联系方式\r\njoinextra:备注说明\r\nfromusername:用户openid\r\nid:操作:[EDIT]|编辑,[DELETE]|删除@ajax-get', 10, '', '', 1402293296, 1403853598, 1, 'MyISAM'),
(17, 'response', '微信响应体列表', 0, '', 1, '', '1:基础', '', '', '', '', '', 10, '', '', 1398355012, 1398355012, 1, 'MyISAM'),
(18, 'webuntil', '第三方接口', 0, '', 1, '{"1":["126","127","134","135","129","130"],"2":["131","128","133","132","136"]}', '1:基本参数,2:拓展参数', '', '', '', '', 'id:ID\r\nwebuntil_name:接口名称\r\nwebuntil_url:第三方地址\r\nwebuntil_token:TOKEN值\r\nwebuntil_type:请求类型\r\nwebuntil_backtype:返回类型\r\nwebuntil_cache:缓存时间\r\nwebuntil_tag:植入TAG\r\nstatus:状态:[STATUS]|开启\r\nid:操作:[EDIT]|编辑,[DELETE]|删除@ajax-get', 10, 'webuntil_title', '', 1398524693, 1399791989, 1, 'MyISAM'),
(19, 'account', '公众号列表', 0, '', 1, '{"1":["137","138","139","151","141","142"],"2":["194","140","143","144","148"]}', '1:基本绑定,2:服务参数', '', '', '', '', 'id:ID\r\naccount_name:公众号名称\r\naccount_nickname:花名\r\naccount_oldid:原始ID\r\naccount_token:TOKEN\r\naccount_default:状态类型\r\nstatus:状态:[STATUS]|开启\r\nid:操作:[EDIT]|编辑,[DELETE]|删除@ajax-get', 10, '', '', 1398531189, 1412328891, 1, 'MyISAM'),
(20, 'weixinmember', '微信关注者', 0, '', 1, '{"1":["152","167","154","155","156","157","158","159","160","161","162","163","164","165","166","169"]}', '1:基础', '', '', '', '', 'id:ID\nfromusername:openid\nnickname:昵称\ncate_group:分组\nsex:性别\nstatus:状态:[STATUS]|开启\nid:操作:[EDIT]|编辑,[DELETE]|删除@ajax-get', 10, '', '', 1399119753, 1400739317, 1, 'MyISAM'),
(23, 'replyschoolhuodon', '校园活动回复列表', 0, '', 1, '', '1:基础', '', '', '', '', 'id:ID\njoinname:参加名字\njointel:联系方式\njoinextra:备注说明\nfromusername:用户openid\npid:所属ID\nid:操作:[EDIT]|编辑,[DELETE]|删除@ajax-get', 10, '', '', 1403153982, 1403153982, 1, 'MyISAM'),
(24, 'replyreplyjdhuodon', '精彩活动回复列表', 0, '', 1, '', '1:基础', '', '', '', '', '', 10, '', '', 1409217013, 1409217013, 1, 'MyISAM');

-- --------------------------------------------------------

--
-- 表的结构 `amango_picture`
--

CREATE TABLE IF NOT EXISTS `amango_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

--
-- 转存表中的数据 `amango_picture`
--

INSERT INTO `amango_picture` (`id`, `path`, `url`, `md5`, `sha1`, `status`, `create_time`) VALUES
(1, '/Uploads/Picture/2014-08-01/53db93ced508d.jpg', '', '42dbce0c82698675978119c4bd33b077', 'c3acdc0b6ef7d94ade03ee041d69d93758f0b390', 1, 1406899150),
(2, '/Uploads/Picture/2014-08-01/53db969c70f4c.jpg', '', '2573191f183af07020129feee9f6a6a2', '21a2d1547abfa9e5f6ccf091227935346708465f', 1, 1406899868),
(3, '/Uploads/Picture/2014-08-01/53db9887cb8ee.jpg', '', 'a006b6aa053d8488ca14250f4ecc95e6', '54312a73dd914793cf0106a0ed61c289c1907459', 1, 1406900359),
(4, '/Uploads/Picture/2014-08-01/53db995e2a850.jpg', '', 'f9cebea978a25c49578255f8ae639b58', 'bd8d229f69513694c299971f780440f04b008621', 1, 1406900574),
(5, '/Uploads/Picture/2014-08-01/53db99852e4f3.jpg', '', '935c3e72122eeecc71bada53570f5051', '3297d70738afc78249a04d8990ad0c395326aa92', 1, 1406900613),
(6, '/Uploads/Picture/2014-08-01/53db9b1c8b4f8.jpg', '', '571feaf9732823e0d176c9e6c7a18893', '91bad48977a85a94ba38f2a7ed749e218797f27b', 1, 1406901020),
(7, '/Uploads/Picture/2014-08-01/53db9caf7b240.jpg', '', '1bbb14501b051e437e76671e277b298f', '67c5388ea5932b207667e453b7faf930de178819', 1, 1406901423),
(8, '/Uploads/Picture/2014-08-01/53db9e3cd3a78.jpg', '', 'cf7662ca09c0b7e455fca0f8f8f8721b', 'a9c77ae5e4d8ca7f1674d52b6ede8a47f48f4ed6', 1, 1406901820),
(9, '/Uploads/Picture/2014-08-01/53db9f9aea878.jpg', '', '2cbe5ca67c1fd895180b1a92dcf6f44c', 'f1ca08b7fb0e1752770caf21fb250fb80e4b204b', 1, 1406902170),
(10, '/Uploads/Picture/2014-08-01/53dba07d0e086.jpg', '', 'c69ad5b33f4214d0dddaaee7f7e244c0', '28dd9df5fd5c51c8104800113f85dedbf65c4105', 1, 1406902396),
(11, '/Uploads/Picture/2014-08-01/53dba1cc6c8ba.jpg', '', '4ce0b6382978f9a95aa7b777d3cf62a1', '915829c3ecd57b2df995878d74b4ec24e1a969ec', 1, 1406902730),
(12, '/Uploads/Picture/2014-08-01/53dba222cc572.jpg', '', '6b643bef89a567cec263d97c50fa5218', 'c3ed4a50dfc856916ddf3de57f69653e1e842092', 1, 1406902818),
(13, '/Uploads/Picture/2014-08-01/53dba39516d19.jpg', '', '7a1f1ff80bd32f27aac316df2fafd09a', '315419d744ae5273f45c3b19d59e1fbdaa586946', 1, 1406903188),
(14, '/Uploads/Picture/2014-08-25/53fae11dbb64f.jpg', '', 'ba45c8f60456a672e003a875e469d0eb', '30420d1a9afb2bcb60335812569af4435a59ce17', 1, 1408950556),
(15, '/Uploads/Picture/2014-08-26/53fc4feab6c98.jpg', '', 'a23f2097bc3f3ade95c7a8cfe6d3a744', 'f1e298b124d6fff7deb4373b3aecf96f90281f0b', 1, 1409044458),
(16, '/Uploads/Picture/2014-08-27/53fd811516d2b.jpg', '', '2f41ed41d507f72c0a79ead545f19e83', '757d74802157cad6a193b8395bd1310783f7f6f4', 1, 1409122580),
(17, '/Uploads/Picture/2014-08-27/53fdbbd12e7c7.jpg', '', '722bf609b5965347c105ad463a7191bc', '3792291fd6605bc55e883e3c1d742e6edc4244aa', 1, 1409137616),
(18, '/Uploads/Picture/2014-08-28/53fec1309e2e0.jpg', '', '3b6844a42706a7056ff6087550d1002c', '8e697372cd943e996c9f267b386920aa383492e5', 1, 1409204528),
(19, '/Uploads/Picture/2014-08-28/53ff113e344bc.jpg', '', '722bd41d816789762eb6180bfe1e48fd', '09641252199c0431363a91f0a27fdecc4287f728', 1, 1409225016),
(20, '/Uploads/Picture/2014-08-28/53ff440a424d8.jpg', '', '4ba940849d4113cb827eff4428939fd2', 'e111bac4eea342d98847b97834387947b1c09b5c', 1, 1409238024),
(21, '/Uploads/Picture/2014-08-31/540295cae4ff9.jpg', '', '2bb1404c088090b54fe9cd9da97388d1', '27bc9053be978b66a715b6da4b605261daa0fc29', 1, 1409455562),
(22, '/Uploads/Picture/2014-08-31/540296e91aa7c.jpg', '', '77614a2c9c8e03a7b93ee065875b81cd', 'd887c72206148e6075bba3a3878fad4bffb05fdf', 1, 1409455848),
(23, '/Uploads/Picture/2014-09-01/54035d75e6fd6.jpg', '', '6105566e31f295d20cfcda2deec2ef88', 'e07b3bf809a16afffcf3f01f773ba5decbf62c6e', 1, 1409506677),
(24, '/Uploads/Picture/2014-09-01/54035d834501b.png', '', '5150cd297ffac02f9f45db98c3a1e645', '52f731c32cbe93b821b3a33badcb93c74dbde0a5', 1, 1409506691),
(25, '/Uploads/Picture/2014-09-01/540412d445bb0.jpg', '', '99f52e3986c7142c1949ade9097bcbc4', '26deb451a7d9b247b6621309ee5c63510d5b5216', 1, 1409553107),
(26, '/Uploads/Picture/2014-09-01/54041d1c8956e.png', '', '2a8b41e415677f59746e155e5be3de77', 'd733cf19b79dde39f3e55b054991ddb633e66748', 1, 1409555739),
(27, '/Uploads/Picture/2014-09-01/54041e230071a.jpg', '', 'd7f99dce03428863f0c63ecc1cec1bc0', 'e9c9518259ed9e8d6af06b7c4f99558f0095ba87', 1, 1409555991),
(28, '/Uploads/Picture/2014-09-01/54041e6bad033.jpg', '', 'c85beb88649dbe5f6defdb2aa2518f03', 'd156ac2065bf3cfa89671b6cfbbd7d15f2111b68', 1, 1409556075),
(29, '/Uploads/Picture/2014-09-01/540424542f2a7.jpg', '', 'ac18a18438beb94bf2fec5081455856e', '3eb8813debaee5816f33af5b18a570c0262c98e1', 1, 1409557587),
(30, '/Uploads/Picture/2014-09-01/54042609613da.jpg', '', '8d206670a411738ceafe804227a49285', 'fa791b1d49459581ed7a267193c25bbe76d00873', 1, 1409558025),
(31, '/Uploads/Picture/2014-09-02/5405941b818c9.jpg', '', '2818e41ac35de39e63cee929a6e65b83', 'b5eed3cd14cac3122edcea4c4b64f7264352f321', 1, 1409651730),
(32, '/Uploads/Picture/2014-09-02/5405a7019612e.jpg', '', '3d2c780c1b5b94d445307a506fad8aef', '189e7fdc5427ed9c767f5f12ca65e50e03345a49', 1, 1409656576),
(33, '/Uploads/Picture/2014-09-02/5405aa4b09ad0.jpg', '', '4a01546cb19ba666e8e573f05210f717', 'e4fad7e5601cc9158ea657e9bebfda4e11a6ad4d', 1, 1409657418),
(34, '/Uploads/Picture/2014-09-02/5405ac23a6526.jpg', '', 'a6afde6e8612159450d8fe0aed2f9615', 'ff315b6d17c219733a61cceab53a0f007024c6fa', 1, 1409657891),
(35, '/Uploads/Picture/2014-09-02/5405b13f49973.jpg', '', 'e954961350d87b8b850bf82b59dfd7bd', 'd905cc21cbd4bb601c1e8a2cb07d8fbb67ec02c4', 1, 1409659198),
(36, '/Uploads/Picture/2014-09-02/5405c456ce8c3.jpg', '', '25bcc44bdddeb409a1819611e2cc0138', 'a360c54029e45a38ed31f89d75fedd6bc5e4835e', 1, 1409664086),
(37, '/Uploads/Picture/2014-09-02/5405c6ce5ea8c.jpg', '', '00c6f7f61e1e9cd90efa3e871f0342b4', '0eeaa96c3d72cb062b870330dac72b1e6b1b329a', 1, 1409664718),
(38, '/Uploads/Picture/2014-09-02/5405c76fb28c2.jpg', '', 'a0ef1dcc86a67dd2d09328041364bfd0', '203ff199b21f36d2fb31ba7377c609031d3cec52', 1, 1409664879),
(39, '/Uploads/Picture/2014-09-02/5405c7dd08343.jpg', '', '5c80714e07c4200c8fdd542035e189ae', 'ec4fa2f3411fe0b91c484093a91dbf130efe8433', 1, 1409664988),
(40, '/Uploads/Picture/2014-09-02/5405c944122f0.jpg', '', 'c240b598d3da010d3dace61a6619fd90', '1004cd57f01240846eee20587eaea69bccf08c9e', 1, 1409665347),
(41, '/Uploads/Picture/2014-09-02/5405ca205ddcb.jpg', '', '1f6bca3a54a445d571e39c59810c7aea', '7d216ea1a8d25a5e2e87a30153882befb7d1b445', 1, 1409665567),
(42, '/Uploads/Picture/2014-09-02/5405ca8c31ae7.jpg', '', '3573a4ecf6a6c51dd68eb53f0a9d7f8e', '7f073372dbab0a971de79cb212a1db49ce54084f', 1, 1409665675),
(43, '/Uploads/Picture/2014-09-02/5405cb1118961.jpg', '', '8c37ac51bce1960a90fac3f4543e67f8', 'f25a6c3d8f534f71178d90c901560790169d4fcb', 1, 1409665808),
(44, '/Uploads/Picture/2014-09-02/5405cb915b28f.png', '', '5b813625580a9e16daa4ef3a6dddc053', '12de2e8f9128a94b4944b006ce7847b307e09abf', 1, 1409665935),
(45, '/Uploads/Picture/2014-09-02/5405cbf78b568.jpg', '', '29c900c96d52902feab8ea70019075fb', '77a8138be7d4b70ecee0e32fc3d34e2340e38802', 1, 1409666039),
(46, '/Uploads/Picture/2014-09-02/5405cc6d7d531.jpg', '', '1a2447467b300358136fd6d8ec134d68', 'ca7649670e93ba4479375b6f4b2a3e5d5738a38c', 1, 1409666157),
(47, '/Uploads/Picture/2014-09-02/5405ccc4b4d94.jpg', '', '7a28c9784dc63061de9e7efcac3061f5', 'a605b4c17279d34c9d70595ca202b678d1d55d98', 1, 1409666244),
(48, '/Uploads/Picture/2014-09-02/5405cd527c958.jpg', '', '7de0fbfbd5503aaf3e55b4ac29942191', 'acc9ae3970ea5e770bf32451417584fcae32e75c', 1, 1409666386),
(49, '/Uploads/Picture/2014-09-02/5405cdb72e335.jpg', '', '41923c21955b89228ac56883e676877b', '806e06eac27e50bfedd2641d51baab0e8edc902a', 1, 1409666486),
(50, '/Uploads/Picture/2014-09-02/5405ce56b4892.jpg', '', '76d686214f68fb728e91097954fed7af', '5e8e0088ca1db48c6011d36b11e4c697d39ea6a9', 1, 1409666646),
(51, '/Uploads/Picture/2014-09-02/5405cf1c442a3.jpg', '', '680fddae7e31e97a6ee76ac20b072947', '6557fc6acb158044644c442ad66f4edbd22f611c', 1, 1409666843),
(52, '/Uploads/Picture/2014-09-03/5406cc73cd765.jpg', '', 'e01e08a7d77f876586bd407b70fca213', '90e96eb4e7801a6137a143bc3b521c3c3dfc3ba0', 1, 1409731699),
(53, '/Uploads/Picture/2014-09-03/54071ef28bdc4.jpg', '', '5f782c7d8073f586f4a2e4f48b05b2ca', '022d5e49892d4d2b51d772b62fd9b46697bc398a', 1, 1409752817),
(54, '/Uploads/Picture/2014-09-03/540725956566b.jpg', '', 'e8d74b3a558ef49077281ca090802da7', '3f9537d0801923ce313304e2ad52908395e1e88f', 1, 1409754516),
(55, '/Uploads/Picture/2014-09-04/54087f6d45dcc.jpg', '', 'e59673922e0c1046061d928091bc8ba0', 'd7f2fb0782b96ec360dfcf415eeaeda957202e1d', 1, 1409843052),
(56, '/Uploads/Picture/2014-09-05/5409d53196ac1.jpg', '', 'c567b999754a3aff42a1507db90fdb84', 'fc0626f31a93ac0a759e5314e396ad0ce299a139', 1, 1409930522),
(57, '/Uploads/Picture/2014-09-10/54101cdb61f3c.jpg', '', '56efb38307181ccfcc6975c8757bb37a', 'cdecb7673b07d72a035bee2bc1f5429cad61802b', 1, 1410342102),
(58, '/Uploads/Picture/2014-09-10/54101d354b5eb.jpg', '', '6222ef4c8d9cb6ebfb32a6063de91ccc', '40b0114faac3a875d42be0ad06ccd754b88a0ceb', 1, 1410342196),
(59, '/Uploads/Picture/2014-09-25/5423c176c65d4.png', '', '274d3ba3ffe83d882f742d0464fb754c', '806623b40226b93e8b66c82ab541c1572b4744ca', 1, 1411629430),
(60, '/Uploads/Picture/2014-09-27/5426cbdacdfe6.jpg', '', 'e9b892cf24bef74d266dffe40f1e4790', '567442f63b54a5fa121791486d5f2295d6713423', 1, 1411828698),
(61, '/Uploads/Picture/2014-09-30/5429860daba95.jpg', '', 'efd170e69e3b33b476602de3c9729415', '692a8ea86f54ca21e4e21421c251d1c34ed79f02', 1, 1412007437),
(62, '/Uploads/Picture/2014-09-30/542a9f95aba95.jpg', '', '1c28de59cce28703882537499087b5c5', '6610e5b8009152df99614a6e715ce91fbf921a94', 1, 1412079509),
(63, '/Uploads/Picture/2014-09-30/542aa566d9701.jpg', '', '6d7f8eb15fd53c0b8b7d00a4ae2c7f5d', 'b437e2b40940700faefdcc6e9e43b394d5517db3', 1, 1412080998),
(64, '/Uploads/Picture/2014-09-30/542aa5f744aa2.jpg', '', '681b9733570d9df3e4aa0f48e01286f4', 'f917ae01015d20a65753fbf3c72dd7592a922a75', 1, 1412081143),
(65, '/Uploads/Picture/2014-09-30/542aa629a037a.jpg', '', '8e0af6d8739261045243492d0f7db8f4', 'd7fca24b856c666de13fbfcb1e51f37d9246d394', 1, 1412081193),
(66, '/Uploads/Picture/2014-10-04/542fafe0dd40a.png', '', 'fa4bc7e8e375c9bf37a20f32a36bb669', '91210518816c49047f20c4df73bef7cc047ee03f', 1, 1412411360),
(67, '/Uploads/Picture/2014-10-15/543e7739ec82e.png', '', 'c69d7859f21b8792ac674fccdcbdb9c8', '8bb2e3c371cb9a8fd8c7c1de762d04e3c328c67d', 1, 1413379897),
(68, '/Uploads/Picture/2014-10-16/543fc7d93567e.png', '', '63a5106e355d7cf20733337297608a6e', 'c01667a89a0a6ce4273cf865dac9f8a15e6d274d', 1, 1413466073);

-- --------------------------------------------------------

--
-- 表的结构 `amango_posts`
--

CREATE TABLE IF NOT EXISTS `amango_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `posts_name` text NOT NULL COMMENT '请求名称',
  `posts_title` varchar(255) NOT NULL COMMENT '请求标识',
  `posts_fields` varchar(255) NOT NULL COMMENT '附带字段',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '请求状态',
  `sort` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '请求排序',
  `posts_description` text NOT NULL COMMENT '请求描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `amango_posts`
--

INSERT INTO `amango_posts` (`id`, `posts_name`, `posts_title`, `posts_fields`, `status`, `sort`, `posts_description`) VALUES
(1, '文本消息', 'text', 'Content', 1, 100, ''),
(2, '图片消息', 'image', 'PicUrl,MediaId', 1, 90, ''),
(3, '语音消息', 'voice', 'MediaId,Format,Recognition', 1, 50, ''),
(4, '视频消息', 'video', 'MediaId,ThumbMediaId', 1, 40, ''),
(5, '位置消息', 'location', 'Location_X,Location_Y,Label,Scale', 1, 0, ''),
(6, '链接消息', 'link', 'Title,Description,Url', 1, 60, '');

-- --------------------------------------------------------

--
-- 表的结构 `amango_replyschoolhuodon`
--

CREATE TABLE IF NOT EXISTS `amango_replyschoolhuodon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `joinname` varchar(255) NOT NULL COMMENT '参加名字',
  `jointel` varchar(255) NOT NULL COMMENT '联系方式',
  `joinextra` text NOT NULL COMMENT '备注说明',
  `fromusername` varchar(255) NOT NULL COMMENT '用户openid',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `amango_replyschoolhuodon`
--

INSERT INTO `amango_replyschoolhuodon` (`id`, `joinname`, `jointel`, `joinextra`, `fromusername`, `pid`) VALUES
(1, '大大', 'ss', 'dada', '游客1403860053', 19),
(3, '测试', 'DADA', 'dadaaaadasdas', '游客1403863256', 19),
(4, '飒飒', '1233433443', '测试回复', '1', 9);

-- --------------------------------------------------------

--
-- 表的结构 `amango_response`
--

CREATE TABLE IF NOT EXISTS `amango_response` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `response_xml` text NOT NULL COMMENT '响应XML',
  `response_compos` text NOT NULL COMMENT '响应结构体',
  `response_reply` text NOT NULL COMMENT '回复体类型',
  `response_name` varchar(255) NOT NULL COMMENT '响应体简称',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `response_static` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'XML状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=141 ;

--
-- 转存表中的数据 `amango_response`
--

INSERT INTO `amango_response` (`id`, `response_xml`, `response_compos`, `response_reply`, `response_name`, `status`, `response_static`) VALUES
(95, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>欢迎关注芒果集大！<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>点击进入了解更多。<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://jmu.amango.net/Uploads/Picture/2014-09-01/54041e6bad033.jpg]]></PicUrl><Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MjM5NzI2ODMyOA==&mid=200837515&idx=1&sn=38c64ac8e2c3e1a19ba42a2645c78113#rd]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:7:"42,news";s:7:"replace";s:230:"欢迎关注芒果集大！,http://jmu.amango.net/Uploads/Picture/2014-09-01/54041e6bad033.jpg,点击进入了解更多。,http://mp.weixin.qq.com/s?__biz=MjM5NzI2ODMyOA==&mid=200837515&idx=1&sn=38c64ac8e2c3e1a19ba42a2645c78113#rd";}', 'Dantw', '关注芒果集大单图文', 1, 0),
(107, '', 'a:4:{s:4:"type";s:5:"local";s:3:"num";i:1;s:6:"neiron";s:1:"9";s:7:"replace";a:1:{i:0;s:0:"";}}', 'Api', '图吧', 1, 0),
(115, '<Content><![CDATA[<amango:Contentheadtag>===> 集大图书馆 <===\n这里可以查询图书馆的藏书哦~<a href=''http://210.34.157.60:8080/sms/opac/search/showSearch.action?xc=6''>\n\n点击进入馆藏查询</a><amango:Contentendtag>]]></Content>', 'a:4:{s:4:"type";s:4:"text";s:3:"num";i:1;s:6:"neiron";s:171:"===> 集大图书馆 <===\n这里可以查询图书馆的藏书哦~<a href=''http://210.34.157.60:8080/sms/opac/search/showSearch.action?xc=6''>\n\n点击进入馆藏查询</a>";s:7:"replace";s:0:"";}', 'Text', '集大馆藏', 1, 1),
(127, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>加入内测群，分享你绝妙的Idea。<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>I want U.<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://jmu.amango.net/Uploads/Picture/2014-10-04/542fafe0dd40a.png]]></PicUrl><Url><![CDATA[http://weixin.qq.com/g/AVID6528jfC2BqOF]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:7:"42,news";s:7:"replace";s:139:"加入内测群，分享你绝妙的Idea。,/Uploads/Picture/2014-10-04/542fafe0dd40a.png,I want U.,http://weixin.qq.com/g/AVID6528jfC2BqOF";}', 'Dantw', '内测', 1, 0),
(129, '<Content><![CDATA[<amango:Contentheadtag>/呲牙欢迎关注芒果集大~\n/NO芒果集大能让你更便捷地查询信息、获取资讯及通知、体验最好玩的功能、发现更多有趣的小伙伴~\n/胜利当然，如果你有好的建议或者其他关于功能的设想，请点击菜单中的“今日看点=>申请内测”，加入内测组与技术大神，内涵大师，逗比小编一起交流吧~\n/OK我们的抽奖活动已经结束咯，新的活动即将开始，敬请期待。<amango:Contentendtag>]]></Content>', 'a:4:{s:4:"type";s:4:"text";s:3:"num";i:1;s:6:"neiron";s:460:"/呲牙欢迎关注芒果集大~\n/NO芒果集大能让你更便捷地查询信息、获取资讯及通知、体验最好玩的功能、发现更多有趣的小伙伴~\n/胜利当然，如果你有好的建议或者其他关于功能的设想，请点击菜单中的“今日看点=>申请内测”，加入内测组与技术大神，内涵大师，逗比小编一起交流吧~\n/OK我们的抽奖活动已经结束咯，新的活动即将开始，敬请期待。";s:7:"replace";s:0:"";}', 'Text', '关注时回复', 1, 1),
(134, '', 'a:4:{s:4:"type";s:5:"cloud";s:3:"num";i:1;s:6:"neiron";s:1:"4";s:7:"replace";a:1:{i:0;s:0:"";}}', 'Api', '云端抽奖', 1, 0),
(126, '<Content><![CDATA[<amango:Contentheadtag>白，日，依山尽！\n黄，河，入海流！\n欲，穷，千里目！\n更，上，一层楼！<amango:Contentendtag>]]></Content>', 'a:4:{s:4:"type";s:4:"text";s:3:"num";i:1;s:6:"neiron";s:99:"白，日，依山尽！\n黄，河，入海流！\n欲，穷，千里目！\n更，上，一层楼！";s:7:"replace";s:0:"";}', 'Text', 'boss', 1, 1),
(41, '', 'a:4:{s:4:"type";s:5:"local";s:3:"num";i:1;s:6:"neiron";s:1:"1";s:7:"replace";a:1:{i:0;s:0:"";}}', 'Api', '菜单-校园贴吧', 1, 0),
(42, '', 'a:4:{s:4:"type";s:5:"local";s:3:"num";i:1;s:6:"neiron";s:1:"8";s:7:"replace";a:1:{i:0;s:0:"";}}', 'Api', '菜单-隔壁', 1, 0),
(94, '', 'a:4:{s:4:"type";s:5:"cloud";s:3:"num";i:1;s:6:"neiron";s:1:"5";s:7:"replace";a:1:{i:0;s:0:"";}}', 'Api', '微盟快递', 1, 0),
(63, '', 'a:4:{s:4:"type";s:5:"cloud";s:3:"num";i:1;s:6:"neiron";s:1:"7";s:7:"replace";a:1:{i:0;s:0:"";}}', 'Api', '掌大人品', 1, 0),
(111, '<Content><![CDATA[<amango:Contentheadtag>===> 我的一卡通 <===\n这里可以查询您的余额哦~\n\n登陆账号为“学号”\n登录密码为“锐捷账号”\n<a href=''http://myid.jmu.edu.cn/ykt/default.aspx?signature=AMANGO_SIG''>点击进入登陆页面</a><amango:Contentendtag>]]></Content>', 'a:4:{s:4:"type";s:4:"text";s:3:"num";i:1;s:6:"neiron";s:223:"===> 我的一卡通 <===\n这里可以查询您的余额哦~\n\n登陆账号为“学号”\n登录密码为“锐捷账号”\n<a href=''http://myid.jmu.edu.cn/ykt/default.aspx?signature=AMANGO_SIG''>点击进入登陆页面</a>";s:7:"replace";s:0:"";}', 'Text', '集大一卡通', 1, 1),
(67, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>厦门公交查询<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>点击进入公交查询<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://vivixin.amango.net/Uploads/Picture/2014-08-26/53fc4feab6c98.jpg]]></PicUrl><Url><![CDATA[http://mybus.xiamentd.com/]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:7:"40,news";s:7:"replace";s:116:"厦门公交查询,/Uploads/Picture/2014-08-26/53fc4feab6c98.jpg,点击进入公交查询,http://mybus.xiamentd.com/";}', 'Dantw', '公交查询', 1, 0),
(70, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>电影<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>点击了解最新影讯。<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://vivixin.amango.net/Uploads/Picture/2014-09-10/54101d354b5eb.jpg]]></PicUrl><Url><![CDATA[http://m.mtime.cn/#!/]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:7:"42,news";s:7:"replace";s:102:"电影,/Uploads/Picture/2014-09-10/54101d354b5eb.jpg,点击了解最新影讯。,http://m.mtime.cn/#!/";}', 'Dantw', '电影', 1, 0),
(71, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>芒果百宝箱<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>点击进入百宝箱<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://xit.amango.net/Uploads/Picture/2014-09-15/541691b1a037a.png]]></PicUrl><Url><![CDATA[http://xit.amango.net/mgbox/]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:7:"42,news";s:7:"replace";s:133:"芒果百宝箱,http://xit.amango.net/Uploads/Picture/2014-09-15/541691b1a037a.png,点击进入百宝箱,http://xit.amango.net/mgbox/";}', 'Dantw', '芒果百宝箱', 1, 0),
(139, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>测试<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>阿斯达大阿萨德<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://s0.hao123img.com/res/r/image/2014-10-15/bfec00ce2ea409a83f3a9fdd6bebf374.jpg]]></PicUrl><Url><![CDATA[RAND3]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:7:"40,rand";s:7:"replace";s:113:"测试,http://s0.hao123img.com/res/r/image/2014-10-15/bfec00ce2ea409a83f3a9fdd6bebf374.jpg,阿斯达大阿萨德,";}', 'Dantw', '测试单图文', 1, 0),
(140, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>陌路的人，你永远不必等<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>已经分手了的人，双方都不必再等下去了。<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://jmu.amango.net/Uploads/Picture/2014-09-30/542aa5f744aa2.jpg]]></PicUrl><Url><![CDATA[http://jmu.amango.net/index.php?s=/Home/Article/detail/id/66/ucusername/P_UCUSERNAME/ucpassword/P_UCPASSWORD/stamp/1413606095.html]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:5:"40,66";s:7:"replace";s:289:"陌路的人，你永远不必等,http://jmu.amango.net/Uploads/Picture/2014-09-30/542aa5f744aa2.jpg,已经分手了的人，双方都不必再等下去了。,http://jmu.amango.net/index.php?s=/Home/Article/detail/id/66/ucusername/P_UCUSERNAME/ucpassword/P_UCPASSWORD/stamp/1413606095.html";}', 'Dantw', '测试', 1, 1),
(89, '', 'a:4:{s:4:"type";s:5:"local";s:3:"num";i:1;s:6:"neiron";s:2:"32";s:7:"replace";a:1:{i:0;s:0:"";}}', 'Api', '自定义菜单贴吧', 1, 0),
(123, '<Content><![CDATA[<amango:Contentheadtag>===> 万年历<===\n这里可以查询万年历哦~\n\n<a href=''http://baidu365.duapp.com/uc/Calendar.html?signature=AMANGO_SIG''>点击进入查询日历</a><amango:Contentendtag>]]></Content>', 'a:4:{s:4:"type";s:4:"text";s:3:"num";i:1;s:6:"neiron";s:154:"===> 万年历<===\n这里可以查询万年历哦~\n\n<a href=''http://baidu365.duapp.com/uc/Calendar.html?signature=AMANGO_SIG''>点击进入查询日历</a>";s:7:"replace";s:0:"";}', 'Text', '日历365', 1, 1),
(120, '<Content><![CDATA[<amango:Contentheadtag>请输入“城市+天气”，例如：厦门天气。<amango:Contentendtag>]]></Content>', 'a:4:{s:4:"type";s:4:"text";s:3:"num";i:1;s:6:"neiron";s:55:"请输入“城市+天气”，例如：厦门天气。";s:7:"replace";s:0:"";}', 'Text', '天气查询提示', 1, 1),
(119, '', 'a:4:{s:4:"type";s:5:"local";s:3:"num";i:1;s:6:"neiron";s:2:"13";s:7:"replace";a:1:{s:5:"title";s:6:"借阅";}}', 'Api', '集大借阅', 1, 0),
(136, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>如何迅速定位某个领域的最佳入门书籍<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>工作中或多或少都需要接触一些全新的领域，也不少用户提了些自学上的问题，今天就总结下如何利用互联网工具，快速找到你所要学习领域的最佳入门书籍。<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://jmu.amango.net/Uploads/Picture/2014-09-30/542aa629a037a.jpg]]></PicUrl><Url><![CDATA[http://jmu.amango.net/index.php?s=/Home/Article/detail/id/67/ucusername/P_UCUSERNAME/ucpassword/P_UCPASSWORD/stamp/1412933139.html]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:5:"40,67";s:7:"replace";s:199:",http://jmu.amango.net/Uploads/Picture/2014-09-30/542aa629a037a.jpg,,http://jmu.amango.net/index.php?s=/Home/Article/detail/id/67/ucusername/P_UCUSERNAME/ucpassword/P_UCPASSWORD/stamp/1412933139.html";}', 'Dantw', '图片测试', 1, 1),
(137, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>芒果日报<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>NEWS2<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://jmu.amango.net/Uploads/Picture/2014-09-30/5429860daba95.jpg]]></PicUrl><Url><![CDATA[http://jmu.amango.net/index.php?s=/Home/Article/lists/category/wxarticle.html]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:7:"42,news";s:7:"replace";s:137:"芒果日报,/Uploads/Picture/2014-09-30/5429860daba95.jpg,,http://jmu.amango.net/index.php?s=/Home/Article/lists/category/wxarticle.html";}', 'Dantw', '芒果日报TAG', 1, 0),
(93, '<Content><![CDATA[<amango:Contentheadtag>/害羞收到，小主~<amango:Contentendtag>]]></Content>', 'a:4:{s:4:"type";s:4:"text";s:3:"num";i:1;s:6:"neiron";s:23:"/害羞收到，小主~";s:7:"replace";s:0:"";}', 'Text', '超时回复', 1, 1),
(117, '', 'a:4:{s:4:"type";s:5:"local";s:3:"num";i:1;s:6:"neiron";s:2:"13";s:7:"replace";a:1:{s:5:"title";s:6:"成绩";}}', 'Api', '集大成绩', 1, 0),
(116, '', 'a:4:{s:4:"type";s:5:"local";s:3:"num";i:1;s:6:"neiron";s:2:"13";s:7:"replace";a:1:{i:0;s:0:"";}}', 'Api', '集大课表', 1, 0),
(131, '<Articles><amango:Articlesdantwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>晚安，芒果学长倾情献唱，快戳进来听吧！<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0><amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[http://jmu.amango.net/Uploads/Picture/2014-09-27/5426cbdacdfe6.jpg]]></PicUrl><Url><![CDATA[http://jmu.amango.net/index.php?s=/Home/Article/detail/id/62/ucusername/P_UCUSERNAME/ucpassword/P_UCPASSWORD/stamp/1411829302.html]]></Url></item><amango:Itemendtag0><amango:Articlesdantwendtag></Articles>', 'a:4:{s:4:"type";s:8:"articles";s:3:"num";i:1;s:6:"neiron";s:5:"42,62";s:7:"replace";s:256:"晚安，芒果学长倾情献唱，快戳进来听吧！,http://jmu.amango.net/Uploads/Picture/2014-09-27/5426cbdacdfe6.jpg,,http://jmu.amango.net/index.php?s=/Home/Article/detail/id/62/ucusername/P_UCUSERNAME/ucpassword/P_UCPASSWORD/stamp/1411829302.html";}', 'Dantw', '晚安', 1, 1),
(128, '<Content><![CDATA[<amango:Contentheadtag>我们没有开发机器人插件/大哭\nSo，陪你聊天的都漂亮的妹纸和帅气的汉纸。/呲牙\n如果小编没有及时回复你，有可能是趴在桌子上睡着了。/惊恐<amango:Contentendtag>]]></Content>', 'a:4:{s:4:"type";s:4:"text";s:3:"num";i:1;s:6:"neiron";s:190:"我们没有开发机器人插件/大哭\nSo，陪你聊天的都漂亮的妹纸和帅气的汉纸。/呲牙\n如果小编没有及时回复你，有可能是趴在桌子上睡着了。/惊恐";s:7:"replace";s:0:"";}', 'Text', '调戏小编', 1, 1),
(132, '', 'a:4:{s:4:"type";s:5:"local";s:3:"num";i:1;s:6:"neiron";s:2:"13";s:7:"replace";a:1:{s:5:"title";s:6:"余额";}}', 'Api', '集大余额', 1, 0),
(135, '<Articles><amango:Articlesduotwheadtag><amango:Itemheadtag0><item><Title><![CDATA[<amango:Itemtitleheadtag0>NEWS00<amango:Itemtitleendtag0>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag0>NEWS00<amango:Itemdescriptionendtag0>]]></Description><PicUrl><![CDATA[NEWS01]]></PicUrl><Url><![CDATA[NEWS03]]></Url></item><amango:Itemendtag0><amango:Itemheadtag1><item><Title><![CDATA[<amango:Itemtitleheadtag1>NEWS10<amango:Itemtitleendtag1>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag1>NEWS10<amango:Itemdescriptionendtag1>]]></Description><PicUrl><![CDATA[NEWS11]]></PicUrl><Url><![CDATA[NEWS13]]></Url></item><amango:Itemendtag1><amango:Itemheadtag2><item><Title><![CDATA[<amango:Itemtitleheadtag2>NEWS20<amango:Itemtitleendtag2>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag2>NEWS20<amango:Itemdescriptionendtag2>]]></Description><PicUrl><![CDATA[NEWS21]]></PicUrl><Url><![CDATA[NEWS23]]></Url></item><amango:Itemendtag2><amango:Itemheadtag3><item><Title><![CDATA[<amango:Itemtitleheadtag3>NEWS30<amango:Itemtitleendtag3>]]></Title><Description><![CDATA[<amango:Itemdescriptionheadtag3>NEWS30<amango:Itemdescriptionendtag3>]]></Description><PicUrl><![CDATA[NEWS31]]></PicUrl><Url><![CDATA[NEWS33]]></Url></item><amango:Itemendtag3><amango:Articlesduotwendtag></Articles>', 'a:4:{s:4:"type";s:12:"fastarticles";s:3:"num";i:4;s:6:"neiron";s:7:"40,news";s:7:"replace";s:0:"";}', 'Duotw', '芒果日报', 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `amango_rules`
--

CREATE TABLE IF NOT EXISTS `amango_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `rules_title` varchar(255) NOT NULL COMMENT '规则标识',
  `rules_content` varchar(255) NOT NULL COMMENT '规则内容',
  `sort` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '规则排序',
  `rules_description` text NOT NULL COMMENT '规则描述',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '规则状态',
  `rules_name` text NOT NULL COMMENT '规则名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `amango_rules`
--

INSERT INTO `amango_rules` (`id`, `rules_title`, `rules_content`, `sort`, `rules_description`, `status`, `rules_name`) VALUES
(1, 'last', '/芒果$/', 25, '尾部含有', 1, '尾部含有'),
(2, 'top', '/^芒果/', 24, '', 1, '开头含有'),
(3, 'equel', '/^芒果$/', 26, '', 1, '完全相等'),
(4, 'lastmany', '/(芒果)$/', 0, '', 1, '尾部多个关键词'),
(5, 'topmany', '/^(芒果)/', 0, '', 1, '头部多个关键词'),
(6, 'anywhere', '/(芒果)/', 0, '', 1, '任意位置多个关键词');

-- --------------------------------------------------------

--
-- 表的结构 `amango_tagscate`
--

CREATE TABLE IF NOT EXISTS `amango_tagscate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tagscate_name` varchar(50) NOT NULL COMMENT 'TAG组名称',
  `tagscate_title` varchar(50) NOT NULL COMMENT 'TAG组标识',
  `tagscate_description` text NOT NULL COMMENT 'TAG组描述',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT 'TAG组状态',
  `tagscate_type` char(50) NOT NULL DEFAULT 'text' COMMENT '消息类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=13 ;

--
-- 转存表中的数据 `amango_tagscate`
--

INSERT INTO `amango_tagscate` (`id`, `tagscate_name`, `tagscate_title`, `tagscate_description`, `status`, `tagscate_type`) VALUES
(1, '文本消息尾部', 'Contentend', '在文本消息回复中,消息尾部植入任意内容', 1, 'text'),
(2, '文本消息头部', 'Contenthead', '在文本消息回复中,消息头部植入任意内容', 1, 'text'),
(3, '单图文消息Item头部', 'Itemhead0', '在单图文消息回复中,消息Item头部植入任意内容', 1, 'dantw'),
(4, '单图文消息Item尾部', 'Itemend0', '在单图文消息回复中,消息Item尾部植入任意内容', 1, 'dantw'),
(5, '单图文消息Title头部', 'Itemtitlehead', '在单图文消息回复中,消息Title头部植入任意内容', 1, 'dantw'),
(6, '单图文消息Title尾部', 'Itemtitleend', '在单图文消息回复中,消息Title尾部植入任意内容', 1, 'dantw'),
(7, '单图文消息Description头部', 'Itemdescriptionhead', '在单图文消息回复中,消息Description头部植入任意内容', 1, 'dantw'),
(8, '单图文消息Description尾部', 'ItemDescriptionend', '在单图文消息回复中,消息Description尾部植入任意内容', 1, 'dantw'),
(9, '单图文消息Articles头部', 'Articlesdantwhead', '在单图文消息回复中,消息Articles头部植入任意内容(至顶)', 1, 'dantw'),
(10, '单图文消息Articles尾部', 'Articlesdantwend', '在单图文消息回复中,消息Articles尾部植入任意内容(至底)', 1, 'dantw'),
(11, '多图文消息Articles头部', 'Articlesduotwhead', '在多图文消息回复中,消息Articles头部植入任意内容(至顶)', 1, 'duotw'),
(12, '多图文消息Articles尾部', 'Articlesduotwend', '在多图文消息回复中,消息Articles尾部植入任意内容(至底)', 1, 'duotw');

-- --------------------------------------------------------

--
-- 表的结构 `amango_tagslists`
--

CREATE TABLE IF NOT EXISTS `amango_tagslists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tagslists_title` varchar(50) NOT NULL COMMENT 'TAG标识',
  `tagslists_group` char(50) NOT NULL COMMENT 'TAG分组',
  `tagslists_type` char(50) NOT NULL DEFAULT 'static' COMMENT 'TAG类型',
  `tagslists_action` varchar(255) NOT NULL DEFAULT 'static' COMMENT 'TAG操作',
  `tagslists_param` text NOT NULL COMMENT 'TAG操作参数',
  `tagslists_description` text NOT NULL COMMENT 'TAG描述',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TAG排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `amango_tagslists`
--

INSERT INTO `amango_tagslists` (`id`, `tagslists_title`, `tagslists_group`, `tagslists_type`, `tagslists_action`, `tagslists_param`, `tagslists_description`, `status`, `sort`) VALUES
(2, '单图文尾部植入', 'Itemhead0', 'action', 'Addons://Placement/Weixin/xmltags', 'id:3', '', 0, 0),
(3, '多图文头部', 'Articlesduotwhead', 'action', 'Addons://Placement/Weixin/xmltags', 'id:4', '', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `amango_ucenter_admin`
--

CREATE TABLE IF NOT EXISTS `amango_ucenter_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员用户ID',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_ucenter_app`
--

CREATE TABLE IF NOT EXISTS `amango_ucenter_app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '应用ID',
  `title` varchar(30) NOT NULL COMMENT '应用名称',
  `url` varchar(100) NOT NULL COMMENT '应用URL',
  `ip` char(15) NOT NULL COMMENT '应用IP',
  `auth_key` varchar(100) NOT NULL COMMENT '加密KEY',
  `sys_login` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '同步登陆',
  `allow_ip` varchar(255) NOT NULL COMMENT '允许访问的IP',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '应用状态',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_ucenter_member`
--

CREATE TABLE IF NOT EXISTS `amango_ucenter_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) NOT NULL COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL COMMENT '用户手机',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_ucenter_setting`
--

CREATE TABLE IF NOT EXISTS `amango_ucenter_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型（1-用户配置）',
  `value` text NOT NULL COMMENT '配置数据',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='设置表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_url`
--

CREATE TABLE IF NOT EXISTS `amango_url` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '链接唯一标识',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `short` char(100) NOT NULL DEFAULT '' COMMENT '短网址',
  `status` tinyint(2) NOT NULL DEFAULT '2' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='链接表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `amango_userdata`
--

CREATE TABLE IF NOT EXISTS `amango_userdata` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型标识',
  `target_id` int(10) unsigned NOT NULL COMMENT '目标id',
  UNIQUE KEY `uid` (`uid`,`type`,`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `amango_webuntil`
--

CREATE TABLE IF NOT EXISTS `amango_webuntil` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `webuntil_name` varchar(255) NOT NULL COMMENT '接口名称',
  `webuntil_title` varchar(100) NOT NULL COMMENT '接口标识',
  `webuntil_param` text NOT NULL COMMENT '附属参数',
  `webuntil_backtype` char(50) NOT NULL DEFAULT 'xml' COMMENT '返回类型',
  `webuntil_sigtype` char(50) NOT NULL DEFAULT 'no' COMMENT '关键词处理',
  `webuntil_cache` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数据缓存',
  `webuntil_tag` tinyint(2) NOT NULL DEFAULT '0' COMMENT '植入TAG',
  `webuntil_url` varchar(255) NOT NULL COMMENT '请求URL',
  `webuntil_token` varchar(255) NOT NULL COMMENT 'TOKEN值',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `webuntil_type` char(50) NOT NULL DEFAULT 'post' COMMENT '请求类型',
  `fromusername` varchar(255) NOT NULL COMMENT '用户openid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `amango_webuntil`
--

INSERT INTO `amango_webuntil` (`id`, `webuntil_name`, `webuntil_title`, `webuntil_param`, `webuntil_backtype`, `webuntil_sigtype`, `webuntil_cache`, `webuntil_tag`, `webuntil_url`, `webuntil_token`, `status`, `webuntil_type`, `fromusername`) VALUES
(1, '芒果测试api', 'amango', 'name:大大大\r\ntype:天气\r\nnum:131313', 'xml', 'yes', 0, 0, 'http://1.amangoapi.sinaapp.com/', 'amango', 1, 'get', ''),
(2, '正方接口', 'zhengfan', '', 'xml', 'no', 0, 0, 'http://weciyo.com/zhengfang/api.php', 'weixin', 1, 'post', ''),
(4, '云端微信', 'weixinyunduan', '', 'xml', 'no', 0, 0, 'http://www.weixinyunduan.com/mpapi.html?appid=8457', '75f38e43ac03db8a1767014c15453781', 1, 'post', ''),
(5, '微盟', 'weimob', '', 'xml', 'no', 0, 0, 'http://api.weimob.com/api?t=546154f8a4afa0ef2ebf211afdf7555a==E', '945989_w', 1, 'post', ''),
(7, '掌大', 'zhangda', '', 'xml', 'no', 0, 0, 'http://www.wxhand.com/wx/index.php/api/kzhbxf1408469317', 'kzhbxf1408469317', 1, 'post', ''),
(9, '乐享', 'wxapi', '', 'xml', 'no', 0, 0, 'http://demo.wxapi.cn/i.php?&i=38971&tk=691100814be27adbc3ea590bd662ff53', '3e121c17c83c5b32459e53710482b504', 1, 'post', '');

-- --------------------------------------------------------

--
-- 表的结构 `amango_weixinmember`
--

CREATE TABLE IF NOT EXISTS `amango_weixinmember` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `fromusername` varchar(255) NOT NULL COMMENT '用户openid',
  `nickname` varchar(255) NOT NULL DEFAULT '游客' COMMENT '用户昵称',
  `sex` tinyint(2) NOT NULL DEFAULT '1' COMMENT '性别',
  `birthday` varchar(20) NOT NULL COMMENT '生日日期',
  `qq` int(14) unsigned NOT NULL COMMENT 'QQ',
  `follow` tinyint(2) NOT NULL DEFAULT '1' COMMENT '关注状态',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `regtime` int(13) unsigned NOT NULL COMMENT '注册日期',
  `lasttime` int(13) unsigned NOT NULL COMMENT '活动时间',
  `lastkeyword` varchar(150) NOT NULL COMMENT '上级关键词',
  `lastmodel` varchar(255) NOT NULL COMMENT '上级模块',
  `ucmember` varchar(255) NOT NULL COMMENT 'UC注册ID',
  `ucpassword` varchar(255) NOT NULL COMMENT 'Uc密码',
  `ucusername` varchar(255) NOT NULL COMMENT 'Uc用户名',
  `cate_group` varchar(255) NOT NULL DEFAULT 'general' COMMENT '用户分组',
  `lastclick` varchar(255) NOT NULL COMMENT '个性菜单',
  `location` varchar(255) NOT NULL COMMENT '所在位置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
