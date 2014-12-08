--
-- 更新分组字段类型
--
alter table `amango_category` MODIFY `icon` VARCHAR(200);
--
-- 更新配置 默认模块 
--
INSERT INTO `amango_config` (`id`, `name`, `type`, `title`, `group`, `extra`, `remark`, `create_time`, `update_time`, `status`, `value`, `sort`) VALUES
('', 'WEB_SITE_THEME', 1, '前台主题', 1, '', '网站前台默认主题', 1417573402, 1417573429, 1, 'default', 0),
('', 'WEB_SITE_DEFAULTINDEX', 0, '首页默认模板', 1, '', '默认为：Index/index', 1417753239, 1417753239, 1, 'Index/index', 0);
--
-- 更新菜单 主题模块
--
INSERT INTO `amango_menu` (`id`, `title`, `pid`, `sort`, `url`, `hide`, `tip`, `group`, `is_dev`) VALUES
('', '安装主题', 93, 0, 'Theme/index', 0, '', '主题管理', 0),
('', '页面模板编辑', 93, 0, 'Theme/edit', 0, '', '主题管理', 0),
('', '自定义模板', 93, 0, 'Theme/tpllists', 1, '', '页面管理', 0);
--
-- 更新菜单 93模块改为模板模块
--
update `amango_menu` set `title`='主题模板' where `id`='93' ; 
update `amango_menu` set `hide`='0' where `id`='93' ; 
update `amango_menu` set `url`='Theme/index' where `id`='93' ; 
--
-- 开启资源管理模块
--
INSERT INTO `amango_menu` (`id`, `title`, `pid`, `sort`, `url`, `hide`, `tip`, `group`, `is_dev`) VALUES
('', '本地上传', 147, 0, 'File/lists', 0, '', '资源管理', 0);