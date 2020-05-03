-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 30 2020 г., 10:43
-- Версия сервера: 5.7.20
-- Версия PHP: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `attachments`
--

DROP TABLE IF EXISTS `attachments`;
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `classname` varchar(255) NOT NULL DEFAULT '' COMMENT 'Parent class name',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `image` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Is image',
  `realname` varchar(255) NOT NULL DEFAULT '0' COMMENT 'Real file name',
  `filename` varchar(255) NOT NULL DEFAULT '0' COMMENT 'Original file name',
  `mime` varchar(255) NOT NULL DEFAULT '0' COMMENT 'MIME-type',
  PRIMARY KEY (`id`),
  KEY `classname` (`classname`,`pid`),
  KEY `classname_2` (`classname`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='Attachments';

--
-- Дамп данных таблицы `attachments`
--

INSERT INTO `attachments` (`id`, `classname`, `pid`, `image`, `realname`, `filename`, `mime`) VALUES
(51, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(50, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_13.pdf', 'test.pdf', 'application/binary'),
(49, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_12.doc', 'test.doc', 'application/binary'),
(48, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(47, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_11.pdf', 'test.pdf', 'application/binary'),
(46, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_10.doc', 'test.doc', 'application/binary'),
(45, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(44, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_9.pdf', 'test.pdf', 'application/binary'),
(43, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_8.doc', 'test.doc', 'application/binary'),
(42, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(41, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_7.pdf', 'test.pdf', 'application/binary'),
(40, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_6.doc', 'test.doc', 'application/binary'),
(39, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(38, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_5.pdf', 'test.pdf', 'application/binary'),
(37, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_4.doc', 'test.doc', 'application/binary'),
(36, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(35, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_3.pdf', 'test.pdf', 'application/binary'),
(34, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_2.doc', 'test.doc', 'application/binary'),
(33, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(32, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(31, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(30, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(29, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_1.pdf', 'test.pdf', 'application/binary'),
(28, 'RAAS\\CMS\\Material_Field', 29, 0, 'test.doc', 'test.doc', 'application/binary'),
(27, 'RAAS\\CMS\\Page_Field', 2, 1, '0', '0', 'application/octet-stream'),
(26, 'RAAS\\CMS\\Page_Field', 2, 1, '0', '0', 'application/octet-stream'),
(25, 'RAAS\\CMS\\Page_Field', 2, 1, '0', '0', 'application/octet-stream'),
(24, 'RAAS\\CMS\\Page_Field', 2, 1, '0', '0', 'application/octet-stream'),
(23, 'RAAS\\CMS\\Page_Field', 2, 1, '0', '0', 'application/octet-stream'),
(22, 'RAAS\\CMS\\Page_Field', 2, 1, '0', '0', 'application/octet-stream'),
(21, 'RAAS\\CMS\\Page_Field', 2, 1, '0', '0', 'application/octet-stream'),
(20, 'RAAS\\CMS\\Page_Field', 2, 1, '0', '0', 'application/octet-stream'),
(19, 'RAAS\\CMS\\Page_Field', 2, 1, '0', '0', 'application/octet-stream'),
(18, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(17, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(16, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(15, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(14, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(13, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(12, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(11, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(10, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(9, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(8, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(7, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(6, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(5, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(4, 'RAAS\\CMS\\Material_Field', 17, 1, '0', '0', 'application/octet-stream'),
(3, 'RAAS\\CMS\\Material_Field', 15, 1, '0', '0', 'application/octet-stream'),
(2, 'RAAS\\CMS\\Material_Field', 15, 1, '0', '0', 'application/octet-stream'),
(1, 'RAAS\\CMS\\Material_Field', 15, 1, '0', '0', 'application/octet-stream'),
(52, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_14.doc', 'test.doc', 'application/binary'),
(53, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_15.pdf', 'test.pdf', 'application/binary'),
(54, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(55, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_16.doc', 'test.doc', 'application/binary'),
(56, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_17.pdf', 'test.pdf', 'application/binary'),
(57, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream'),
(58, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_18.doc', 'test.doc', 'application/binary'),
(59, 'RAAS\\CMS\\Material_Field', 29, 0, 'test_19.pdf', 'test.pdf', 'application/binary'),
(60, 'RAAS\\CMS\\Material_Field', 27, 1, '0', '0', 'application/octet-stream');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_access`
--

DROP TABLE IF EXISTS `cms_access`;
CREATE TABLE IF NOT EXISTS `cms_access` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  `material_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  `block_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  `allow` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1 - allow, 0 - deny',
  `to_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'To (type)',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `gid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Group ID#',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `material_id` (`material_id`),
  KEY `block_id` (`block_id`),
  KEY `uid` (`uid`),
  KEY `gid` (`gid`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Site access';

--
-- Дамп данных таблицы `cms_access`
--

INSERT INTO `cms_access` (`id`, `page_id`, `material_id`, `block_id`, `allow`, `to_type`, `uid`, `gid`, `priority`) VALUES
(1, 4, 0, 0, 0, 0, 0, 0, 0),
(2, 4, 0, 0, 1, 4, 0, 1, 1),
(3, 0, 0, 19, 0, 0, 0, 0, 0),
(4, 0, 0, 19, 1, 3, 2, 0, 1),
(5, 0, 7, 0, 0, 0, 0, 0, 0),
(6, 0, 7, 0, 1, 3, 1, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_access_blocks_cache`
--

DROP TABLE IF EXISTS `cms_access_blocks_cache`;
CREATE TABLE IF NOT EXISTS `cms_access_blocks_cache` (
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `block_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  `allow` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1 - allow, 0 - deny',
  PRIMARY KEY (`uid`,`block_id`),
  KEY `uid` (`uid`),
  KEY `block_id` (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Blocks access cache';

--
-- Дамп данных таблицы `cms_access_blocks_cache`
--

INSERT INTO `cms_access_blocks_cache` (`uid`, `block_id`, `allow`) VALUES
(0, 19, 0),
(1, 19, 0),
(2, 19, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_access_materials_cache`
--

DROP TABLE IF EXISTS `cms_access_materials_cache`;
CREATE TABLE IF NOT EXISTS `cms_access_materials_cache` (
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `material_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  `allow` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1 - allow, 0 - deny',
  PRIMARY KEY (`uid`,`material_id`),
  KEY `uid` (`uid`),
  KEY `material_id` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Materials access cache';

--
-- Дамп данных таблицы `cms_access_materials_cache`
--

INSERT INTO `cms_access_materials_cache` (`uid`, `material_id`, `allow`) VALUES
(0, 7, 0),
(1, 7, 1),
(2, 7, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_access_pages_cache`
--

DROP TABLE IF EXISTS `cms_access_pages_cache`;
CREATE TABLE IF NOT EXISTS `cms_access_pages_cache` (
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  `allow` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1 - allow, 0 - deny',
  PRIMARY KEY (`uid`,`page_id`),
  KEY `uid` (`uid`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pages access cache';

--
-- Дамп данных таблицы `cms_access_pages_cache`
--

INSERT INTO `cms_access_pages_cache` (`uid`, `page_id`, `allow`) VALUES
(0, 4, 0),
(1, 4, 1),
(2, 4, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks`
--

DROP TABLE IF EXISTS `cms_blocks`;
CREATE TABLE IF NOT EXISTS `cms_blocks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `location` varchar(255) NOT NULL DEFAULT '' COMMENT 'Location',
  `vis` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Visibility',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  `modify_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  `author_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  `editor_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Editor ID#',
  `block_type` varchar(255) NOT NULL DEFAULT 'RAAS\\CMS\\Block_HTML' COMMENT 'Block type',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `inherit` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Inherit',
  `nat` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Translate address',
  `params` varchar(255) NOT NULL DEFAULT '' COMMENT 'Additional params',
  `interface_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Interface ID#',
  `widget_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Widget ID#',
  `cache_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cache type',
  `cache_single_page` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cache by single pages',
  `cache_interface_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cache interface_id',
  `vis_material` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Visibility by material',
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `editor_id` (`editor_id`),
  KEY `cache_interface_id` (`cache_interface_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COMMENT='Site pages';

--
-- Дамп данных таблицы `cms_blocks`
--

INSERT INTO `cms_blocks` (`id`, `location`, `vis`, `post_date`, `modify_date`, `author_id`, `editor_id`, `block_type`, `name`, `inherit`, `nat`, `params`, `interface_id`, `widget_id`, `cache_type`, `cache_single_page`, `cache_interface_id`, `vis_material`) VALUES
(1, 'logo', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Логотип', 1, 1, '', 0, 15, 0, 0, 0, 0),
(2, 'contacts_top', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Контакты', 1, 1, '', 0, 0, 0, 0, 0, 0),
(3, 'contacts_top', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Социальные сети', 1, 1, '', 0, 0, 0, 0, 0, 0),
(4, 'copyrights', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Копирайты', 1, 1, '', 0, 0, 0, 0, 0, 0),
(5, 'copyrights', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Privacy', 1, 1, '', 0, 0, 0, 0, 0, 0),
(6, 'footer_counters', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_Form', 'Обратная связь', 1, 1, '', 2, 11, 0, 0, 0, 0),
(7, 'footer_counters', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_Form', 'Заказать звонок', 1, 1, '', 2, 13, 0, 0, 0, 0),
(8, 'footer_counters', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Яндекс.Метрика', 1, 1, '', 0, 0, 0, 0, 0, 0),
(9, 'head_counters', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Google Analytics', 1, 1, '', 0, 0, 0, 0, 0, 0),
(10, 'footer_counters', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Триггеры', 1, 1, '', 0, 0, 0, 0, 0, 0),
(11, 'footer_counters', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Уведомление о Cookies', 1, 1, '', 0, 0, 0, 0, 0, 0),
(12, 'content', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Добро пожаловать', 0, 1, '', 0, 0, 0, 0, 0, 0),
(13, 'content', 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 1, 1, 'RAAS\\CMS\\Block_Material', 'Наши преимущества', 0, 0, '', 1, 16, 0, 0, 0, 0),
(14, 'menu_top', 1, '2018-04-05 11:33:48', '2018-04-05 11:33:48', 1, 1, 'RAAS\\CMS\\Block_Menu', 'Верхнее меню', 1, 1, '', 3, 20, 1, 0, 6, 0),
(15, 'menu_bottom', 1, '2018-04-05 11:33:48', '2018-04-05 11:33:48', 1, 1, 'RAAS\\CMS\\Block_Menu', 'Нижнее меню', 1, 1, '', 3, 21, 1, 0, 6, 0),
(16, 'banners', 1, '2018-04-05 11:33:48', '2018-04-05 11:33:48', 1, 1, 'RAAS\\CMS\\Block_Material', 'Баннеры', 0, 0, '', 1, 9, 0, 0, 0, 0),
(17, 'content', 1, '2018-04-05 11:34:14', '2018-04-05 11:34:14', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Текстовый блок', 0, 1, '', 0, 0, 0, 0, 0, 0),
(18, 'content', 1, '2018-04-05 11:34:14', '2018-04-05 11:34:14', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Текстовый блок', 0, 1, '', 0, 0, 0, 0, 0, 0),
(19, 'content', 1, '2018-04-05 11:34:14', '2018-04-22 09:49:11', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Текстовый блок', 0, 1, '', 0, 0, 0, 0, 0, 0),
(20, 'content', 1, '2018-04-05 11:34:14', '2018-04-05 11:34:14', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Текстовый блок', 0, 1, '', 0, 0, 0, 0, 0, 0),
(21, 'content', 1, '2018-04-05 11:34:14', '2018-04-05 11:34:14', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Текстовый блок', 0, 1, '', 0, 0, 0, 0, 0, 0),
(22, 'content', 1, '2018-04-05 11:34:14', '2018-04-05 11:34:14', 1, 1, 'RAAS\\CMS\\Block_Material', 'Новости', 0, 1, '', 1, 22, 0, 0, 0, 0),
(23, 'left', 1, '2018-04-05 11:34:14', '2018-04-05 11:34:14', 1, 1, 'RAAS\\CMS\\Block_Material', 'Новости', 1, 0, '', 1, 23, 0, 0, 0, 0),
(24, 'content', 1, '2018-04-05 11:34:15', '2018-04-05 11:34:15', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Карта', 0, 1, '', 0, 0, 0, 0, 0, 0),
(25, 'content', 1, '2018-04-05 11:34:15', '2018-04-05 11:34:15', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Контакты', 0, 1, '', 0, 0, 0, 0, 0, 0),
(26, 'content', 1, '2018-04-05 11:34:15', '2018-04-05 11:34:15', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Обратная связь', 0, 1, '', 0, 0, 0, 0, 0, 0),
(27, 'content', 1, '2018-04-05 11:34:15', '2018-04-22 13:59:43', 1, 1, 'RAAS\\CMS\\Block_Form', 'Обратная связь', 0, 1, '', 2, 10, 2, 0, 0, 0),
(28, 'content', 1, '2018-04-05 11:34:15', '2018-04-05 11:34:15', 1, 1, 'RAAS\\CMS\\Block_HTML', '404 — страница не найдена', 0, 1, '', 0, 0, 0, 0, 0, 0),
(29, 'content', 1, '2018-04-05 11:34:15', '2018-04-05 11:34:15', 1, 1, 'RAAS\\CMS\\Block_Menu', 'Карта сайта', 0, 1, '', 3, 19, 0, 0, 0, 0),
(30, '', 1, '2018-04-05 11:34:15', '2018-04-05 11:34:15', 1, 1, 'RAAS\\CMS\\Block_PHP', 'sitemap.xml', 0, 1, '', 0, 14, 0, 0, 0, 0),
(31, '', 1, '2018-04-05 11:34:16', '2018-04-05 11:34:16', 1, 1, 'RAAS\\CMS\\Block_HTML', 'robots.txt', 0, 1, '', 0, 17, 0, 0, 0, 0),
(32, '', 1, '2018-04-05 11:34:16', '2018-04-05 11:34:16', 1, 1, 'RAAS\\CMS\\Block_HTML', 'custom.css', 0, 1, '', 0, 18, 0, 0, 0, 0),
(33, 'share', 1, '2018-04-05 11:34:16', '2018-04-05 11:34:16', 1, 1, 'RAAS\\CMS\\Block_HTML', 'Поделиться', 1, 1, '', 0, 0, 0, 0, 0, 0),
(34, 'content', 1, '2018-04-05 11:34:27', '2019-03-29 14:53:25', 1, 1, 'RAAS\\CMS\\Block_Material', 'Каталог продукции', 1, 1, 'metaTemplates=template&withChildrenGoods=1&commentFormBlock=52&commentsListBlock=51&faqFormBlock=53&faqListBlock=50', 31, 37, 2, 0, 6, 0),
(35, 'content', 1, '2018-04-05 11:34:27', '2018-04-05 11:34:27', 1, 1, 'RAAS\\CMS\\Block_Material', 'Каталог продукции', 0, 0, '', 31, 37, 0, 0, 0, 0),
(36, 'content', 1, '2018-04-05 11:34:27', '2018-04-05 11:34:27', 1, 1, 'RAAS\\CMS\\Block_PHP', 'Спецпредложение', 0, 1, '', 0, 43, 0, 0, 0, 0),
(37, 'left', 1, '2018-04-05 11:34:27', '2018-04-05 11:34:27', 1, 1, 'RAAS\\CMS\\Block_Menu', 'Левое меню', 1, 1, '', 3, 41, 1, 0, 6, 0),
(38, 'content', 1, '2018-04-05 11:34:28', '2018-04-05 11:34:28', 1, 1, 'RAAS\\CMS\\Shop\\Block_Cart', 'Корзина', 0, 1, '', 24, 32, 0, 0, 0, 0),
(39, 'left', 1, '2018-04-05 11:34:28', '2018-04-05 11:34:28', 1, 1, 'RAAS\\CMS\\Block_PHP', 'Информер корзины', 1, 1, '', 0, 39, 0, 0, 0, 0),
(40, '', 1, '2018-04-05 11:34:28', '2018-04-05 11:34:28', 1, 1, 'RAAS\\CMS\\Shop\\Block_Cart', 'Корзина', 0, 1, '', 24, 32, 0, 0, 0, 0),
(41, 'content', 1, '2018-04-05 11:34:29', '2018-04-05 11:34:29', 1, 1, 'RAAS\\CMS\\Shop\\Block_Cart', 'Корзина', 0, 1, '', 24, 32, 0, 0, 0, 0),
(42, 'left', 1, '2018-04-05 11:34:29', '2018-04-05 11:34:29', 1, 1, 'RAAS\\CMS\\Block_PHP', 'Информер избранного', 1, 1, '', 0, 40, 0, 0, 0, 0),
(43, '', 1, '2018-04-05 11:34:29', '2018-04-05 11:34:29', 1, 1, 'RAAS\\CMS\\Shop\\Block_Cart', 'Корзина', 0, 1, '', 24, 32, 0, 0, 0, 0),
(44, '', 1, '2018-04-05 11:34:30', '2018-04-05 11:34:30', 1, 1, 'RAAS\\CMS\\Shop\\Block_YML', 'Яндекс.Маркет', 0, 1, '', 28, 34, 0, 0, 0, 0),
(45, 'content', 1, '2018-04-22 09:45:26', '2018-04-22 09:45:26', 1, 1, 'RAAS\\CMS\\Users\\Block_Register', 'Регистрация', 0, 1, '', 45, 51, 0, 0, 0, 0),
(46, 'content', 1, '2018-04-22 09:45:26', '2018-04-22 09:45:26', 1, 1, 'RAAS\\CMS\\Users\\Block_LogIn', 'Вход в систему', 0, 1, '', 47, 53, 0, 0, 0, 0),
(47, 'content', 1, '2018-04-22 09:45:26', '2018-04-22 09:45:26', 1, 1, 'RAAS\\CMS\\Users\\Block_Recovery', 'Восстановление пароля', 0, 1, '', 48, 54, 0, 0, 0, 0),
(48, 'content', 1, '2018-04-22 09:45:26', '2018-04-22 09:45:26', 1, 1, 'RAAS\\CMS\\Users\\Block_Activation', 'Активация', 0, 1, '', 46, 52, 0, 0, 0, 0),
(49, 'banners', 1, '2018-04-22 12:54:10', '2018-04-22 12:54:10', 1, 1, 'RAAS\\CMS\\Block_Material', 'Баннеры', 0, 0, '', 8, 8, 0, 0, 0, 0),
(50, 'content', 0, '2019-03-29 14:19:56', '2019-03-29 14:20:27', 1, 1, 'RAAS\\CMS\\Block_Material', 'Вопрос-ответ к товарам', 0, 0, '', 1, 55, 0, 0, 0, 0),
(51, 'content', 0, '2019-03-29 14:20:16', '2019-03-29 14:20:29', 1, 1, 'RAAS\\CMS\\Block_Material', 'Отзывы к товарам', 0, 0, '', 1, 56, 0, 0, 0, 0),
(52, 'content', 0, '2019-03-29 14:20:49', '2019-03-29 14:20:49', 1, 1, 'RAAS\\CMS\\Block_Form', 'Отзывы к товарам', 0, 1, '', 2, 10, 0, 0, 0, 0),
(53, 'content', 0, '2019-03-29 14:20:57', '2019-03-29 14:20:57', 1, 1, 'RAAS\\CMS\\Block_Form', 'Вопрос-ответ к товарам', 0, 1, '', 2, 10, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_form`
--

DROP TABLE IF EXISTS `cms_blocks_form`;
CREATE TABLE IF NOT EXISTS `cms_blocks_form` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `form` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Form ID#',
  PRIMARY KEY (`id`),
  KEY `form` (`form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Form blocks';

--
-- Дамп данных таблицы `cms_blocks_form`
--

INSERT INTO `cms_blocks_form` (`id`, `form`) VALUES
(6, 1),
(27, 1),
(7, 2),
(53, 5),
(52, 6);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_html`
--

DROP TABLE IF EXISTS `cms_blocks_html`;
CREATE TABLE IF NOT EXISTS `cms_blocks_html` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `description` text COMMENT 'Text',
  `wysiwyg` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'WYSIWYG editor on',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='HTML blocks';

--
-- Дамп данных таблицы `cms_blocks_html`
--

INSERT INTO `cms_blocks_html` (`id`, `description`, `wysiwyg`) VALUES
(1, '<a href=\"/\" class=\"logo\">\r\n  <span class=\"logo__image\"><img alt=\"\" src=\"/files/cms/common/image/logo.png\" /></span>\r\n  <span class=\"logo__text\">\r\n    <span class=\"logo__name\">Наша компания</span>\r\n    <span class=\"logo__slogan\">Производим все, что угодно!</span>\r\n  </span>\r\n</a>\r\n', 1),
(2, '<div class=\"contacts-top\" itemscope itemtype=\"http://schema.org/Organization\">\r\n  <meta itemprop=\"name\" content=\"\" />\r\n  <div class=\"contacts-top__phone\" itemprop=\"telephone\"><a href=\"tel:%2B79990000000\"><span>(999)</span> 000-00-00</a></div>\r\n  <div class=\"contacts-top__address\" itemprop=\"address\" itemscope itemtype=\"http://schema.org/PostalAddress\">\r\n    <span itemprop=\"addressLocality\">г. Город</span>,\r\n    <span itemprop=\"streetAddress\">ул. Центральная, 0</span>, оф. 123\r\n  </div>\r\n</div>\r\n', 0),
(3, '<div class=\"socials-top\">\r\n  <span class=\"socials-top__title\">Мы в социальных сетях:</span>\r\n  <a href=\"https://vk.com/\" class=\"fa fa-vk\" title=\"ВКонтакте\" target=\"_blank\">&nbsp;</a>\r\n  <a href=\"https://www.facebook.com/groups/\" class=\"fa fa-facebook\" title=\"Facebook\" target=\"_blank\">&nbsp;</a>\r\n  <a href=\"https://instagram.com/\" class=\"fa fa-instagram\" title=\"Instagram\" target=\"_blank\">&nbsp;</a>\r\n</div>\r\n', 0),
(4, '<div class=\"copyrights\">&copy; Компания, 2015. Все права защищены.</div>\r\n', 1),
(5, '<div class=\"privacy\">Совершая любые действия на сайте, вы подтверждаете свое согласие&nbsp;на <a href=\"/privacy/\">обработку персональных данных в соответствии с политикой сайта</a>. Сайт может использовать технологию cookies для сбора информации технического характера. Для персонифицированной работы сайта мы также можем обрабатывать IP-адрес региона вашего местоположения.</div>\r\n', 1),
(8, '', 0),
(9, '', 0),
(10, '<div class=\"triggers\">\r\n  <a class=\"triggers__trigger triggers__trigger_totop scrollTo\" href=\"#top\" title=\"Наверх\"></a>\r\n  <a class=\"triggers__trigger triggers__trigger_order-call\" data-target=\"#order_call_modal\" data-toggle=\"modal\" href=\"#\" title=\"Заказать звонок\"></a>\r\n  <a class=\"triggers__trigger triggers__trigger_feedback\" data-target=\"#feedback_modal\" data-toggle=\"modal\" href=\"#\" title=\"Написать письмо\"></a>\r\n</div>\r\n<script src=\"/js/triggers.js\"></script>\r\n', 0),
(11, '<div class=\"cookies-notification\">\r\n  <a href=\"#\" class=\"cookies-notification__close\"></a>\r\n  <p>\r\n    На нашем сайте мы используем cookie для сбора информации технического характера. В частности, для персонифицированной работы сайта мы обрабатываем IP-адрес региона вашего местоположения. <br />\r\n    Совершая любые действия на сайте, вы <a href=\"/privacy/\" target=\"_blank\">соглашаетесь с политикой обработки персональных данных</a>\r\n  </p>\r\n</div>\r\n<script src=\"/js/cookies-notification.js\"></script>\r\n', 0),
(12, '<h1>Добро пожаловать!</h1>\r\n<p>Извините, страница находится в стадии наполнения.</p>\r\n', 1),
(17, '<p>Извините, раздел находится в стадии наполнения</p>', 1),
(18, '<p>Извините, раздел находится в стадии наполнения</p>', 1),
(19, '<p>Извините, раздел находится в стадии наполнения</p>', 1),
(20, '<p>Извините, раздел находится в стадии наполнения</p>', 1),
(21, '<p>Извините, раздел находится в стадии наполнения</p>', 1),
(24, '<div class=\"map\">\r\n  <script type=\"text/javascript\" charset=\"utf-8\" src=\"//api-maps.yandex.ru/services/constructor/1.0/js/?sid=ac2qYbmG3G-Jl487_Mu2VedJiQSpaZLo&amp;width=100%25&amp;height=300&amp;lang=ru_RU&amp;sourceType=constructor&amp;scroll=true\"></script>\r\n</div>\r\n', 0),
(25, '<div class=\"contacts\">\r\n  <div itemscope itemtype=\"http://schema.org/Organization\">\r\n    <meta itemprop=\"name\" content=\"\" />\r\n    <p>\r\n      <strong>Адрес: </strong>\r\n      <span itemprop=\"address\" itemscope itemtype=\"http://schema.org/PostalAddress\">\r\n        <span itemprop=\"postalCode\">620000</span>,\r\n        <span itemprop=\"addressLocality\">г. Екатеринбург</span>,\r\n        <span itemprop=\"streetAddress\">ул. ..., 0</span>, оф. 0 (0 этаж)\r\n      </span>\r\n    </p>\r\n    <p>\r\n      <strong>Телефон: </strong>\r\n      <a href=\"tel:%2B79990000000\" itemprop=\"telephone\">+7 999 000-00-00</a>\r\n    </p>\r\n    <p>\r\n      <strong>E-mail:&nbsp;</strong>\r\n      <a href=\"mailto:info@test.org\" itemprop=\"email\">info@test.org</a>\r\n    </p>\r\n    <p>\r\n      <strong>Режим работы:&nbsp;</strong>\r\n      09:00 - 18:00\r\n    </p>\r\n    <p>\r\n      <strong>Транспорт:&nbsp;</strong>\r\n      ...\r\n    </p>\r\n  </div>\r\n</div>\r\n', 0),
(26, '<h3>Обратная связь</h3>', 1),
(28, 'Извините, запрашиваемая Вами страница не найдена. Если вы уверены, что эта страница должна существовать, обратитесь к администрации сайта.', 1),
(31, 'User-agent: *\r\nDisallow: /404/\r\nDisallow: /admin/\r\nDisallow: /ajax/\r\nDisallow: /compare/\r\nDisallow: /cart/\r\nDisallow: /cache/\r\nDisallow: /cron/\r\nDisallow: /files/*_tn\r\nDisallow: /files/*_small\r\nDisallow: /files/*_inline\r\nDisallow: /fonts/\r\nDisallow: /images/\r\nDisallow: /inc/\r\nDisallow: /modules/\r\nDisallow: /price/\r\nDisallow: /search/\r\nDisallow: /sitemaps/\r\nDisallow: /system/\r\nDisallow: /yml/\r\nDisallow: *print=1\r\nClean-param: sort&order&print&price_from&price_to\r\n\r\nUser-agent: Yandex\r\nDisallow: /404/\r\nDisallow: /admin/\r\nDisallow: /ajax/\r\nDisallow: /compare/\r\nDisallow: /cart/\r\nDisallow: /cache/\r\nDisallow: /cron/\r\nDisallow: /files/*_tn\r\nDisallow: /files/*_small\r\nDisallow: /files/*_inline\r\nDisallow: /fonts/\r\nDisallow: /images/\r\nDisallow: /inc/\r\nDisallow: /modules/\r\nDisallow: /price/\r\nDisallow: /search/\r\nDisallow: /sitemaps/\r\nDisallow: /system/\r\nDisallow: /yml/\r\nDisallow: *print=1\r\n\r\nHost: pneumatic-tools\r\nSitemap: http://pneumatic-tools/sitemap.xml', 0),
(32, '', 0),
(33, '<div class=\"share\">\r\n  <script src=\"//yastatic.net/es5-shims/0.0.2/es5-shims.min.js\"></script>\r\n  <script src=\"//yastatic.net/share2/share.js\"></script>\r\n  <div class=\"ya-share2\" data-services=\"vkontakte,facebook,twitter,gplus,whatsapp\"></div>\r\n</div>\r\n', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_material`
--

DROP TABLE IF EXISTS `cms_blocks_material`;
CREATE TABLE IF NOT EXISTS `cms_blocks_material` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `material_type` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `pages_var_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
  `rows_per_page` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Rows per page',
  `sort_var_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Sorting var name',
  `order_var_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Order var name',
  `sort_field_default` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field for sorting by default',
  `sort_order_default` varchar(255) NOT NULL DEFAULT '' COMMENT 'Default order',
  `legacy` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Redirect legacy addresses',
  PRIMARY KEY (`id`),
  KEY `material_type` (`material_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Material blocks';

--
-- Дамп данных таблицы `cms_blocks_material`
--

INSERT INTO `cms_blocks_material` (`id`, `material_type`, `pages_var_name`, `rows_per_page`, `sort_var_name`, `order_var_name`, `sort_field_default`, `sort_order_default`, `legacy`) VALUES
(13, 1, 'page', 0, '', '', 'post_date', 'asc', 0),
(16, 2, 'page', 0, '', '', 'post_date', 'asc', 0),
(22, 3, 'page', 20, '', '', '16', 'desc!', 0),
(23, 3, '', 3, '', '', '16', 'desc!', 0),
(34, 4, 'page', 20, '', '', 'name', 'asc', 0),
(35, 4, '', 0, '', '', 'price', 'asc', 0),
(49, 2, 'page', 20, '', '', 'name', 'asc!', 0),
(50, 6, 'page', 20, '', '', '50', 'asc!', 0),
(51, 7, 'page', 20, '', '', 'name', 'asc!', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_material_filter`
--

DROP TABLE IF EXISTS `cms_blocks_material_filter`;
CREATE TABLE IF NOT EXISTS `cms_blocks_material_filter` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `var` varchar(255) NOT NULL DEFAULT '' COMMENT 'Variable',
  `relation` enum('=','LIKE','CONTAINED','FULLTEXT','<=','>=') NOT NULL DEFAULT '=' COMMENT 'Relation',
  `field` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  KEY `id` (`id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Material blocks filtering';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_material_sort`
--

DROP TABLE IF EXISTS `cms_blocks_material_sort`;
CREATE TABLE IF NOT EXISTS `cms_blocks_material_sort` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `var` varchar(255) NOT NULL DEFAULT '' COMMENT 'Variable',
  `field` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field',
  `relation` enum('asc!','desc!','asc','desc') NOT NULL DEFAULT 'asc!' COMMENT 'Relation',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  KEY `id` (`id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Material blocks sorting';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_menu`
--

DROP TABLE IF EXISTS `cms_blocks_menu`;
CREATE TABLE IF NOT EXISTS `cms_blocks_menu` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `menu` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Menu ID#',
  `full_menu` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Full menu',
  PRIMARY KEY (`id`),
  KEY `menu` (`menu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Menu blocks';

--
-- Дамп данных таблицы `cms_blocks_menu`
--

INSERT INTO `cms_blocks_menu` (`id`, `menu`, `full_menu`) VALUES
(14, 1, 1),
(15, 2, 1),
(29, 3, 1),
(37, 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_pages_assoc`
--

DROP TABLE IF EXISTS `cms_blocks_pages_assoc`;
CREATE TABLE IF NOT EXISTS `cms_blocks_pages_assoc` (
  `block_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (`block_id`,`page_id`),
  KEY `block_id` (`block_id`),
  KEY `page_id` (`page_id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Blocks to pages associations';

--
-- Дамп данных таблицы `cms_blocks_pages_assoc`
--

INSERT INTO `cms_blocks_pages_assoc` (`block_id`, `page_id`, `priority`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 1, 11),
(12, 1, 12),
(13, 1, 13),
(14, 1, 14),
(15, 1, 15),
(16, 1, 16),
(1, 2, 17),
(2, 2, 18),
(3, 2, 19),
(4, 2, 20),
(5, 2, 21),
(6, 2, 22),
(7, 2, 23),
(8, 2, 24),
(9, 2, 25),
(10, 2, 26),
(11, 2, 27),
(14, 2, 28),
(15, 2, 29),
(17, 2, 30),
(1, 3, 31),
(2, 3, 32),
(3, 3, 33),
(4, 3, 34),
(5, 3, 35),
(6, 3, 36),
(7, 3, 37),
(8, 3, 38),
(9, 3, 39),
(10, 3, 40),
(11, 3, 41),
(14, 3, 42),
(15, 3, 43),
(18, 3, 44),
(1, 4, 45),
(2, 4, 46),
(3, 4, 47),
(4, 4, 48),
(5, 4, 49),
(6, 4, 50),
(7, 4, 51),
(8, 4, 52),
(9, 4, 53),
(10, 4, 54),
(11, 4, 55),
(14, 4, 56),
(15, 4, 57),
(19, 4, 58),
(1, 5, 59),
(2, 5, 60),
(3, 5, 61),
(4, 5, 62),
(5, 5, 63),
(6, 5, 64),
(7, 5, 65),
(8, 5, 66),
(9, 5, 67),
(10, 5, 68),
(11, 5, 69),
(14, 5, 70),
(15, 5, 71),
(20, 5, 72),
(1, 6, 73),
(2, 6, 74),
(3, 6, 75),
(4, 6, 76),
(5, 6, 77),
(6, 6, 78),
(7, 6, 79),
(8, 6, 80),
(9, 6, 81),
(10, 6, 82),
(11, 6, 83),
(14, 6, 84),
(15, 6, 85),
(21, 6, 86),
(1, 7, 87),
(2, 7, 88),
(3, 7, 89),
(4, 7, 90),
(5, 7, 91),
(6, 7, 92),
(7, 7, 93),
(8, 7, 94),
(9, 7, 95),
(10, 7, 96),
(11, 7, 97),
(14, 7, 98),
(15, 7, 99),
(22, 7, 100),
(39, 1, 101),
(39, 2, 101),
(39, 3, 101),
(39, 4, 101),
(39, 5, 101),
(39, 6, 101),
(39, 7, 101),
(39, 8, 101),
(39, 9, 101),
(39, 10, 101),
(39, 15, 101),
(39, 16, 101),
(39, 17, 101),
(39, 18, 101),
(39, 19, 101),
(39, 20, 101),
(39, 21, 101),
(39, 22, 101),
(39, 23, 101),
(39, 24, 101),
(39, 25, 101),
(39, 27, 101),
(1, 8, 107),
(2, 8, 108),
(3, 8, 109),
(4, 8, 110),
(5, 8, 111),
(6, 8, 112),
(7, 8, 113),
(8, 8, 114),
(9, 8, 115),
(10, 8, 116),
(11, 8, 117),
(14, 8, 118),
(15, 8, 119),
(24, 8, 121),
(25, 8, 122),
(26, 8, 123),
(27, 8, 124),
(1, 9, 125),
(2, 9, 126),
(3, 9, 127),
(4, 9, 128),
(5, 9, 129),
(6, 9, 130),
(7, 9, 131),
(8, 9, 132),
(9, 9, 133),
(10, 9, 134),
(11, 9, 135),
(14, 9, 136),
(15, 9, 137),
(28, 9, 139),
(1, 10, 140),
(2, 10, 141),
(3, 10, 142),
(4, 10, 143),
(5, 10, 144),
(6, 10, 145),
(7, 10, 146),
(8, 10, 147),
(9, 10, 148),
(10, 10, 149),
(11, 10, 150),
(14, 10, 151),
(15, 10, 152),
(29, 10, 154),
(30, 11, 155),
(31, 12, 156),
(32, 13, 157),
(33, 1, 158),
(33, 4, 159),
(33, 5, 160),
(33, 6, 161),
(33, 2, 162),
(33, 3, 163),
(33, 7, 164),
(33, 8, 165),
(33, 9, 166),
(33, 10, 167),
(1, 15, 168),
(2, 15, 169),
(3, 15, 170),
(4, 15, 171),
(5, 15, 172),
(6, 15, 173),
(7, 15, 174),
(8, 15, 175),
(9, 15, 176),
(10, 15, 177),
(11, 15, 178),
(14, 15, 179),
(15, 15, 180),
(33, 15, 182),
(1, 16, 183),
(2, 16, 184),
(3, 16, 185),
(4, 16, 186),
(5, 16, 187),
(6, 16, 188),
(7, 16, 189),
(8, 16, 190),
(9, 16, 191),
(10, 16, 192),
(11, 16, 193),
(14, 16, 194),
(15, 16, 195),
(33, 16, 197),
(1, 17, 198),
(2, 17, 199),
(3, 17, 200),
(4, 17, 201),
(5, 17, 202),
(6, 17, 203),
(7, 17, 204),
(8, 17, 205),
(9, 17, 206),
(10, 17, 207),
(11, 17, 208),
(14, 17, 209),
(15, 17, 210),
(33, 17, 212),
(1, 18, 213),
(2, 18, 214),
(3, 18, 215),
(4, 18, 216),
(5, 18, 217),
(6, 18, 218),
(7, 18, 219),
(8, 18, 220),
(9, 18, 221),
(10, 18, 222),
(11, 18, 223),
(14, 18, 224),
(15, 18, 225),
(33, 18, 227),
(1, 19, 228),
(2, 19, 229),
(3, 19, 230),
(4, 19, 231),
(5, 19, 232),
(6, 19, 233),
(7, 19, 234),
(8, 19, 235),
(9, 19, 236),
(10, 19, 237),
(11, 19, 238),
(14, 19, 239),
(15, 19, 240),
(33, 19, 242),
(1, 20, 243),
(2, 20, 244),
(3, 20, 245),
(4, 20, 246),
(5, 20, 247),
(6, 20, 248),
(7, 20, 249),
(8, 20, 250),
(9, 20, 251),
(10, 20, 252),
(11, 20, 253),
(14, 20, 254),
(15, 20, 255),
(33, 20, 257),
(1, 21, 258),
(2, 21, 259),
(3, 21, 260),
(4, 21, 261),
(5, 21, 262),
(6, 21, 263),
(7, 21, 264),
(8, 21, 265),
(9, 21, 266),
(10, 21, 267),
(11, 21, 268),
(14, 21, 269),
(15, 21, 270),
(33, 21, 272),
(1, 22, 273),
(2, 22, 274),
(3, 22, 275),
(4, 22, 276),
(5, 22, 277),
(6, 22, 278),
(7, 22, 279),
(8, 22, 280),
(9, 22, 281),
(10, 22, 282),
(11, 22, 283),
(14, 22, 284),
(15, 22, 285),
(33, 22, 287),
(1, 23, 288),
(2, 23, 289),
(3, 23, 290),
(4, 23, 291),
(5, 23, 292),
(6, 23, 293),
(7, 23, 294),
(8, 23, 295),
(9, 23, 296),
(10, 23, 297),
(11, 23, 298),
(14, 23, 299),
(15, 23, 300),
(33, 23, 302),
(1, 24, 303),
(2, 24, 304),
(3, 24, 305),
(4, 24, 306),
(5, 24, 307),
(6, 24, 308),
(7, 24, 309),
(8, 24, 310),
(9, 24, 311),
(10, 24, 312),
(11, 24, 313),
(14, 24, 314),
(15, 24, 315),
(33, 24, 317),
(34, 15, 318),
(34, 18, 319),
(34, 19, 320),
(34, 20, 321),
(34, 17, 322),
(34, 21, 323),
(34, 22, 324),
(34, 16, 325),
(34, 23, 326),
(34, 24, 327),
(35, 1, 328),
(36, 1, 329),
(42, 1, 330),
(42, 2, 330),
(42, 3, 330),
(42, 4, 330),
(42, 5, 330),
(42, 6, 330),
(42, 7, 330),
(42, 8, 330),
(42, 9, 330),
(42, 10, 330),
(42, 15, 330),
(42, 16, 330),
(42, 17, 330),
(42, 18, 330),
(42, 19, 330),
(42, 20, 330),
(42, 21, 330),
(42, 22, 330),
(42, 23, 330),
(42, 24, 330),
(42, 25, 330),
(42, 27, 330),
(1, 25, 350),
(2, 25, 351),
(3, 25, 352),
(4, 25, 353),
(5, 25, 354),
(6, 25, 355),
(7, 25, 356),
(8, 25, 357),
(9, 25, 358),
(10, 25, 359),
(11, 25, 360),
(14, 25, 361),
(15, 25, 362),
(33, 25, 364),
(38, 25, 366),
(37, 1, 367),
(37, 2, 367),
(37, 3, 367),
(37, 4, 367),
(37, 5, 367),
(37, 6, 367),
(37, 7, 367),
(37, 8, 367),
(37, 9, 367),
(37, 10, 367),
(37, 15, 367),
(37, 16, 367),
(37, 17, 367),
(37, 18, 367),
(37, 19, 367),
(37, 20, 367),
(37, 21, 367),
(37, 22, 367),
(37, 23, 367),
(37, 24, 367),
(37, 25, 367),
(37, 27, 367),
(40, 26, 388),
(1, 27, 389),
(2, 27, 390),
(3, 27, 391),
(4, 27, 392),
(5, 27, 393),
(6, 27, 394),
(7, 27, 395),
(8, 27, 396),
(9, 27, 397),
(10, 27, 398),
(11, 27, 399),
(14, 27, 400),
(15, 27, 401),
(33, 27, 403),
(41, 27, 406),
(23, 1, 407),
(23, 2, 407),
(23, 3, 407),
(23, 4, 407),
(23, 5, 407),
(23, 6, 407),
(23, 8, 407),
(23, 9, 407),
(23, 10, 407),
(23, 15, 407),
(23, 16, 407),
(23, 17, 407),
(23, 18, 407),
(23, 19, 407),
(23, 20, 407),
(23, 21, 407),
(23, 22, 407),
(23, 23, 407),
(23, 24, 407),
(23, 25, 407),
(23, 27, 407),
(43, 28, 429),
(44, 29, 430),
(1, 30, 431),
(2, 30, 432),
(3, 30, 433),
(4, 30, 434),
(5, 30, 435),
(6, 30, 436),
(7, 30, 437),
(8, 30, 438),
(9, 30, 439),
(10, 30, 440),
(11, 30, 441),
(14, 30, 442),
(15, 30, 443),
(39, 30, 444),
(33, 30, 445),
(42, 30, 446),
(37, 30, 447),
(23, 30, 448),
(1, 31, 449),
(2, 31, 450),
(3, 31, 451),
(4, 31, 452),
(5, 31, 453),
(6, 31, 454),
(7, 31, 455),
(8, 31, 456),
(9, 31, 457),
(10, 31, 458),
(11, 31, 459),
(14, 31, 460),
(15, 31, 461),
(39, 31, 462),
(33, 31, 463),
(42, 31, 464),
(37, 31, 465),
(23, 31, 466),
(1, 32, 467),
(2, 32, 468),
(3, 32, 469),
(4, 32, 470),
(5, 32, 471),
(6, 32, 472),
(7, 32, 473),
(8, 32, 474),
(9, 32, 475),
(10, 32, 476),
(11, 32, 477),
(14, 32, 478),
(15, 32, 479),
(39, 32, 480),
(33, 32, 481),
(42, 32, 482),
(37, 32, 483),
(23, 32, 484),
(1, 33, 485),
(2, 33, 486),
(3, 33, 487),
(4, 33, 488),
(5, 33, 489),
(6, 33, 490),
(7, 33, 491),
(8, 33, 492),
(9, 33, 493),
(10, 33, 494),
(11, 33, 495),
(14, 33, 496),
(15, 33, 497),
(39, 33, 498),
(33, 33, 499),
(42, 33, 500),
(37, 33, 501),
(23, 33, 502),
(45, 30, 503),
(46, 32, 504),
(47, 33, 505),
(48, 31, 506),
(49, 20, 507),
(50, 15, 508),
(51, 15, 509),
(52, 15, 510),
(53, 15, 511);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_php`
--

DROP TABLE IF EXISTS `cms_blocks_php`;
CREATE TABLE IF NOT EXISTS `cms_blocks_php` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `description` text COMMENT 'Code',
  `widget` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Widget ID#',
  PRIMARY KEY (`id`),
  KEY `widget` (`widget`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PHP blocks';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_search`
--

DROP TABLE IF EXISTS `cms_blocks_search`;
CREATE TABLE IF NOT EXISTS `cms_blocks_search` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `search_var_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Search var name',
  `min_length` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Minimal query length',
  `pages_var_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Pages var name',
  `rows_per_page` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Rows per page',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Search blocks';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_search_languages_assoc`
--

DROP TABLE IF EXISTS `cms_blocks_search_languages_assoc`;
CREATE TABLE IF NOT EXISTS `cms_blocks_search_languages_assoc` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `language` varchar(255) NOT NULL DEFAULT '' COMMENT 'Language',
  PRIMARY KEY (`id`,`language`),
  KEY `id` (`id`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Search blocks languages';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_search_material_types_assoc`
--

DROP TABLE IF EXISTS `cms_blocks_search_material_types_assoc`;
CREATE TABLE IF NOT EXISTS `cms_blocks_search_material_types_assoc` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `material_type` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  PRIMARY KEY (`id`,`material_type`),
  KEY `id` (`id`),
  KEY `material_type` (`material_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Search blocks material types';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_blocks_search_pages_assoc`
--

DROP TABLE IF EXISTS `cms_blocks_search_pages_assoc`;
CREATE TABLE IF NOT EXISTS `cms_blocks_search_pages_assoc` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  PRIMARY KEY (`id`,`page_id`),
  KEY `id` (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Search blocks pages';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_data`
--

DROP TABLE IF EXISTS `cms_data`;
CREATE TABLE IF NOT EXISTS `cms_data` (
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `fid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Field ID#',
  `fii` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Field index',
  `value` text COMMENT 'Value',
  `inherited` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Inherited',
  PRIMARY KEY (`pid`,`fid`,`fii`),
  KEY `pid` (`pid`),
  KEY `fid` (`fid`),
  KEY `fii` (`fii`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pages fields';

--
-- Дамп данных таблицы `cms_data`
--

INSERT INTO `cms_data` (`pid`, `fid`, `fii`, `value`, `inherited`) VALUES
(1, 1, 0, '', 0),
(1, 5, 0, 'Тестовый пользователь', 0),
(1, 6, 0, '+7 999 000-00-00', 0),
(1, 7, 0, 'test@test.org', 0),
(1, 8, 0, 'Тестовый вопрос', 0),
(1, 9, 0, '1', 0),
(1, 13, 0, 'smile-o', 0),
(1, 37, 0, '+7 999 000-00-00', 0),
(1, 43, 0, 'Тестовый', 0),
(1, 44, 0, 'Пользователь', 0),
(1, 45, 0, '1', 0),
(2, 13, 0, 'thumbs-o-up', 0),
(2, 37, 0, '+7 999 000-00-00', 0),
(2, 43, 0, 'Тестовый', 0),
(2, 44, 0, 'Пользователь', 0),
(2, 45, 0, '2', 0),
(3, 13, 0, 'rub', 0),
(4, 1, 0, '', 0),
(4, 14, 0, '#', 0),
(4, 15, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":1}', 0),
(5, 14, 0, '#', 0),
(5, 15, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":2}', 0),
(6, 14, 0, '#', 0),
(6, 15, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":3}', 0),
(7, 16, 0, '2018-03-30', 0),
(7, 17, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":4}', 0),
(7, 17, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":5}', 0),
(7, 17, 2, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":6}', 0),
(7, 17, 3, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":7}', 0),
(7, 17, 4, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":8}', 0),
(7, 18, 0, 'Преамбула, согласно традиционным представлениям, приводит астероидный знак, что нельзя рассматривать без изменения системы координат. Расчеты \r\nпредсказывают, что гетерогенная система пока плохо продолжает периодический винил. Показательный пример – ...', 0),
(8, 16, 0, '2018-04-01 09:50', 0),
(8, 17, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":9}', 0),
(8, 17, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":10}', 0),
(8, 17, 2, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":11}', 0),
(8, 17, 3, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":12}', 0),
(8, 17, 4, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":13}', 0),
(8, 18, 0, 'Преамбула, согласно традиционным представлениям, приводит астероидный знак, что нельзя рассматривать без изменения системы координат. Расчеты \nпредсказывают, что гетерогенная система пока плохо продолжает периодический винил. Показательный пример – ...', 0),
(9, 16, 0, '2018-04-05 07:36', 0),
(9, 17, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":14}', 0),
(9, 17, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":15}', 0),
(9, 17, 2, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":16}', 0),
(9, 17, 3, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":17}', 0),
(9, 17, 4, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":18}', 0),
(9, 18, 0, 'Радиант порождает дифференциальный субъект. В ресторане стоимость обслуживания (15%) включена в счет; в баре и кафе - 10-15% счета только за услуги официанта; в такси - чаевые включены в стоимость проезда, тем не менее точность гироскопа характерна....', 0),
(10, 25, 0, 'f4dbdf21', 0),
(10, 26, 0, '83620', 0),
(10, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":30}', 0),
(10, 27, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":31}', 0),
(10, 27, 2, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":32}', 0),
(10, 27, 3, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":33}', 0),
(10, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(10, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(10, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":28}', 0),
(10, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":29}', 0),
(10, 30, 0, '1', 0),
(10, 31, 0, '0', 0),
(10, 32, 0, '2', 0),
(10, 33, 0, '2', 0),
(10, 34, 0, '0', 0),
(11, 25, 0, '83dcefb7', 0),
(11, 26, 0, '67175', 0),
(11, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":36}', 0),
(11, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(11, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(11, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":34}', 0),
(11, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":35}', 0),
(11, 30, 0, '1', 0),
(11, 31, 0, '1', 0),
(11, 32, 0, '1', 0),
(11, 33, 0, '1', 0),
(11, 34, 0, '75907', 0),
(12, 25, 0, '1ad5be0d', 0),
(12, 26, 0, '71013', 0),
(12, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":39}', 0),
(12, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(12, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(12, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":37}', 0),
(12, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":38}', 0),
(12, 30, 0, '1', 0),
(12, 31, 0, '1', 0),
(12, 32, 0, '1', 0),
(12, 33, 0, '1', 0),
(12, 34, 0, '86635', 0),
(13, 25, 0, '6dd28e9b', 0),
(13, 26, 0, '30450', 0),
(13, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":42}', 0),
(13, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(13, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(13, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":40}', 0),
(13, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":41}', 0),
(13, 30, 0, '1', 0),
(13, 31, 0, '1', 0),
(13, 32, 0, '1', 0),
(13, 33, 0, '1', 0),
(13, 34, 0, '0', 0),
(14, 25, 0, 'f3b61b38', 0),
(14, 26, 0, '25712', 0),
(14, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":45}', 0),
(14, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(14, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(14, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":43}', 0),
(14, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":44}', 0),
(14, 30, 0, '1', 0),
(14, 31, 0, '0', 0),
(14, 32, 0, '2', 0),
(14, 33, 0, '2', 0),
(14, 34, 0, '0', 0),
(15, 25, 0, '84b12bae', 0),
(15, 26, 0, '54096', 0),
(15, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":48}', 0),
(15, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(15, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(15, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":46}', 0),
(15, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":47}', 0),
(15, 30, 0, '1', 0),
(15, 31, 0, '1', 0),
(15, 32, 0, '1', 0),
(15, 33, 0, '1', 0),
(15, 34, 0, '0', 0),
(16, 2, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":19}', 0),
(16, 25, 0, '1db87a14', 0),
(16, 26, 0, '49651', 0),
(16, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":51}', 0),
(16, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(16, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(16, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":49}', 0),
(16, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":50}', 0),
(16, 30, 0, '1', 0),
(16, 31, 0, '1', 0),
(16, 32, 0, '1', 0),
(16, 33, 0, '1', 0),
(16, 34, 0, '58091', 0),
(17, 2, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":20}', 0),
(17, 25, 0, '6abf4a82', 0),
(17, 26, 0, '61245', 0),
(17, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":54}', 0),
(17, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(17, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(17, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":52}', 0),
(17, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":53}', 0),
(17, 30, 0, '1', 0),
(17, 31, 0, '1', 0),
(17, 32, 0, '1', 0),
(17, 33, 0, '1', 0),
(17, 34, 0, '73494', 0),
(18, 2, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":21}', 0),
(18, 25, 0, 'fa005713', 0),
(18, 26, 0, '5609', 0),
(18, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":57}', 0),
(18, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(18, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(18, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":55}', 0),
(18, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":56}', 0),
(18, 30, 0, '1', 0),
(18, 31, 0, '0', 0),
(18, 32, 0, '2', 0),
(18, 33, 0, '2', 0),
(18, 34, 0, '6506', 0),
(19, 2, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":22}', 0),
(19, 25, 0, '8d076785', 0),
(19, 26, 0, '85812', 0),
(19, 27, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":60}', 0),
(19, 28, 0, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(19, 28, 1, 'https://www.youtube.com/watch?v=YVgc2PQd_bo', 0),
(19, 29, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":58}', 0),
(19, 29, 1, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":59}', 0),
(19, 30, 0, '1', 0),
(19, 31, 0, '1', 0),
(19, 32, 0, '1', 0),
(19, 33, 0, '1', 0),
(19, 34, 0, '0', 0),
(19, 35, 0, '18', 0),
(19, 35, 1, '19', 0),
(19, 35, 2, '10', 0),
(19, 35, 3, '11', 0),
(19, 35, 4, '16', 0),
(19, 35, 5, '15', 0),
(19, 35, 6, '12', 0),
(19, 35, 7, '17', 0),
(19, 35, 8, '11', 0),
(19, 35, 9, '16', 0),
(19, 35, 10, '10', 0),
(19, 35, 11, '13', 0),
(19, 35, 12, '10', 0),
(19, 35, 13, '15', 0),
(19, 35, 14, '19', 0),
(19, 35, 15, '18', 0),
(19, 35, 16, '15', 0),
(19, 35, 17, '10', 0),
(19, 35, 18, '19', 0),
(19, 35, 19, '10', 0),
(19, 35, 20, '14', 0),
(19, 35, 21, '14', 0),
(19, 35, 22, '14', 0),
(19, 35, 23, '18', 0),
(19, 35, 24, '19', 0),
(19, 35, 25, '17', 0),
(19, 35, 26, '14', 0),
(19, 35, 27, '19', 0),
(19, 35, 28, '16', 0),
(19, 35, 29, '11', 0),
(20, 2, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":23}', 0),
(20, 49, 0, '12', 0),
(20, 50, 0, '', 0),
(20, 51, 0, '', 0),
(20, 52, 0, '', 0),
(20, 53, 0, '', 0),
(20, 54, 0, '', 0),
(21, 2, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":24}', 0),
(21, 49, 0, '12', 0),
(21, 50, 0, '', 0),
(21, 51, 0, '', 0),
(21, 52, 0, '', 0),
(21, 53, 0, '', 0),
(21, 54, 0, '', 0),
(22, 2, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":25}', 0),
(22, 49, 0, '12', 0),
(22, 50, 0, '', 0),
(22, 51, 0, '', 0),
(22, 52, 0, '', 0),
(22, 53, 0, '', 0),
(22, 54, 0, '', 0),
(23, 2, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":26}', 0),
(23, 55, 0, '', 0),
(23, 56, 0, '12', 0),
(23, 57, 0, '1', 0),
(23, 58, 0, '', 0),
(23, 59, 0, '', 0),
(24, 2, 0, '{\"vis\":1,\"name\":\"\",\"description\":\"\",\"attachment\":27}', 0),
(24, 55, 0, '', 0),
(24, 56, 0, '12', 0),
(24, 57, 0, '2', 0),
(24, 58, 0, '', 0),
(24, 59, 0, '', 0),
(25, 1, 0, '', 0),
(25, 55, 0, '', 0),
(25, 56, 0, '12', 0),
(25, 57, 0, '3', 0),
(25, 58, 0, '', 0),
(25, 59, 0, '', 0),
(30, 1, 0, '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_dictionaries`
--

DROP TABLE IF EXISTS `cms_dictionaries`;
CREATE TABLE IF NOT EXISTS `cms_dictionaries` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `vis` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Visibility',
  `pvis` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Parent visibility',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  `orderby` enum('id','urn','name','priority') NOT NULL DEFAULT 'priority' COMMENT 'Order by',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `urn` (`urn`),
  KEY `orderby` (`orderby`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Dictionaries';

--
-- Дамп данных таблицы `cms_dictionaries`
--

INSERT INTO `cms_dictionaries` (`id`, `pid`, `vis`, `pvis`, `urn`, `name`, `priority`, `orderby`) VALUES
(1, 0, 1, 1, 'test', 'Тестовый справочник', 1, 'priority'),
(2, 1, 1, 1, 'value1', 'Запись 1', 2, 'priority'),
(3, 1, 1, 1, 'value2', 'Запись 2', 3, 'priority'),
(4, 1, 0, 1, 'value3', 'Запись 3', 4, 'priority');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_dictionaries_tree_cache`
--

DROP TABLE IF EXISTS `cms_dictionaries_tree_cache`;
CREATE TABLE IF NOT EXISTS `cms_dictionaries_tree_cache` (
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `child_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Child ID#',
  `__level` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Level',
  PRIMARY KEY (`child_id`,`__level`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`),
  KEY `__level` (`__level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dictionaries tree cache';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_feedback`
--

DROP TABLE IF EXISTS `cms_feedback`;
CREATE TABLE IF NOT EXISTS `cms_feedback` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Site user ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Form ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  `material_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  `vis` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Visited',
  `ip` varchar(255) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP address',
  `user_agent` varchar(255) NOT NULL DEFAULT '0.0.0.0' COMMENT 'User Agent',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `pid` (`pid`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Feedback';

--
-- Дамп данных таблицы `cms_feedback`
--

INSERT INTO `cms_feedback` (`id`, `uid`, `pid`, `page_id`, `material_id`, `post_date`, `vis`, `ip`, `user_agent`) VALUES
(1, 0, 1, 1, 5, '2018-04-22 10:23:58', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_fields`
--

DROP TABLE IF EXISTS `cms_fields`;
CREATE TABLE IF NOT EXISTS `cms_fields` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `classname` varchar(255) NOT NULL DEFAULT '' COMMENT 'Parent class name',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `datatype` varchar(255) NOT NULL DEFAULT '' COMMENT 'Data type',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `required` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Required',
  `maxlength` int(255) NOT NULL,
  `multiple` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Multiple data',
  `source_type` enum('','ini','csv','xml','sql','php','dictionary') NOT NULL DEFAULT '' COMMENT 'Source type',
  `source` text COMMENT 'Source',
  `defval` text COMMENT 'Default value',
  `min_val` float NOT NULL DEFAULT '0' COMMENT 'Minimal value',
  `max_val` float NOT NULL DEFAULT '0' COMMENT 'Maximal value',
  `step` float NOT NULL DEFAULT '0' COMMENT 'Step',
  `preprocessor_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Preprocessor interface ID#',
  `postprocessor_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Postprocessor interface ID#',
  `placeholder` varchar(255) NOT NULL DEFAULT '' COMMENT 'Placeholder',
  `pattern` varchar(255) NOT NULL DEFAULT '' COMMENT 'Pattern',
  `show_in_table` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Show as table column',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `datatype` (`datatype`),
  KEY `classname` (`classname`),
  KEY `classname_2` (`classname`,`pid`),
  KEY `preprocessor_id` (`preprocessor_id`),
  KEY `postprocessor_id` (`postprocessor_id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='Material fields';

--
-- Дамп данных таблицы `cms_fields`
--

INSERT INTO `cms_fields` (`id`, `classname`, `pid`, `datatype`, `urn`, `name`, `required`, `maxlength`, `multiple`, `source_type`, `source`, `defval`, `min_val`, `max_val`, `step`, `preprocessor_id`, `postprocessor_id`, `placeholder`, `pattern`, `show_in_table`, `priority`) VALUES
(1, 'RAAS\\CMS\\Material_Type', 0, 'htmlarea', '_description_', 'Описание', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 1),
(2, 'RAAS\\CMS\\Material_Type', 0, 'image', 'image', 'Изображение', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 2),
(3, 'RAAS\\CMS\\Material_Type', 0, 'checkbox', 'noindex', 'Не индексировать', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 3),
(4, 'RAAS\\CMS\\Material_Type', 0, 'image', 'background', 'Фоновое изображение', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 4),
(5, 'RAAS\\CMS\\Form', 1, 'text', 'full_name', 'Ваше имя', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 5),
(6, 'RAAS\\CMS\\Form', 1, 'text', 'phone', 'Телефон', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 6),
(7, 'RAAS\\CMS\\Form', 1, 'text', 'email', 'E-mail', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 7),
(8, 'RAAS\\CMS\\Form', 1, 'textarea', '_description_', 'Текст вопроса', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 8),
(9, 'RAAS\\CMS\\Form', 1, 'checkbox', 'agree', 'Согласен(на) на обработку персональных данных', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 9),
(10, 'RAAS\\CMS\\Form', 2, 'text', 'phone_call', 'Телефон', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 10),
(11, 'RAAS\\CMS\\Form', 2, 'checkbox', 'agree', 'Согласен(на) на обработку персональных данных', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 11),
(12, 'RAAS\\CMS\\Material_Type', 1, 'image', 'image', 'Изображение', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 12),
(13, 'RAAS\\CMS\\Material_Type', 1, 'text', 'icon', 'Значок', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 13),
(14, 'RAAS\\CMS\\Material_Type', 2, 'text', 'url', 'Адрес ссылки', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 14),
(15, 'RAAS\\CMS\\Material_Type', 2, 'image', 'image', 'Изображение', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 15),
(16, 'RAAS\\CMS\\Material_Type', 3, 'date', 'date', 'Дата', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 16),
(17, 'RAAS\\CMS\\Material_Type', 3, 'image', 'images', 'Изображение', 0, 0, 1, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 17),
(18, 'RAAS\\CMS\\Material_Type', 3, 'textarea', 'brief', 'Краткое описание', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 18),
(19, 'RAAS\\CMS\\Material_Type', 3, 'checkbox', 'noindex', 'Не индексировать', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 19),
(20, 'RAAS\\CMS\\Form', 3, 'text', 'full_name', 'Ваше имя', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 20),
(21, 'RAAS\\CMS\\Form', 3, 'text', 'phone', 'Телефон', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 21),
(22, 'RAAS\\CMS\\Form', 3, 'text', 'email', 'E-mail', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 22),
(23, 'RAAS\\CMS\\Form', 3, 'textarea', '_description_', 'Комментарий', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 23),
(24, 'RAAS\\CMS\\Form', 3, 'checkbox', 'agree', 'Согласен(на) на обработку персональных данных', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 24),
(25, 'RAAS\\CMS\\Material_Type', 4, 'text', 'article', 'Артикул', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 25),
(26, 'RAAS\\CMS\\Material_Type', 4, 'number', 'price', 'Стоимость', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 26),
(27, 'RAAS\\CMS\\Material_Type', 4, 'image', 'images', 'Изображение', 0, 0, 1, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 27),
(28, 'RAAS\\CMS\\Material_Type', 4, 'text', 'videos', 'Видеозаписи', 0, 0, 1, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 28),
(29, 'RAAS\\CMS\\Material_Type', 4, 'file', 'files', 'Файлы', 0, 0, 1, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 29),
(30, 'RAAS\\CMS\\Material_Type', 4, 'checkbox', 'spec', 'Спецпредложение', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 30),
(31, 'RAAS\\CMS\\Material_Type', 4, 'checkbox', 'available', 'В наличии', 0, 0, 0, '', NULL, '1', 0, 0, 0, 0, 0, '', '', 0, 31),
(32, 'RAAS\\CMS\\Material_Type', 4, 'number', 'min', 'Минимальное количество', 0, 0, 0, '', NULL, '1', 0, 0, 0, 0, 0, '', '', 0, 32),
(33, 'RAAS\\CMS\\Material_Type', 4, 'number', 'step', 'Шаг корзины', 0, 0, 0, '', NULL, '1', 0, 0, 0, 0, 0, '', '', 0, 33),
(34, 'RAAS\\CMS\\Material_Type', 4, 'number', 'price_old', 'Старая цена', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 0, 34),
(35, 'RAAS\\CMS\\Material_Type', 4, 'material', 'related', 'Связанные товары', 0, 0, 1, '', '4', NULL, 0, 0, 0, 0, 0, '', '', 0, 35),
(37, 'RAAS\\CMS\\User', 0, 'text', 'phone', 'Телефон', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 37),
(38, 'RAAS\\CMS\\Form', 4, 'text', 'login', 'Логин', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 38),
(39, 'RAAS\\CMS\\Form', 4, 'password', 'password', 'Пароль', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 39),
(40, 'RAAS\\CMS\\Form', 4, 'text', 'email', 'E-mail', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 40),
(41, 'RAAS\\CMS\\Form', 4, 'text', 'full_name', 'Ваше имя', 1, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 41),
(42, 'RAAS\\CMS\\Form', 4, 'text', 'phone', 'Телефон', 0, 0, 0, '', NULL, NULL, 0, 0, 0, 0, 0, '', '', 1, 42),
(43, 'RAAS\\CMS\\User', 0, 'text', 'last_name', 'Фамилия', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 43),
(44, 'RAAS\\CMS\\User', 0, 'text', 'first_name', 'Имя', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 44),
(45, 'RAAS\\CMS\\User', 0, 'text', 'second_name', 'Отчество', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 45),
(47, 'RAAS\\CMS\\Material_Type', 4, 'select', 'testfield', 'Тестовое поле справочника', 0, 0, 0, 'dictionary', '1', '', 0, 0, 0, 0, 0, '', '', 0, 46),
(48, 'RAAS\\CMS\\Material_Type', 5, 'text', 'special', 'Особое поле', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 47),
(49, 'RAAS\\CMS\\Material_Type', 6, 'material', 'material', 'Материал', 0, 0, 0, '', '4', '', 0, 0, 0, 0, 0, '', '', 0, 48),
(50, 'RAAS\\CMS\\Material_Type', 6, 'datetime-local', 'date', 'Дата', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 49),
(51, 'RAAS\\CMS\\Material_Type', 6, 'text', 'phone', 'Телефон', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 50),
(52, 'RAAS\\CMS\\Material_Type', 6, 'email', 'email', 'E-mail', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 51),
(53, 'RAAS\\CMS\\Material_Type', 6, 'text', 'answer_name', 'Имя отвечающего', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 52),
(54, 'RAAS\\CMS\\Material_Type', 6, 'htmlarea', 'answer', 'Ответ', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 53),
(55, 'RAAS\\CMS\\Material_Type', 7, 'datetime-local', 'date', 'Дата', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 54),
(56, 'RAAS\\CMS\\Material_Type', 7, 'material', 'material', 'Материал', 0, 0, 0, '', '4', '', 0, 0, 0, 0, 0, '', '', 0, 55),
(57, 'RAAS\\CMS\\Material_Type', 7, 'number', 'rating', 'Рейтинг', 0, 0, 0, '', '0', '', 0, 5, 1, 0, 0, '', '', 0, 56),
(58, 'RAAS\\CMS\\Material_Type', 7, 'text', 'answer_name', 'Имя отвечающего', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 57),
(59, 'RAAS\\CMS\\Material_Type', 7, 'htmlarea', 'answer', 'Ответ', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 58),
(60, 'RAAS\\CMS\\Form', 5, 'text', 'full_name', 'Ваше имя', 1, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 10),
(61, 'RAAS\\CMS\\Form', 5, 'checkbox', 'phone', 'Телефон', 1, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 20),
(62, 'RAAS\\CMS\\Form', 5, 'textarea', '_description_', 'Текст вопроса', 1, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 30),
(63, 'RAAS\\CMS\\Form', 5, 'material', 'material', 'Материал', 1, 0, 0, '', '', '', 0, 0, 0, 0, 0, '', '', 0, 40),
(64, 'RAAS\\CMS\\Form', 6, 'text', 'full_name', 'Ваше имя', 1, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 59),
(65, 'RAAS\\CMS\\Form', 6, 'email', 'email', 'E-mail', 0, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 60),
(66, 'RAAS\\CMS\\Form', 6, 'textarea', '_description_', 'Текст отзыва', 1, 0, 0, '', '0', '', 0, 0, 0, 0, 0, '', '', 0, 61),
(67, 'RAAS\\CMS\\Form', 6, 'material', 'material', 'Материал', 1, 0, 0, '', '4', '', 0, 0, 0, 0, 0, '', '', 0, 62),
(68, 'RAAS\\CMS\\Form', 6, 'number', 'rating', 'Рейтинг', 0, 0, 0, '', '0', '', 0, 5, 1, 0, 0, '', '', 0, 63);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_forms`
--

DROP TABLE IF EXISTS `cms_forms`;
CREATE TABLE IF NOT EXISTS `cms_forms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `material_type` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type',
  `create_feedback` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Create feedback',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contact e-mail',
  `signature` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Require POST signature',
  `antispam` varchar(255) NOT NULL DEFAULT '' COMMENT 'Use anti-spam',
  `antispam_field_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Anti-spam field name',
  `interface_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Interface ID#',
  PRIMARY KEY (`id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Forms';

--
-- Дамп данных таблицы `cms_forms`
--

INSERT INTO `cms_forms` (`id`, `name`, `urn`, `material_type`, `create_feedback`, `email`, `signature`, `antispam`, `antispam_field_name`, `interface_id`) VALUES
(1, 'Обратная связь', 'feedback', 0, 1, '', 1, 'hidden', '_name', 5),
(2, 'Заказать звонок', 'order_call', 0, 1, '', 1, 'hidden', '_name', 5),
(3, 'Форма заказа', 'order', 0, 1, '', 1, 'hidden', '_name', 25),
(4, 'Форма для регистрации', 'forma_dla_registracii', 0, 1, '', 1, 'hidden', '_name', 49),
(5, 'Вопрос-ответ к товарам', 'goods_faq', 6, 1, '', 1, 'hidden', '_name', 5),
(6, 'Отзывы к товарам', 'goods_reviews', 7, 1, '', 1, 'hidden', '_name', 5);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_groups`
--

DROP TABLE IF EXISTS `cms_groups`;
CREATE TABLE IF NOT EXISTS `cms_groups` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent group ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `description` text COMMENT 'Description',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Groups of users';

--
-- Дамп данных таблицы `cms_groups`
--

INSERT INTO `cms_groups` (`id`, `pid`, `name`, `urn`, `description`) VALUES
(1, 0, 'testgroup', 'testgroup', '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_groups_tree_cache`
--

DROP TABLE IF EXISTS `cms_groups_tree_cache`;
CREATE TABLE IF NOT EXISTS `cms_groups_tree_cache` (
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `child_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Child ID#',
  `__level` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Level',
  PRIMARY KEY (`child_id`,`__level`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`),
  KEY `__level` (`__level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Groups tree cache';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_materials`
--

DROP TABLE IF EXISTS `cms_materials`;
CREATE TABLE IF NOT EXISTS `cms_materials` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Default page ID#',
  `vis` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Visibility',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  `modify_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  `author_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  `editor_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Editor ID#',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `description` text COMMENT 'Description',
  `meta_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Meta title',
  `meta_description` text COMMENT 'Meta description',
  `meta_keywords` text COMMENT 'Meta keywords',
  `h1` varchar(255) NOT NULL DEFAULT '' COMMENT 'H1 title',
  `menu_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Menu name',
  `breadcrumbs_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Breadcrumbs name',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  `visit_counter` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Visit counter',
  `modify_counter` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modify counter',
  `changefreq` enum('','always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT '' COMMENT 'Change frequency',
  `last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last modified',
  `sitemaps_priority` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.50' COMMENT 'Sitemaps priority',
  `show_from` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish from date/time',
  `show_to` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish to date/time',
  `cache_url_parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cached URL Parent ID#',
  `cache_url` varchar(255) NOT NULL DEFAULT '/' COMMENT 'Cached URL',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `author_id` (`author_id`),
  KEY `editor_id` (`editor_id`),
  KEY `urn` (`urn`),
  KEY `show_from` (`show_from`),
  KEY `show_to` (`show_to`),
  KEY `priority` (`priority`),
  KEY `cache_url_parent_id` (`cache_url_parent_id`),
  KEY `cache_url` (`cache_url`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='Translator exceptions';

--
-- Дамп данных таблицы `cms_materials`
--

INSERT INTO `cms_materials` (`id`, `pid`, `page_id`, `vis`, `post_date`, `modify_date`, `author_id`, `editor_id`, `urn`, `name`, `description`, `meta_title`, `meta_description`, `meta_keywords`, `h1`, `menu_name`, `breadcrumbs_name`, `priority`, `visit_counter`, `modify_counter`, `changefreq`, `last_modified`, `sitemaps_priority`, `show_from`, `show_to`, `cache_url_parent_id`, `cache_url`) VALUES
(1, 1, 0, 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 0, 0, 'klient-orientirovannyi_podhod', 'Клиент-ориентированный подход', 'Мы ценим наших клиентов и стараемся максимально вникнуть в бизнес-процесс, чтобы обеспечить индивидуальность решения и максимальное соответствие потребностям каждого клиента', '', NULL, NULL, '', '', '', 10, 0, 1, '', '2018-04-05 11:33:47', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(2, 1, 0, 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 0, 0, 'kachestvo_ispolneniya', 'Качество исполнения', 'Мы стараемся сделать нашу продукцию максимально качественной, оперативно реагируя на замечания клиента. На все услуги предоставляется долгосрочная гарантия', '', NULL, NULL, '', '', '', 20, 0, 1, '', '2018-04-05 11:33:47', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(3, 1, 0, 1, '2018-04-05 11:33:47', '2018-04-05 11:33:47', 0, 0, 'umerennaya_cenovaya_politika', 'Умеренная ценовая политика', 'Наши цены выгодно отличаются от предложений большинства конкурентов. Также для постоянных клиентов предусмотрена гибкая система скидок', '', NULL, NULL, '', '', '', 30, 0, 1, '', '2018-04-05 11:33:47', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(4, 2, 0, 1, '2018-04-05 11:34:14', '2018-04-05 11:34:14', 0, 0, 'neodnorodnyi_soliton_glazami_sovremennikov', 'Неоднородный солитон глазами современников', 'Очевидно, что аполлоновское начало заканчивает полимерный кредитор. Рыночная информация, согласно третьему закону Ньютона, складчата. Линеаризация участвует \nв погрешности определения курса меньше, чем термодинамический интеллект с учётом интеграла...', '', NULL, NULL, '', '', '', 10, 0, 1, '', '2018-04-05 11:34:14', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(5, 2, 0, 1, '2018-04-05 11:34:14', '2018-04-05 11:34:14', 0, 0, '', '', '', '', NULL, NULL, '', '', '', 20, 0, 1, '', '2018-04-05 11:34:14', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(6, 2, 0, 1, '2018-04-05 11:34:14', '2018-04-05 11:34:14', 0, 0, 'individualnyi_gipergennyi_mineral_metodologiya_i_osobennosti', 'Индивидуальный гипергенный минерал: методология и особенности', 'Диалектика представляет собой подземный сток. Гарант стремительно предоставляет чернозём. Трещина гидролизует скрытый смысл  – север вверху, восток слева.', '', NULL, NULL, '', '', '', 30, 0, 1, '', '2018-04-05 11:34:14', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(7, 3, 0, 1, '2018-04-05 11:34:14', '2018-04-22 09:49:36', 0, 1, 'empiricheskiy_kreditor_v_xxi_veke', 'Эмпирический кредитор в XXI веке', '<p>Преамбула, согласно традиционным представлениям, приводит астероидный знак, что нельзя рассматривать без изменения системы координат. Расчеты предсказывают, что гетерогенная система пока плохо продолжает периодический винил. Показательный пример &ndash; стилистическая игра испаряет лимногляциальный рутений.</p>\r\n\r\n<p>Очевидно, что либидо отличительно оценивает небольшой системный анализ. Конформизм притягивает пласт. Однако, исследователи постоянно сталкиваются с тем, что ощущение&nbsp;мира определяет пигмент, отвоевывая рыночный сегмент.</p>\r\n\r\n<p>Частная производная, по определению, кристалично ускоряет экваториальный ямб. Основание вызывает контраст. Тавтология, как бы это ни казалось парадоксальным, вызывает центральный педон. Эта разница, вероятно, помогает объяснить, почему фокус-группа очевидна&nbsp;не&nbsp;для&nbsp;всех. Ямб традиционен.</p>', '', '', '', '', '', '', 10, 0, 2, '', '2018-04-22 09:49:36', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 7, '/news/empiricheskiy_kreditor_v_xxi_veke/'),
(8, 3, 0, 1, '2018-04-05 11:34:15', '2018-04-05 11:34:15', 0, 0, 'empiricheskiy_kreditor_v_xxi_veke-8', 'Эмпирический кредитор в XXI веке', '<p>Преамбула, согласно традиционным представлениям, приводит астероидный знак, что нельзя рассматривать без изменения системы координат. Расчеты \nпредсказывают, что гетерогенная система пока плохо продолжает периодический винил. Показательный пример –  стилистическая игра испаряет лимногляциальный рутений.</p>\n<p>Очевидно, что либидо отличительно оценивает небольшой системный анализ. Конформизм притягивает пласт. Однако, исследователи постоянно сталкиваются с тем, что ощущение мира определяет пигмент, отвоевывая рыночный сегмент.</p>\n<p>Частная производная, по определению, кристалично ускоряет экваториальный ямб. Основание вызывает контраст. Тавтология, как бы это ни казалось парадоксальным, вызывает центральный педон. Эта разница, вероятно, помогает объяснить, почему фокус-группа очевидна не для всех. Ямб традиционен.</p>', '', NULL, NULL, '', '', '', 20, 0, 1, '', '2018-04-05 11:34:15', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 7, '/news/empiricheskiy_kreditor_v_xxi_veke-8/'),
(9, 3, 0, 1, '2018-04-05 11:34:15', '2018-04-05 11:34:15', 0, 0, 'ideologicheskiy_umysel_osnovnye_momenty', 'Идеологический умысел: основные моменты', '<p>Радиант порождает дифференциальный субъект. В ресторане стоимость обслуживания (15%) включена в счет; в баре и кафе - 10-15% счета только за услуги официанта; в такси - чаевые включены в стоимость проезда, тем не менее точность гироскопа характерна. Изомерия диазотирует конфликт. Вещество надкусывает симулякр.</p>\n<p>Психологический параллелизм обогащает целевой трафик. Бихевиоризм программирует сходящийся ряд. Политическая элита иллюстрирует акцент.</p>\n<p>Бабувизм, особенно в условиях политической нестабильности, имитирует интеграл от функции, обращающейся в бесконечность в изолированной точке, потому что сюжет и фабула различаются. Руководящее ископаемое амбивалентно сублимирует ритмоформульный бурозём, основываясь на ограничениях, наложенных на систему. Гончарный дренаж на следующий год, когда было лунное затмение и сгорел древний храм Афины в Афинах (при эфоре Питии и афинском архонте Каллии), одномерно контролирует вектор. Дуализм противозаконно вызывает портрет потребителя. Экситон перечеркивает интеллект. Микростроение, несмотря на внешние воздействия, латерально иллюстрирует изобарический интеллект.</p>', '', NULL, NULL, '', '', '', 30, 0, 1, '', '2018-04-05 11:34:15', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 7, '/news/ideologicheskiy_umysel_osnovnye_momenty/'),
(10, 4, 0, 1, '2018-04-05 11:34:26', '2018-04-05 11:34:26', 0, 0, 'tovar_1', 'Товар 1', '', '', NULL, NULL, '', '', '', 10, 0, 1, '', '2018-04-05 11:34:26', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_1/'),
(11, 4, 0, 1, '2018-04-05 11:34:26', '2018-04-05 11:34:26', 0, 0, 'tovar_2', 'Товар 2', '<p>Официальный язык регрессийно интегрирует гидродинамический удар. Литургическая драма подчеркивает архетип. Точность тангажа транслирует плюмаж. Искусство законодательно. Продуктовый ассортимент, на первый взгляд, занимает валютный референдум. Следует отметить, что скорость детонации выводит метод кластерного \nанализа, и это неудивительно, если вспомнить синергетический характер явления.</p>\n<p>Если предварительно подвергнуть объекты длительному вакуумированию,  перигелий дискредитирует композиционный аллит. Пространственная вариабельность почвенного покрова, как и везде в пределах наблюдаемой вселенной, астатически вызывает гедонизм, что и требовалось доказать. Анализ зарубежного опыта мгновенно усиливает турбулентный газ, изменяя привычную реальность. Геометрическая прогрессия трансформирует внутримолекулярный гений, но языковая игра не приводит к активно-диалогическому пониманию. Как мы уже знаем, внутреннее кольцо формирует генетический шток.</p>\n<p>Общеизвестно, что  струя интегрирует фраджипэн, таким образом сбылась мечта идиота - утверждение полностью доказано. Правило альтернанса полимеризует положительный дренаж. Аполлоновское начало выслеживает туффит. Математический маятник асферично просветляет современный имидж. Согласно концепции М.Маклюэна,  структурализм верифицирует экситон.</p>', '', NULL, NULL, '', '', '', 20, 0, 1, '', '2018-04-05 11:34:26', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_2/'),
(12, 4, 0, 1, '2018-04-05 11:34:26', '2018-04-05 11:34:26', 0, 0, 'tovar_3', 'Товар 3', '<p>Живая сессия систематически начинает эйдос, туда же входят 39 графств, 6 метрополитенских графств и Большой Лондон. Уравнение в частных производных экстремально покрывает постмодернизм. Роджерс первым ввел в научный обиход понятие «клиент», так как аутотренинг ослабляет культурный фирн. Многочисленные расчеты предсказывают, а эксперименты подтверждают, что постиндустриализм традиционен.</p>\n<p>Расслоение, на первый взгляд, аккумулирует гумус, хотя все знают, что Венгрия подарила миру таких великих композиторов как Ференц Лист, Бела Барток, Золтан Кодай, режиссеров Иштвана Сабо и Миклоша Янчо, поэта Шандора Пэтефи и художника Чонтвари. Фотон обуславливает метеорный дождь, хотя этот факт нуждается в дальнейшей тщательной экспериментальной проверке. Если для простоты пренебречь потерями на теплопроводность, то видно, что драма раскладывает на элементы неоднозначный многочлен. Мажоритарная избирательная система, как следует из вышесказанного,  воспроизводима в лабораторных условиях.</p>\n<p>Итак, ясно, что политическое учение Монтескье вызывает структурализм. Фотоиндуцированный энергетический перенос, анализируя результаты рекламной кампании, абстрактен. Поэтому плазменное образование трансформирует договор.</p>', '', NULL, NULL, '', '', '', 30, 0, 1, '', '2018-04-05 11:34:26', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_3/'),
(13, 4, 0, 1, '2018-04-05 11:34:26', '2018-04-05 11:34:26', 0, 0, 'tovar_4', 'Товар 4', '<p>Очевидно, что аполлоновское начало заканчивает полимерный кредитор. Рыночная информация, согласно третьему закону Ньютона, складчата. Линеаризация участвует \nв погрешности определения курса меньше, чем термодинамический интеллект с учётом интеграла собственного кинетического момента ротора. Исходя из данного утверждения, бессознательное постоянно. Ревер  без оглядки на авторитеты представляет собой журавчик.</p>\n<p>Сверхпроводник, по определению, сложен. Генезис не так уж очевиден. Основная магистраль проходит с севера на юг от Шкодера через Дуррес до Влёры, после поворота волна даёт более \nпростую систему дифференциальных уравнений, если исключить период. Конечно,  примочка интенсивно уравновешивает цикл. Комбинаторное приращение рассматривается традиционный реализм, как и предсказывает общая теория поля.</p>\n<p>Зеркало требует \nперейти к поступательно перемещающейся системе координат, чем и характеризуется параметр Родинга-Гамильтона. Чем больше люди узнают друг друга, тем больше одиннадцатисложник слабопроницаем. Пласт вызывает неопровержимый интеграл Фурье. Система координат ударяет индикатор.</p>', '', NULL, NULL, '', '', '', 40, 0, 1, '', '2018-04-05 11:34:26', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_4/'),
(14, 4, 0, 1, '2018-04-05 11:34:27', '2018-04-05 11:34:27', 0, 0, 'tovar_5', 'Товар 5', '<p>Официальный язык регрессийно интегрирует гидродинамический удар. Литургическая драма подчеркивает архетип. Точность тангажа транслирует плюмаж. Искусство законодательно. Продуктовый ассортимент, на первый взгляд, занимает валютный референдум. Следует отметить, что скорость детонации выводит метод кластерного \nанализа, и это неудивительно, если вспомнить синергетический характер явления.</p>\n<p>Если предварительно подвергнуть объекты длительному вакуумированию,  перигелий дискредитирует композиционный аллит. Пространственная вариабельность почвенного покрова, как и везде в пределах наблюдаемой вселенной, астатически вызывает гедонизм, что и требовалось доказать. Анализ зарубежного опыта мгновенно усиливает турбулентный газ, изменяя привычную реальность. Геометрическая прогрессия трансформирует внутримолекулярный гений, но языковая игра не приводит к активно-диалогическому пониманию. Как мы уже знаем, внутреннее кольцо формирует генетический шток.</p>\n<p>Общеизвестно, что  струя интегрирует фраджипэн, таким образом сбылась мечта идиота - утверждение полностью доказано. Правило альтернанса полимеризует положительный дренаж. Аполлоновское начало выслеживает туффит. Математический маятник асферично просветляет современный имидж. Согласно концепции М.Маклюэна,  структурализм верифицирует экситон.</p>', '', NULL, NULL, '', '', '', 50, 0, 1, '', '2018-04-05 11:34:27', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_5/'),
(15, 4, 0, 1, '2018-04-05 11:34:27', '2018-04-05 11:34:27', 0, 0, 'tovar_6', 'Товар 6', '', '', NULL, NULL, '', '', '', 60, 0, 1, '', '2018-04-05 11:34:27', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_6/'),
(16, 4, 0, 1, '2018-04-05 11:34:27', '2018-04-05 11:34:27', 0, 0, 'tovar_7', 'Товар 7', '<p>Понятие тоталитаризма, конечно, упруго начинает ассоцианизм. Целое число дисгармонично. Генетика, в соответствии с модифицированным уравнением Эйлера, фоссилизирует периодический импульс.</p>\n<p>Тем не менее, модальность высказывания вертикально отражает базальтовый слой. Движимое имущество представляет собой прибор, что часто служит основанием изменения и прекращения гражданских прав и обязанностей. Комета, в первом приближении, дегустирует закон внешнего мира. Фишка притягивает объект. Бамбуковый медведь панда огнеопасно трансформирует лирический субъект. Лайн-ап обедняет квазар.</p>\n<p>Притеррасная низменность иллюстрирует культурный синтаксис искусства. \"кодекс деяний\", не вдаваясь в подробности, даёт более \nпростую систему дифференциальных уравнений, если исключить комплексный кристалл. Великобритания преобразует метафоричный комплекс рения с саленом.</p>', '', NULL, NULL, '', '', '', 70, 0, 1, '', '2018-04-05 11:34:27', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_7/'),
(17, 4, 0, 1, '2018-04-05 11:34:27', '2018-04-05 11:34:27', 0, 0, 'tovar_8', 'Товар 8', '<p>Очевидно, что аполлоновское начало заканчивает полимерный кредитор. Рыночная информация, согласно третьему закону Ньютона, складчата. Линеаризация участвует \nв погрешности определения курса меньше, чем термодинамический интеллект с учётом интеграла собственного кинетического момента ротора. Исходя из данного утверждения, бессознательное постоянно. Ревер  без оглядки на авторитеты представляет собой журавчик.</p>\n<p>Сверхпроводник, по определению, сложен. Генезис не так уж очевиден. Основная магистраль проходит с севера на юг от Шкодера через Дуррес до Влёры, после поворота волна даёт более \nпростую систему дифференциальных уравнений, если исключить период. Конечно,  примочка интенсивно уравновешивает цикл. Комбинаторное приращение рассматривается традиционный реализм, как и предсказывает общая теория поля.</p>\n<p>Зеркало требует \nперейти к поступательно перемещающейся системе координат, чем и характеризуется параметр Родинга-Гамильтона. Чем больше люди узнают друг друга, тем больше одиннадцатисложник слабопроницаем. Пласт вызывает неопровержимый интеграл Фурье. Система координат ударяет индикатор.</p>', '', NULL, NULL, '', '', '', 80, 0, 1, '', '2018-04-05 11:34:27', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_8/'),
(18, 4, 0, 1, '2018-04-05 11:34:27', '2018-04-05 11:34:27', 0, 0, 'tovar_9', 'Товар 9', '<p>Импульс устойчиво отталкивает конституционный свежеприготовленный раствор. Неоднородность иллюстрирует восстановитель. Принцип \nартистизма выслеживает трог, хотя в официозе принято обратное.</p>\n<p>Ритмическая организованность таких стихов не всегда очевидна при чтении \"про себя\", но внутридискретное арпеджио непрерывно. Гетерогенная структура, учитывая отсутствие в законе норм, посвященных данному вопросу, представляет собой энергетический подуровень. Движимое имущество пространственно неоднородно. Хорус совершает твердый восстановитель. Верховое болото реально формирует лирический сарос одинаково по всем направлениям. Делькредере астатично.</p>\n<p>Пескование, на первый взгляд, законодательно. Португальская колонизация на следующий год, когда было лунное затмение и сгорел древний храм Афины в Афинах (при эфоре Питии и афинском архонте Каллии), готично совершает фотоиндуцированный энергетический перенос. Социальный статус, так или иначе, возбуждает косвенный рисчоррит.</p>', '', NULL, NULL, '', '', '', 90, 0, 1, '', '2018-04-05 11:34:27', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_9/'),
(19, 4, 0, 1, '2018-04-05 11:34:27', '2018-04-05 11:34:27', 0, 0, 'tovar_10', 'Товар 10', '<p>Импульс устойчиво отталкивает конституционный свежеприготовленный раствор. Неоднородность иллюстрирует восстановитель. Принцип \nартистизма выслеживает трог, хотя в официозе принято обратное.</p>\n<p>Ритмическая организованность таких стихов не всегда очевидна при чтении \"про себя\", но внутридискретное арпеджио непрерывно. Гетерогенная структура, учитывая отсутствие в законе норм, посвященных данному вопросу, представляет собой энергетический подуровень. Движимое имущество пространственно неоднородно. Хорус совершает твердый восстановитель. Верховое болото реально формирует лирический сарос одинаково по всем направлениям. Делькредере астатично.</p>\n<p>Пескование, на первый взгляд, законодательно. Португальская колонизация на следующий год, когда было лунное затмение и сгорел древний храм Афины в Афинах (при эфоре Питии и афинском архонте Каллии), готично совершает фотоиндуцированный энергетический перенос. Социальный статус, так или иначе, возбуждает косвенный рисчоррит.</p>', '', NULL, NULL, '', '', '', 100, 0, 1, '', '2018-04-05 11:34:27', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 18, '/catalog/category1/category11/category111/tovar_10/'),
(20, 6, 0, 1, '2019-03-29 14:50:50', '2019-03-29 14:52:21', 1, 1, 'vopros_1', 'Вопрос 1', '', '', '', '', '', '', '', 0, 0, 1, '', '2019-03-29 14:50:50', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(21, 6, 0, 1, '2019-03-29 14:50:59', '2019-03-29 14:52:25', 1, 1, 'vopros_2', 'Вопрос 2', '', '', '', '', '', '', '', 0, 0, 1, '', '2019-03-29 14:50:59', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(22, 6, 0, 1, '2019-03-29 14:51:02', '2019-03-29 14:52:30', 1, 1, 'vopros_3', 'Вопрос 3', '', '', '', '', '', '', '', 0, 0, 1, '', '2019-03-29 14:51:02', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(23, 7, 0, 1, '2019-03-29 14:51:10', '2019-03-29 14:52:35', 1, 1, 'otzyv_1', 'Отзыв 1', '', '', '', '', '', '', '', 0, 0, 1, '', '2019-03-29 14:51:10', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(24, 7, 0, 1, '2019-03-29 14:51:14', '2019-03-29 14:52:39', 1, 1, 'otzyv_2', 'Отзыв 2', '', '', '', '', '', '', '', 0, 0, 1, '', '2019-03-29 14:51:14', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, ''),
(25, 7, 0, 1, '2019-03-29 14:51:23', '2019-03-29 14:52:44', 1, 1, 'otzyv_3', 'Отзыв 3', '', '', '', '', '', '', '', 0, 0, 1, '', '2019-03-29 14:51:23', '0.50', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_materials_affected_pages_cache`
--

DROP TABLE IF EXISTS `cms_materials_affected_pages_cache`;
CREATE TABLE IF NOT EXISTS `cms_materials_affected_pages_cache` (
  `material_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  PRIMARY KEY (`material_id`,`page_id`),
  KEY `material_id` (`material_id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Materials affected pages';

--
-- Дамп данных таблицы `cms_materials_affected_pages_cache`
--

INSERT INTO `cms_materials_affected_pages_cache` (`material_id`, `page_id`) VALUES
(7, 7),
(8, 7),
(9, 7),
(10, 18),
(10, 19),
(10, 20),
(10, 21),
(10, 22),
(10, 23),
(10, 24),
(11, 18),
(11, 19),
(11, 20),
(11, 21),
(11, 22),
(11, 23),
(11, 24),
(12, 18),
(12, 19),
(12, 20),
(12, 21),
(12, 22),
(12, 23),
(12, 24),
(13, 18),
(13, 19),
(13, 20),
(13, 21),
(13, 22),
(13, 23),
(13, 24),
(14, 18),
(14, 19),
(14, 20),
(14, 21),
(14, 22),
(14, 23),
(14, 24),
(15, 18),
(15, 19),
(15, 20),
(15, 21),
(15, 22),
(15, 23),
(15, 24),
(16, 18),
(16, 19),
(16, 20),
(16, 21),
(16, 22),
(16, 23),
(16, 24),
(17, 18),
(17, 19),
(17, 20),
(17, 21),
(17, 22),
(17, 23),
(17, 24),
(18, 18),
(18, 19),
(18, 20),
(18, 21),
(18, 22),
(18, 23),
(18, 24),
(19, 18),
(19, 19),
(19, 20),
(19, 21),
(19, 22),
(19, 23),
(19, 24);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_materials_pages_assoc`
--

DROP TABLE IF EXISTS `cms_materials_pages_assoc`;
CREATE TABLE IF NOT EXISTS `cms_materials_pages_assoc` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  PRIMARY KEY (`id`,`pid`),
  KEY `id` (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Materials to pages associations';

--
-- Дамп данных таблицы `cms_materials_pages_assoc`
--

INSERT INTO `cms_materials_pages_assoc` (`id`, `pid`) VALUES
(10, 1),
(10, 18),
(10, 19),
(10, 20),
(10, 21),
(10, 22),
(10, 23),
(10, 24),
(11, 1),
(11, 18),
(11, 19),
(11, 20),
(11, 21),
(11, 22),
(11, 23),
(11, 24),
(12, 1),
(12, 18),
(12, 19),
(12, 20),
(12, 21),
(12, 22),
(12, 23),
(12, 24),
(13, 1),
(13, 18),
(13, 19),
(13, 20),
(13, 21),
(13, 22),
(13, 23),
(13, 24),
(14, 1),
(14, 18),
(14, 19),
(14, 20),
(14, 21),
(14, 22),
(14, 23),
(14, 24),
(15, 1),
(15, 18),
(15, 19),
(15, 20),
(15, 21),
(15, 22),
(15, 23),
(15, 24),
(16, 1),
(16, 18),
(16, 19),
(16, 20),
(16, 21),
(16, 22),
(16, 23),
(16, 24),
(17, 1),
(17, 18),
(17, 19),
(17, 20),
(17, 21),
(17, 22),
(17, 23),
(17, 24),
(18, 1),
(18, 18),
(18, 19),
(18, 20),
(18, 21),
(18, 22),
(18, 23),
(18, 24),
(19, 1),
(19, 18),
(19, 19),
(19, 20),
(19, 21),
(19, 22),
(19, 23),
(19, 24),
(20, 15),
(20, 16),
(20, 17),
(20, 18),
(20, 19),
(20, 20),
(20, 21),
(20, 22),
(20, 23),
(20, 24),
(21, 15),
(21, 16),
(21, 17),
(21, 18),
(21, 19),
(21, 20),
(21, 21),
(21, 22),
(21, 23),
(21, 24),
(22, 15),
(22, 16),
(22, 17),
(22, 18),
(22, 19),
(22, 20),
(22, 21),
(22, 22),
(22, 23),
(22, 24),
(23, 15),
(23, 16),
(23, 17),
(23, 18),
(23, 19),
(23, 20),
(23, 21),
(23, 22),
(23, 23),
(23, 24),
(24, 15),
(24, 16),
(24, 17),
(24, 18),
(24, 19),
(24, 20),
(24, 21),
(24, 22),
(24, 23),
(24, 24),
(25, 15),
(25, 16),
(25, 17),
(25, 18),
(25, 19),
(25, 20),
(25, 21),
(25, 22),
(25, 23),
(25, 24);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_material_types`
--

DROP TABLE IF EXISTS `cms_material_types`;
CREATE TABLE IF NOT EXISTS `cms_material_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent type ID#',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `global_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Global materials',
  PRIMARY KEY (`id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Material types';

--
-- Дамп данных таблицы `cms_material_types`
--

INSERT INTO `cms_material_types` (`id`, `pid`, `urn`, `name`, `global_type`) VALUES
(1, 0, 'features', 'Наши преимущества', 1),
(2, 0, 'banners', 'Баннеры', 1),
(3, 0, 'news', 'Новости', 1),
(4, 0, 'catalog', 'Каталог продукции', 0),
(5, 4, 'special', 'Особые товары', 0),
(6, 0, 'goods_faq', 'Вопрос-ответ к товарам', 0),
(7, 0, 'goods_comments', 'Отзывы к товарам', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_material_types_affected_pages_for_materials_cache`
--

DROP TABLE IF EXISTS `cms_material_types_affected_pages_for_materials_cache`;
CREATE TABLE IF NOT EXISTS `cms_material_types_affected_pages_for_materials_cache` (
  `material_type_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  `nat` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'NAT',
  PRIMARY KEY (`material_type_id`,`page_id`),
  KEY `material_type_id` (`material_type_id`),
  KEY `page_id` (`page_id`),
  KEY `nat` (`nat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Material types affected pages for materials';

--
-- Дамп данных таблицы `cms_material_types_affected_pages_for_materials_cache`
--

INSERT INTO `cms_material_types_affected_pages_for_materials_cache` (`material_type_id`, `page_id`, `nat`) VALUES
(1, 1, 0),
(2, 1, 0),
(2, 20, 0),
(3, 1, 0),
(3, 2, 0),
(3, 3, 0),
(3, 4, 0),
(3, 5, 0),
(3, 6, 0),
(3, 8, 0),
(3, 9, 0),
(3, 10, 0),
(3, 15, 0),
(3, 16, 0),
(3, 17, 0),
(3, 18, 0),
(3, 19, 0),
(3, 20, 0),
(3, 21, 0),
(3, 22, 0),
(3, 23, 0),
(3, 24, 0),
(3, 25, 0),
(3, 27, 0),
(3, 30, 0),
(3, 31, 0),
(3, 32, 0),
(3, 33, 0),
(4, 1, 0),
(5, 1, 0),
(3, 7, 1),
(4, 15, 1),
(4, 16, 1),
(4, 17, 1),
(4, 18, 1),
(4, 19, 1),
(4, 20, 1),
(4, 21, 1),
(4, 22, 1),
(4, 23, 1),
(4, 24, 1),
(5, 15, 1),
(5, 16, 1),
(5, 17, 1),
(5, 18, 1),
(5, 19, 1),
(5, 20, 1),
(5, 21, 1),
(5, 22, 1),
(5, 23, 1),
(5, 24, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_material_types_affected_pages_for_self_cache`
--

DROP TABLE IF EXISTS `cms_material_types_affected_pages_for_self_cache`;
CREATE TABLE IF NOT EXISTS `cms_material_types_affected_pages_for_self_cache` (
  `material_type_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  `nat` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'NAT',
  PRIMARY KEY (`material_type_id`,`page_id`),
  KEY `material_type_id` (`material_type_id`),
  KEY `page_id` (`page_id`),
  KEY `nat` (`nat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Material types affected pages for self (for admin)';

--
-- Дамп данных таблицы `cms_material_types_affected_pages_for_self_cache`
--

INSERT INTO `cms_material_types_affected_pages_for_self_cache` (`material_type_id`, `page_id`, `nat`) VALUES
(1, 1, 0),
(2, 1, 0),
(2, 20, 0),
(3, 1, 0),
(3, 2, 0),
(3, 3, 0),
(3, 4, 0),
(3, 5, 0),
(3, 6, 0),
(3, 8, 0),
(3, 9, 0),
(3, 10, 0),
(3, 15, 0),
(3, 16, 0),
(3, 17, 0),
(3, 18, 0),
(3, 19, 0),
(3, 20, 0),
(3, 21, 0),
(3, 22, 0),
(3, 23, 0),
(3, 24, 0),
(3, 25, 0),
(3, 27, 0),
(3, 30, 0),
(3, 31, 0),
(3, 32, 0),
(3, 33, 0),
(4, 1, 0),
(6, 15, 0),
(6, 16, 0),
(6, 17, 0),
(6, 18, 0),
(6, 19, 0),
(6, 20, 0),
(6, 21, 0),
(6, 22, 0),
(6, 23, 0),
(6, 24, 0),
(7, 15, 0),
(7, 16, 0),
(7, 17, 0),
(7, 18, 0),
(7, 19, 0),
(7, 20, 0),
(7, 21, 0),
(7, 22, 0),
(7, 23, 0),
(7, 24, 0),
(3, 7, 1),
(4, 15, 1),
(4, 16, 1),
(4, 17, 1),
(4, 18, 1),
(4, 19, 1),
(4, 20, 1),
(4, 21, 1),
(4, 22, 1),
(4, 23, 1),
(4, 24, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_material_types_tree_cache`
--

DROP TABLE IF EXISTS `cms_material_types_tree_cache`;
CREATE TABLE IF NOT EXISTS `cms_material_types_tree_cache` (
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `child_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Child ID#',
  `__level` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Level',
  PRIMARY KEY (`child_id`,`__level`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`),
  KEY `__level` (`__level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Material types tree cache';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_menus`
--

DROP TABLE IF EXISTS `cms_menus`;
CREATE TABLE IF NOT EXISTS `cms_menus` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `domain_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Domain ID#',
  `vis` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Visibility',
  `pvis` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Parent visibility',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT 'URL',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  `inherit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Nesting level',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `page_id` (`page_id`),
  KEY `urn` (`urn`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Menus';

--
-- Дамп данных таблицы `cms_menus`
--

INSERT INTO `cms_menus` (`id`, `pid`, `domain_id`, `vis`, `pvis`, `name`, `urn`, `url`, `page_id`, `inherit`, `priority`) VALUES
(1, 0, 0, 1, 1, 'Верхнее меню', 'top', '/', 0, 0, 0),
(2, 0, 0, 1, 1, 'Нижнее меню', 'bottom', '/', 1, 1, 0),
(3, 0, 0, 1, 1, 'Карта сайта', 'sitemap', '/', 1, 10, 0),
(4, 0, 0, 1, 1, 'Левое меню', 'left', '/catalog/', 15, 10, 0),
(5, 1, 0, 1, 1, 'О компании', '', '/about/', 2, 9, 20),
(6, 1, 0, 1, 1, 'Услуги', '', '/services/', 3, 9, 30),
(7, 1, 0, 0, 1, 'Новости', '', '/news/', 7, 9, 40),
(8, 1, 0, 1, 1, 'Контакты', '', '/contacts/', 8, 9, 50),
(9, 1, 0, 1, 1, 'Каталог продукции', '', '/catalog/', 15, 9, 60),
(10, 1, 0, 1, 1, 'Главная', '', '/', 0, 0, 10);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_menus_tree_cache`
--

DROP TABLE IF EXISTS `cms_menus_tree_cache`;
CREATE TABLE IF NOT EXISTS `cms_menus_tree_cache` (
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `child_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Child ID#',
  `__level` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Level',
  PRIMARY KEY (`child_id`,`__level`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`),
  KEY `__level` (`__level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Menus tree cache';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_pages`
--

DROP TABLE IF EXISTS `cms_pages`;
CREATE TABLE IF NOT EXISTS `cms_pages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `vis` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Visibility',
  `pvis` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Parent visibility',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  `modify_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modify date',
  `author_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  `editor_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Editor ID#',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `response_code` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Service page response code',
  `mime` varchar(255) NOT NULL DEFAULT 'text/html' COMMENT 'MIME-type',
  `meta_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Meta title',
  `inherit_meta_title` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Inherit meta-title',
  `meta_description` text COMMENT 'Meta description',
  `inherit_meta_description` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Inherit meta-description',
  `meta_keywords` text COMMENT 'Meta keywords',
  `inherit_meta_keywords` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Inherit meta-keywords',
  `h1` varchar(255) NOT NULL DEFAULT '' COMMENT 'H1 title',
  `menu_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Menu name',
  `breadcrumbs_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Breadcrumbs name',
  `template` varchar(255) NOT NULL DEFAULT '' COMMENT 'Template',
  `inherit_template` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Inherit meta-title',
  `lang` varchar(255) NOT NULL DEFAULT '' COMMENT 'Language',
  `inherit_lang` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Inherit language',
  `nat` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Translate address',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  `cache` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cache page',
  `inherit_cache` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Inherit cache page',
  `visit_counter` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Visit counter',
  `modify_counter` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modify counter',
  `changefreq` enum('','always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT '' COMMENT 'Change frequency',
  `inherit_changefreq` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Inherit change frequency',
  `last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last modified',
  `sitemaps_priority` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.50' COMMENT 'Sitemaps priority',
  `inherit_sitemaps_priority` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Inherit sitemaps priority',
  `cache_url` varchar(255) NOT NULL DEFAULT '/' COMMENT 'Cached URL',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `author_id` (`author_id`),
  KEY `editor_id` (`editor_id`),
  KEY `urn` (`urn`),
  KEY `template` (`template`),
  KEY `priority` (`priority`),
  KEY `cache_url` (`cache_url`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='Site pages';

--
-- Дамп данных таблицы `cms_pages`
--

INSERT INTO `cms_pages` (`id`, `pid`, `vis`, `pvis`, `post_date`, `modify_date`, `author_id`, `editor_id`, `urn`, `name`, `response_code`, `mime`, `meta_title`, `inherit_meta_title`, `meta_description`, `inherit_meta_description`, `meta_keywords`, `inherit_meta_keywords`, `h1`, `menu_name`, `breadcrumbs_name`, `template`, `inherit_template`, `lang`, `inherit_lang`, `nat`, `priority`, `cache`, `inherit_cache`, `visit_counter`, `modify_counter`, `changefreq`, `inherit_changefreq`, `last_modified`, `sitemaps_priority`, `inherit_sitemaps_priority`, `cache_url`) VALUES
(1, 0, 1, 1, '2018-04-05 11:33:47', '2018-04-22 10:22:34', 1, 1, 'test', 'Главная', 0, 'text/html', '', 0, '', 0, '', 0, '', '', '', '1', 0, 'ru', 1, 0, 1, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/'),
(2, 1, 1, 1, '2018-04-05 11:34:14', '2018-04-22 10:22:34', 1, 1, 'about', 'О компании', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 2, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/about/'),
(3, 1, 1, 1, '2018-04-05 11:34:14', '2018-04-22 10:22:34', 1, 1, 'services', 'Услуги', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 3, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/services/'),
(4, 3, 1, 1, '2018-04-05 11:34:14', '2018-04-22 10:22:34', 1, 1, 'service1', 'Услуга 1', 0, 'text/html', '', 0, '', 0, '', 0, '', '', '', '1', 0, 'ru', 1, 0, 4, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/services/service1/'),
(5, 3, 1, 1, '2018-04-05 11:34:14', '2018-04-22 10:22:34', 1, 1, 'service2', 'Услуга 2', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 5, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/services/service2/'),
(6, 3, 0, 1, '2018-04-05 11:34:14', '2018-04-22 11:57:39', 1, 1, 'service3', 'Услуга 3', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 6, 1, 0, 0, 3, '', 0, '2018-04-22 11:57:39', '0.50', 0, '/services/service3/'),
(7, 1, 1, 1, '2018-04-05 11:34:14', '2018-04-22 10:22:34', 1, 1, 'news', 'Новости', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 7, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/news/'),
(8, 1, 1, 1, '2018-04-05 11:34:15', '2018-04-22 10:22:34', 1, 1, 'contacts', 'Контакты', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 8, 1, 0, 0, 2, '', 0, '2018-04-22 13:59:43', '0.50', 0, '/contacts/'),
(9, 1, 1, 1, '2018-04-05 11:34:15', '2018-04-22 10:22:34', 1, 1, '404', '404 — страница не найдена', 404, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 9, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/404/'),
(10, 1, 1, 1, '2018-04-05 11:34:15', '2018-04-22 10:22:34', 1, 1, 'map', 'Карта сайта', 200, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 10, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/map/'),
(11, 1, 1, 1, '2018-04-05 11:34:15', '2018-04-22 10:22:34', 1, 1, 'sitemaps', 'sitemap.xml', 200, 'application/xml', '', 0, NULL, 0, NULL, 0, '', '', '', '0', 0, 'ru', 1, 0, 11, 0, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/sitemaps/'),
(12, 1, 1, 1, '2018-04-05 11:34:15', '2018-04-22 10:22:34', 1, 1, 'robots', 'robots.txt', 200, 'text/plain', '', 0, NULL, 0, NULL, 0, '', '', '', '0', 0, 'ru', 1, 0, 12, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/robots/'),
(13, 1, 1, 1, '2018-04-05 11:34:16', '2018-04-22 10:22:34', 1, 1, 'custom_css', 'custom.css', 200, 'text/css', '', 0, NULL, 0, NULL, 0, '', '', '', '0', 0, 'ru', 1, 0, 13, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/custom_css/'),
(14, 1, 1, 1, '2018-04-05 11:34:16', '2018-04-22 10:22:34', 1, 1, 'ajax', 'AJAX', 200, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '0', 0, 'ru', 1, 0, 14, 0, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/ajax/'),
(15, 1, 1, 1, '2018-04-05 11:34:23', '2018-04-22 10:22:34', 1, 1, 'catalog', 'Каталог продукции', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 15, 1, 0, 0, 2, '', 0, '2019-03-29 14:19:56', '0.50', 0, '/catalog/'),
(16, 15, 1, 1, '2018-04-05 11:34:23', '2018-04-22 10:22:34', 1, 1, 'category1', 'Категория 1', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 16, 1, 0, 0, 2, '', 0, '2019-03-29 14:21:32', '0.50', 0, '/catalog/category1/'),
(17, 16, 1, 1, '2018-04-05 11:34:24', '2018-04-22 10:22:34', 1, 1, 'category11', 'Категория 11', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 17, 1, 0, 0, 2, '', 0, '2019-03-29 14:21:32', '0.50', 0, '/catalog/category1/category11/'),
(18, 17, 1, 1, '2018-04-05 11:34:24', '2018-04-22 10:22:34', 1, 1, 'category111', 'Категория 111', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 18, 1, 0, 0, 2, '', 0, '2019-03-29 14:21:32', '0.50', 0, '/catalog/category1/category11/category111/'),
(19, 17, 1, 1, '2018-04-05 11:34:24', '2018-04-22 10:22:34', 1, 1, 'category112', 'Категория 112', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 19, 1, 0, 0, 2, '', 0, '2019-03-29 14:21:32', '0.50', 0, '/catalog/category1/category11/category112/'),
(20, 17, 1, 1, '2018-04-05 11:34:24', '2018-04-22 10:22:34', 1, 1, 'category113', 'Категория 113', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 20, 1, 0, 0, 2, '', 0, '2019-03-29 14:21:32', '0.50', 0, '/catalog/category1/category11/category113/'),
(21, 16, 1, 1, '2018-04-05 11:34:25', '2018-04-22 10:22:34', 1, 1, 'category12', 'Категория 12', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 21, 1, 0, 0, 2, '', 0, '2019-03-29 14:21:32', '0.50', 0, '/catalog/category1/category12/'),
(22, 16, 1, 1, '2018-04-05 11:34:25', '2018-04-22 10:22:34', 1, 1, 'category13', 'Категория 13', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 22, 1, 0, 0, 2, '', 0, '2019-03-29 14:21:32', '0.50', 0, '/catalog/category1/category13/'),
(23, 15, 1, 1, '2018-04-05 11:34:25', '2018-04-22 10:22:34', 1, 1, 'category2', 'Категория 2', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 23, 1, 0, 0, 2, '', 0, '2019-03-29 14:21:32', '0.50', 0, '/catalog/category2/'),
(24, 15, 1, 1, '2018-04-05 11:34:26', '2018-04-22 10:22:34', 1, 1, 'category3', 'Категория 3', 0, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 24, 1, 0, 0, 2, '', 0, '2019-03-29 14:21:32', '0.50', 0, '/catalog/category3/'),
(25, 1, 1, 1, '2018-04-05 11:34:28', '2018-04-22 11:53:40', 1, 1, 'cart', 'Корзина', 200, 'text/html', '', 0, '', 0, '', 0, '', '', '', '1', 0, 'ru', 1, 1, 25, 0, 0, 0, 3, '', 0, '2018-04-22 11:53:40', '0.50', 0, '/cart/'),
(26, 14, 1, 1, '2018-04-05 11:34:28', '2018-04-22 10:22:34', 1, 1, 'cart', 'Корзина', 200, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '0', 0, 'ru', 1, 0, 26, 0, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/ajax/cart/'),
(27, 1, 1, 1, '2018-04-05 11:34:28', '2018-04-22 10:22:34', 1, 1, 'favorites', 'Избранное', 200, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 27, 0, 0, 0, 2, '', 0, '2018-04-22 09:45:25', '0.50', 0, '/favorites/'),
(28, 14, 1, 1, '2018-04-05 11:34:29', '2018-04-22 10:22:34', 1, 1, 'favorites', 'Избранное', 200, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '0', 0, 'ru', 1, 0, 28, 0, 0, 0, 2, '', 0, '2018-04-22 09:45:24', '0.50', 0, '/ajax/favorites/'),
(29, 1, 1, 1, '2018-04-05 11:34:29', '2018-04-22 10:22:34', 1, 1, 'yml', 'Яндекс.Маркет', 200, 'application/xml', '', 0, NULL, 0, NULL, 0, '', '', '', '0', 0, 'ru', 1, 0, 29, 1, 0, 0, 2, '', 0, '2018-04-22 09:45:25', '0.50', 0, '/yml/'),
(30, 1, 1, 1, '2018-04-22 09:45:24', '2018-04-22 14:01:09', 1, 1, 'register', 'Регистрация', 200, 'text/html', '', 0, '', 0, '', 0, '', '', '', '1', 0, 'ru', 1, 0, 30, 1, 0, 0, 3, '', 0, '2018-04-22 14:01:09', '0.50', 0, '/register/'),
(31, 1, 1, 1, '2018-04-22 09:45:25', '2018-04-22 10:22:34', 1, 1, 'activate', 'Активация', 200, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 31, 0, 0, 0, 2, '', 0, '2018-04-22 09:45:25', '0.50', 0, '/activate/'),
(32, 1, 1, 1, '2018-04-22 09:45:25', '2018-04-22 10:22:34', 1, 1, 'login', 'Вход в систему', 200, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 32, 0, 0, 0, 2, '', 0, '2018-04-22 09:45:25', '0.50', 0, '/login/'),
(33, 1, 1, 1, '2018-04-22 09:45:26', '2018-04-22 10:22:34', 1, 1, 'recovery', 'Восстановление пароля', 200, 'text/html', '', 0, NULL, 0, NULL, 0, '', '', '', '1', 0, 'ru', 1, 0, 33, 0, 0, 0, 2, '', 0, '2018-04-22 09:45:26', '0.50', 0, '/recovery/');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_pages_data`
--

DROP TABLE IF EXISTS `cms_pages_data`;
CREATE TABLE IF NOT EXISTS `cms_pages_data` (
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  `fid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Field ID#',
  `fii` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Field index',
  `value` text COMMENT 'Value',
  PRIMARY KEY (`pid`,`fid`,`fii`),
  KEY `pid` (`pid`),
  KEY `fid` (`fid`),
  KEY `fii` (`fii`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pages fields';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_pages_tree_cache`
--

DROP TABLE IF EXISTS `cms_pages_tree_cache`;
CREATE TABLE IF NOT EXISTS `cms_pages_tree_cache` (
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `child_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Child ID#',
  `__level` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Level',
  PRIMARY KEY (`child_id`,`__level`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`),
  KEY `__level` (`__level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pages tree cache';

--
-- Дамп данных таблицы `cms_pages_tree_cache`
--

INSERT INTO `cms_pages_tree_cache` (`parent_id`, `child_id`, `__level`) VALUES
(1, 2, 1),
(1, 3, 1),
(1, 4, 2),
(1, 5, 2),
(1, 6, 2),
(1, 7, 1),
(1, 8, 1),
(1, 9, 1),
(1, 10, 1),
(1, 11, 1),
(1, 12, 1),
(1, 13, 1),
(1, 14, 1),
(1, 15, 1),
(1, 16, 2),
(1, 17, 3),
(1, 18, 4),
(1, 19, 4),
(1, 20, 4),
(1, 21, 3),
(1, 22, 3),
(1, 23, 2),
(1, 24, 2),
(1, 25, 1),
(1, 26, 2),
(1, 27, 1),
(1, 28, 2),
(1, 29, 1),
(3, 4, 1),
(3, 5, 1),
(3, 6, 1),
(14, 26, 1),
(14, 28, 1),
(15, 16, 1),
(15, 17, 2),
(15, 18, 3),
(15, 19, 3),
(15, 20, 3),
(15, 21, 2),
(15, 22, 2),
(15, 23, 1),
(15, 24, 1),
(16, 17, 1),
(16, 18, 2),
(16, 19, 2),
(16, 20, 2),
(16, 21, 1),
(16, 22, 1),
(17, 18, 1),
(17, 19, 1),
(17, 20, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_redirects`
--

DROP TABLE IF EXISTS `cms_redirects`;
CREATE TABLE IF NOT EXISTS `cms_redirects` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `rx` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'RegExp',
  `url_from` varchar(255) NOT NULL DEFAULT '' COMMENT 'URL from',
  `url_to` varchar(255) NOT NULL DEFAULT '' COMMENT 'URL to',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (`id`),
  KEY `url_from` (`url_from`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Redirects';

--
-- Дамп данных таблицы `cms_redirects`
--

INSERT INTO `cms_redirects` (`id`, `rx`, `url_from`, `url_to`, `priority`) VALUES
(1, 0, '/sitemaps.xml', '/sitemap.xml', 0),
(2, 0, '/www.', '/', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_blocks_cart`
--

DROP TABLE IF EXISTS `cms_shop_blocks_cart`;
CREATE TABLE IF NOT EXISTS `cms_shop_blocks_cart` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID#',
  `cart_type` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cart type ID#',
  `epay_interface_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'E-pay interface ID#',
  `epay_login` varchar(255) NOT NULL DEFAULT '' COMMENT 'E-pay login',
  `epay_pass1` varchar(255) NOT NULL DEFAULT '' COMMENT 'E-pay pass1',
  `epay_pass2` varchar(255) NOT NULL DEFAULT '' COMMENT 'E-pay pass2',
  `epay_test` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'E-pay test mode',
  `epay_currency` varchar(255) NOT NULL DEFAULT '' COMMENT 'Currency',
  PRIMARY KEY (`id`),
  KEY `cart_type` (`cart_type`),
  KEY `epay_interface_id` (`epay_interface_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Cart blocks';

--
-- Дамп данных таблицы `cms_shop_blocks_cart`
--

INSERT INTO `cms_shop_blocks_cart` (`id`, `cart_type`, `epay_interface_id`, `epay_login`, `epay_pass1`, `epay_pass2`, `epay_test`, `epay_currency`) VALUES
(38, 1, 0, '', '', '', 0, ''),
(40, 1, 0, '', '', '', 0, ''),
(41, 2, 0, '', '', '', 0, ''),
(43, 2, 0, '', '', '', 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_blocks_yml`
--

DROP TABLE IF EXISTS `cms_shop_blocks_yml`;
CREATE TABLE IF NOT EXISTS `cms_shop_blocks_yml` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `shop_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Shop name',
  `company` varchar(255) NOT NULL DEFAULT '' COMMENT 'Company name',
  `agency` varchar(255) NOT NULL DEFAULT '' COMMENT 'Support company name',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT 'Support e-mail',
  `cpa` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'YM purchase',
  `default_currency` varchar(255) NOT NULL DEFAULT '' COMMENT 'Default currency',
  `local_delivery_cost` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT 'Local delivery cost',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='Yandex Market blocks';

--
-- Дамп данных таблицы `cms_shop_blocks_yml`
--

INSERT INTO `cms_shop_blocks_yml` (`id`, `shop_name`, `company`, `agency`, `email`, `cpa`, `default_currency`, `local_delivery_cost`) VALUES
(44, '', '', 'Volume Networks', 'info@volumnet.ru', 0, 'RUR', '0.00');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_blocks_yml_currencies`
--

DROP TABLE IF EXISTS `cms_shop_blocks_yml_currencies`;
CREATE TABLE IF NOT EXISTS `cms_shop_blocks_yml_currencies` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  `currency_name` varchar(8) NOT NULL DEFAULT '' COMMENT 'Currency ID#',
  `currency_rate` varchar(255) NOT NULL DEFAULT '' COMMENT 'Currency rate',
  `currency_plus` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT 'Currency plus, %%',
  PRIMARY KEY (`id`,`currency_name`),
  KEY `currency_name` (`currency_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currencies';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_blocks_yml_fields`
--

DROP TABLE IF EXISTS `cms_shop_blocks_yml_fields`;
CREATE TABLE IF NOT EXISTS `cms_shop_blocks_yml_fields` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  `mtype` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `field_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field name',
  `field_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field ID#',
  `field_callback` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field callback',
  `field_static_value` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field static value',
  PRIMARY KEY (`id`,`mtype`,`field_name`),
  KEY `id` (`id`),
  KEY `mtype` (`mtype`),
  KEY `field_id` (`field_id`),
  KEY `field_name` (`field_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='YM blocks to material types fields';

--
-- Дамп данных таблицы `cms_shop_blocks_yml_fields`
--

INSERT INTO `cms_shop_blocks_yml_fields` (`id`, `mtype`, `field_name`, `field_id`, `field_callback`, `field_static_value`) VALUES
(44, 4, 'available', '31', '', ''),
(44, 4, 'currencyId', '', '', 'RUR'),
(44, 4, 'delivery', '', '', '1'),
(44, 4, 'description', 'description', '', ''),
(44, 4, 'name', 'name', '', ''),
(44, 4, 'oldprice', '34', '', ''),
(44, 4, 'pickup', '', '', '1'),
(44, 4, 'picture', '27', '', ''),
(44, 4, 'price', '26', '', ''),
(44, 4, 'rec', '', '', ''),
(44, 4, 'vendorCode', '25', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_blocks_yml_ignored_fields`
--

DROP TABLE IF EXISTS `cms_shop_blocks_yml_ignored_fields`;
CREATE TABLE IF NOT EXISTS `cms_shop_blocks_yml_ignored_fields` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  `mtype` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `field_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field ID#',
  PRIMARY KEY (`id`,`mtype`,`field_id`),
  KEY `id` (`id`),
  KEY `mtype` (`mtype`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='YM blocks to material types params ignored fields';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_blocks_yml_material_types_assoc`
--

DROP TABLE IF EXISTS `cms_shop_blocks_yml_material_types_assoc`;
CREATE TABLE IF NOT EXISTS `cms_shop_blocks_yml_material_types_assoc` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  `mtype` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT 'YM type',
  `param_exceptions` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Params from all fields except...',
  `params_callback` varchar(255) NOT NULL DEFAULT '' COMMENT 'Params callback',
  PRIMARY KEY (`id`,`mtype`),
  KEY `id` (`id`),
  KEY `mtype` (`mtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='YM blocks to material types association';

--
-- Дамп данных таблицы `cms_shop_blocks_yml_material_types_assoc`
--

INSERT INTO `cms_shop_blocks_yml_material_types_assoc` (`id`, `mtype`, `type`, `param_exceptions`, `params_callback`) VALUES
(44, 4, '', 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_blocks_yml_pages_assoc`
--

DROP TABLE IF EXISTS `cms_shop_blocks_yml_pages_assoc`;
CREATE TABLE IF NOT EXISTS `cms_shop_blocks_yml_pages_assoc` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  PRIMARY KEY (`id`,`page_id`),
  KEY `id` (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='YM blocks to pages association';

--
-- Дамп данных таблицы `cms_shop_blocks_yml_pages_assoc`
--

INSERT INTO `cms_shop_blocks_yml_pages_assoc` (`id`, `page_id`) VALUES
(44, 15),
(44, 16),
(44, 17),
(44, 18),
(44, 19),
(44, 20),
(44, 21),
(44, 22),
(44, 23),
(44, 24);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_blocks_yml_params`
--

DROP TABLE IF EXISTS `cms_shop_blocks_yml_params`;
CREATE TABLE IF NOT EXISTS `cms_shop_blocks_yml_params` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Block ID#',
  `mtype` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `param_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Param name',
  `field_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field ID#',
  `field_callback` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field callback',
  `param_unit` varchar(255) NOT NULL DEFAULT '' COMMENT 'Param unit',
  `param_static_value` varchar(255) NOT NULL DEFAULT '' COMMENT 'Param static value',
  PRIMARY KEY (`id`,`mtype`,`param_name`),
  KEY `id` (`id`),
  KEY `mtype` (`mtype`),
  KEY `field_id` (`field_id`),
  KEY `param_name` (`param_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='YM blocks to material types params';

--
-- Дамп данных таблицы `cms_shop_blocks_yml_params`
--

INSERT INTO `cms_shop_blocks_yml_params` (`id`, `mtype`, `param_name`, `field_id`, `field_callback`, `param_unit`, `param_static_value`) VALUES
(44, 4, 'Спецпредложение', '30', 'return $x ? \'true\' : \'false\';', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_carts`
--

DROP TABLE IF EXISTS `cms_shop_carts`;
CREATE TABLE IF NOT EXISTS `cms_shop_carts` (
  `cart_type_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cart type ID#',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `material_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  `meta` varchar(255) NOT NULL DEFAULT '' COMMENT 'Meta',
  `amount` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Amount',
  PRIMARY KEY (`cart_type_id`,`uid`,`material_id`,`meta`),
  KEY `cart_type_id` (`cart_type_id`),
  KEY `uid` (`uid`),
  KEY `material_id` (`material_id`),
  KEY `meta` (`meta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Cart sessions';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_cart_types`
--

DROP TABLE IF EXISTS `cms_shop_cart_types`;
CREATE TABLE IF NOT EXISTS `cms_shop_cart_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `form_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Form ID#',
  `no_amount` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Single item of a type',
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Cart types';

--
-- Дамп данных таблицы `cms_shop_cart_types`
--

INSERT INTO `cms_shop_cart_types` (`id`, `urn`, `name`, `form_id`, `no_amount`) VALUES
(1, 'cart', 'Корзина', 3, 0),
(2, 'favorites', 'Избранное', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_cart_types_material_types_assoc`
--

DROP TABLE IF EXISTS `cms_shop_cart_types_material_types_assoc`;
CREATE TABLE IF NOT EXISTS `cms_shop_cart_types_material_types_assoc` (
  `ctype` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cart type ID#',
  `mtype` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `price_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Price field ID#',
  `price_callback` text COMMENT 'Price callback',
  PRIMARY KEY (`ctype`,`mtype`),
  KEY `ctype` (`ctype`),
  KEY `mtype` (`mtype`),
  KEY `price_id` (`price_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Cart types to material types association';

--
-- Дамп данных таблицы `cms_shop_cart_types_material_types_assoc`
--

INSERT INTO `cms_shop_cart_types_material_types_assoc` (`ctype`, `mtype`, `price_id`, `price_callback`) VALUES
(1, 4, 26, ''),
(2, 4, 26, '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_imageloaders`
--

DROP TABLE IF EXISTS `cms_shop_imageloaders`;
CREATE TABLE IF NOT EXISTS `cms_shop_imageloaders` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `mtype` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `ufid` varchar(255) NOT NULL DEFAULT '' COMMENT 'Unique field ID#',
  `ifid` varchar(255) NOT NULL DEFAULT '' COMMENT 'Image field ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `sep_string` varchar(255) NOT NULL DEFAULT '' COMMENT 'Separator string',
  `interface_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Interface ID#',
  PRIMARY KEY (`id`),
  KEY `mtype` (`mtype`),
  KEY `ufid` (`ufid`),
  KEY `ifid` (`ifid`),
  KEY `interface_id` (`interface_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Image loaders';

--
-- Дамп данных таблицы `cms_shop_imageloaders`
--

INSERT INTO `cms_shop_imageloaders` (`id`, `mtype`, `ufid`, `ifid`, `name`, `urn`, `sep_string`, `interface_id`) VALUES
(1, 4, '25', '27', 'Стандартный загрузчик изображений', 'default', '_', 26);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_orders`
--

DROP TABLE IF EXISTS `cms_shop_orders`;
CREATE TABLE IF NOT EXISTS `cms_shop_orders` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Site user ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cart type ID#',
  `page_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Page ID#',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  `vis` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Visited',
  `ip` varchar(255) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP address',
  `user_agent` varchar(255) NOT NULL DEFAULT '0.0.0.0' COMMENT 'User Agent',
  `status_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Status ID#',
  `paid` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Payment status',
  `payment_interface_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Payment interface ID#',
  `payment_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'Payment ID#',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `pid` (`pid`),
  KEY `page_id` (`page_id`),
  KEY `status_id` (`status_id`),
  KEY `paid` (`paid`),
  KEY `payment_id` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Orders';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_orders_goods`
--

DROP TABLE IF EXISTS `cms_shop_orders_goods`;
CREATE TABLE IF NOT EXISTS `cms_shop_orders_goods` (
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Order ID#',
  `material_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material ID#',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT 'Name',
  `meta` varchar(255) NOT NULL DEFAULT '' COMMENT 'Meta data',
  `realprice` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Real price',
  `amount` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Amount',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (`order_id`,`material_id`,`meta`),
  KEY `order_id` (`order_id`),
  KEY `material_id` (`material_id`),
  KEY `meta` (`meta`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Orders goods';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_orders_history`
--

DROP TABLE IF EXISTS `cms_shop_orders_history`;
CREATE TABLE IF NOT EXISTS `cms_shop_orders_history` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Order ID#',
  `status_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Status ID#',
  `paid` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Payment status',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Status post date',
  `description` text COMMENT 'Description',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `order_id` (`order_id`),
  KEY `status_id` (`status_id`),
  KEY `paid` (`paid`),
  KEY `post_date` (`post_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Orders history';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_orders_statuses`
--

DROP TABLE IF EXISTS `cms_shop_orders_statuses`;
CREATE TABLE IF NOT EXISTS `cms_shop_orders_statuses` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `do_notify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Notify user',
  `notification_title` text COMMENT 'User notification title',
  `notification` text COMMENT 'User notification',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (`id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Orders statuses';

--
-- Дамп данных таблицы `cms_shop_orders_statuses`
--

INSERT INTO `cms_shop_orders_statuses` (`id`, `urn`, `name`, `do_notify`, `notification_title`, `notification`, `priority`) VALUES
(1, 'progress', 'В обработке', 0, 'Ваш заказ №{{ID}} переведен в статус \"В обработке\"', '<p>Доброго времени суток!</p>\n\n<p>Ваш заказ №{{ID}} переведен в статус &quot;В обработке&quot;</p>\n\n<p>--</p>\n\n<p>\n  С уважением,<br />\n  Администрация сайта <a href=\"http://lab\">lab</a>\n</p>', 1),
(2, 'completed', 'Обработан', 0, 'Ваш заказ №{{ID}} обработан', '<p>Доброго времени суток!</p>\n\n<p>Ваш заказ №{{ID}} обработан</p>\n\n<p>--</p>\n\n<p>\n  С уважением,<br />\n  Администрация сайта <a href=\"http://lab\">lab</a>\n</p>', 2),
(3, 'canceled', 'Отменен', 0, 'Ваш заказ №{{ID}} отменен', '<p>Доброго времени суток!</p>\n\n<p>Ваш заказ №{{ID}} отменен</p>\n\n<p>--</p>\n\n<p>\n  С уважением,<br />\n  Администрация сайта <a href=\"http://lab\">lab</a>\n</p>', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_priceloaders`
--

DROP TABLE IF EXISTS `cms_shop_priceloaders`;
CREATE TABLE IF NOT EXISTS `cms_shop_priceloaders` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `mtype` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Material type ID#',
  `ufid` varchar(255) NOT NULL DEFAULT '' COMMENT 'Unique field ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `interface_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Interface ID#',
  `rows` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Rows from top',
  `cols` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cols from left',
  `cat_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Root category ID#',
  `create_pages` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Allow to create pages',
  `create_materials` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Allow to create materials',
  `catalog_offset` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Catalog offset',
  PRIMARY KEY (`id`),
  KEY `mtype` (`mtype`),
  KEY `ufid` (`ufid`),
  KEY `interface_id` (`interface_id`),
  KEY `cat_id` (`cat_id`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Price loaders';

--
-- Дамп данных таблицы `cms_shop_priceloaders`
--

INSERT INTO `cms_shop_priceloaders` (`id`, `mtype`, `ufid`, `name`, `urn`, `interface_id`, `rows`, `cols`, `cat_id`, `create_pages`, `create_materials`, `catalog_offset`) VALUES
(1, 4, '25', 'Стандартный загрузчик прайсов', 'default', 27, 0, 0, 15, 0, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_shop_priceloaders_columns`
--

DROP TABLE IF EXISTS `cms_shop_priceloaders_columns`;
CREATE TABLE IF NOT EXISTS `cms_shop_priceloaders_columns` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Price loader ID#',
  `fid` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field ID#',
  `callback` text COMMENT 'Callback code',
  `callback_download` text COMMENT 'Download callback code',
  `priority` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `fid` (`fid`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Price loaders columns';

--
-- Дамп данных таблицы `cms_shop_priceloaders_columns`
--

INSERT INTO `cms_shop_priceloaders_columns` (`id`, `pid`, `fid`, `callback`, `callback_download`, `priority`) VALUES
(1, 1, '25', NULL, NULL, 1),
(2, 1, 'name', NULL, NULL, 2),
(3, 1, 'description', NULL, NULL, 3),
(4, 1, '35', '$y = array_filter(array_map(\'trim\', preg_split(\'/[;,]/umi\', $x)), \'trim\');\n$temp = array();\nforeach ($y as $val) {\n    $SQL_query = \"SELECT pid FROM cms_data WHERE fid = 25 AND value = \'\" . \\RAAS\\CMS\\Material::_SQL()->real_escape_string($val) . \"\'\";\n    if ($SQL_result = \\RAAS\\CMS\\Material::_SQL()->getvalue($SQL_query)) {\n        $temp[] = (int)$SQL_result;\n    }\n}\nreturn $temp;', '$temp = array();\nforeach ((array)$x as $val) {\n    $row = new \\RAAS\\CMS\\Material((int)$val);\n    if ($row->id) {\n        $temp[] = $row->article;\n    }\n}\nreturn implode(\', \', $temp);', 4),
(5, 1, '31', 'return ($x && (trim($x) !== \'0\')) ? (int)(bool)preg_match(\'/налич/umi\', $x) : 0;', 'return (int)$x ? \'в наличии\' : \'под заказ\';', 5),
(6, 1, '34', '$y = str_replace(\',\', \'.\', $x); $y = (float)preg_replace(\'/[^\\d\\.]+/i\', \'\', trim($x)); return $y;', NULL, 6),
(7, 1, '26', '$y = str_replace(\',\', \'.\', $x); $y = (float)preg_replace(\'/[^\\d\\.]+/i\', \'\', trim($y)); return $y;', NULL, 7);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_snippets`
--

DROP TABLE IF EXISTS `cms_snippets`;
CREATE TABLE IF NOT EXISTS `cms_snippets` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `description` text COMMENT 'Code',
  `locked` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Locked',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='Snippets';

--
-- Дамп данных таблицы `cms_snippets`
--

INSERT INTO `cms_snippets` (`id`, `pid`, `urn`, `name`, `description`, `locked`) VALUES
(1, 1, '__raas_material_interface', 'Стандартный интерфейс материала', '<?php\n/**\n * Стандартный интерфейс материалов\n * @param Block_Material $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS;\n\n$interface = new MaterialInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(2, 1, '__raas_form_interface', 'Стандартный интерфейс формы', '<?php\n/**\n * Стандартный интерфейс формы\n * @param Block_Form $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS;\n\n$interface = new FormInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(3, 1, '__raas_menu_interface', 'Стандартный интерфейс меню', '<?php\n/**\n * Стандартный интерфейс меню\n * @param Block_Menu $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS;\n\n$interface = new MenuInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(4, 1, '__raas_search_interface', 'Стандартный интерфейс поиска', '<?php\n/**\n * Стандартный интерфейс поиска\n * @param Block_Search $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS;\n\n$interface = new SearchInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(5, 1, '__raas_form_notify', 'Стандартное уведомление формы', '<?php\n/**\n * Стандартное уведомление о форме обратной связи\n * @param bool $SMS Уведомление отправляется по SMS\n * @param Feedback $Item Уведомление формы обратной связи\n * @param Material $Material Созданный материал\n */\nnamespace RAAS\\CMS;\n\n/**\n * Получает представление поля для отправки по SMS\n * @param Form_Field $field Поле для получения представления\n * @return string\n */\n$smsField = function (Form_Field $field) {\n    $values = $field->getValues(true);\n    $arr = array();\n    foreach ($values as $key => $val) {\n        $val = $field->doRich($val);\n        switch ($field->datatype) {\n            case \'date\':\n                $arr[$key] = date(DATEFORMAT, strtotime($val));\n                break;\n            case \'datetime-local\':\n                $arr[$key] = date(DATETIMEFORMAT, strtotime($val));\n                break;\n            case \'file\':\n            case \'image\':\n                $arr[$key] .= $val->filename;\n                break;\n            case \'htmlarea\':\n                $arr[$key] = strip_tags($val);\n                break;\n            case \'material\':\n                $arr[$key] = $val->name;\n                break;\n            default:\n                if (!$field->multiple && ($field->datatype == \'checkbox\')) {\n                    $arr[$key] = $val ? _YES : _NO;\n                } else {\n                    $arr[$key] = $val;\n                }\n                break;\n        }\n    }\n    return $field->name . \': \' . implode(\', \', $arr) . \"\\n\";\n};\n\n/**\n * Получает представление поля для отправки по электронной почте\n * @param Form_Field $field Поле для получения представления\n * @return string\n */\n$emailField = function (Form_Field $field) {\n    $values = $field->getValues(true);\n    $arr = array();\n    foreach ($values as $key => $val) {\n        $val = $field->doRich($val);\n        switch ($field->datatype) {\n            case \'date\':\n                $arr[$key] = date(DATEFORMAT, strtotime($val));\n                break;\n            case \'datetime-local\':\n                $arr[$key] = date(DATETIMEFORMAT, strtotime($val));\n                break;\n            case \'color\':\n                $arr[$key] = \'<span style=\"display: inline-block; height: 16px; width: 16px; background-color: \' . htmlspecialchars($val) . \'\"></span>\';\n                break;\n            case \'email\':\n                $arr[$key] .= \'<a href=\"mailto:\' . htmlspecialchars($val) . \'\">\' . htmlspecialchars($val) . \'</a>\';\n                break;\n            case \'url\':\n                $arr[$key] .= \'<a href=\"\' . (!preg_match(\'/^http(s)?:\\\\/\\\\//umi\', trim($val)) ? \'http://\' : \'\') . htmlspecialchars($val) . \'\">\' . htmlspecialchars($val) . \'</a>\';\n                break;\n            case \'file\':\n                $arr[$key] .= \'<a href=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . \'/\' . $val->fileURL . \'\">\' . htmlspecialchars($val->filename) . \'</a>\';\n                break;\n            case \'image\':\n                $arr[$key] .= \'<a href=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . \'/\' . $val->fileURL . \'\">\n                                 <img src=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . \'/\' . $val->tnURL. \'\" alt=\"\' . htmlspecialchars($val->filename) . \'\" title=\"\' . htmlspecialchars($val->filename) . \'\" />\n                               </a>\';\n                break;\n            case \'htmlarea\':\n                $arr[$key] = \'<div>\' . $val . \'</div>\';\n                break;\n            case \'material\':\n                $arr[$key] = \'<a href=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . htmlspecialchars($val->url) . \'\">\n                                \' . htmlspecialchars($val->name) . \'\n                              </a>\';\n                break;\n            default:\n                if (!$field->multiple && ($field->datatype == \'checkbox\')) {\n                    $arr[$key] = $val ? _YES : _NO;\n                } else {\n                    $arr[$key] = nl2br(htmlspecialchars($val));\n                }\n                break;\n        }\n    }\n    return \'<div>\' . htmlspecialchars($field->name) . \': \' . implode(\', \', $arr) . \'</div>\';\n};\n?>\n<?php if ($SMS) {\n    echo date(DATETIMEFORMAT) . \' \' . sprintf(FEEDBACK_STANDARD_HEADER, $Item->parent->name, $Item->page->name, $Item->domain) . \"\\n\";\n    echo FEEDBACK_ID . \': \' . (int)$Item->id . \"\\n\";\n    foreach ($Item->fields as $field) {\n        echo $smsField($field);\n    }\n} else { ?>\n    <div>\n      <div>\n        <?php echo FEEDBACK_ID . \': \' . (int)$Item->id?>\n      </div>\n      <?php\n      foreach ($Item->fields as $field) {\n          echo $emailField($field);\n      }\n      ?>\n    </div>\n    <?php if ($Material && $Material->id) {\n        $url = \'http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\';\n        $url .= htmlspecialchars(\n            $_SERVER[\'HTTP_HOST\'] .\n            \'/admin/?p=cms&sub=main&action=edit_material&id=\' .\n            (int)$Material->id .\n            \'&pid=\'\n        );\n        if (in_array(\n            $Item->page->id,\n            array_map(\n                function ($x) {\n                    return $x->id;\n                },\n                (array)$Item->parent->Material_Type->affectedPages\n            )\n        )) {\n            $url .= $Item->page->id;\n        } else {\n            $url .= $Item->parent->Material_Type->affectedPages[0]->id;\n        }\n        ?>\n        <p>\n          <a href=\"<?php echo $url?>\">\n            <?php echo VIEW?>\n          </a>\n        </p>\n    <?php } elseif ($Item->parent->create_feedback) { ?>\n        <p><a href=\"http<?php echo ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\')?>://<?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'] . \'/admin/?p=cms&sub=feedback&action=view&id=\' . $Item->id)?>\"><?php echo VIEW?></a></p>\n    <?php } ?>\n    <p>\n      <small>\n        <?php echo IP_ADDRESS?>: <?php echo htmlspecialchars($Item->ip)?><br />\n        <?php echo USER_AGENT?>: <?php echo htmlspecialchars($Item->user_agent)?><br />\n        <?php echo PAGE?>:\n        <?php if ($Item->page->parents) { ?>\n            <?php foreach ($Item->page->parents as $row) { ?>\n                <a href=\"<?php echo htmlspecialchars($Item->domain . $row->url)?>\"><?php echo htmlspecialchars($row->name)?></a> /\n            <?php } ?>\n        <?php } ?>\n        <a href=\"<?php echo htmlspecialchars($Item->domain . $Item->page->url)?>\"><?php echo htmlspecialchars($Item->page->name)?></a>\n        <?php if ($Item->material->id) { ?>\n            / <a href=\"<?php echo htmlspecialchars($Item->domain . $Item->material->url)?>\"><?php echo htmlspecialchars($Item->material->name)?></a>\n        <?php } ?>\n        <br />\n        <?php echo FORM?>:\n        <?php if ($Item->parent->create_feedback) { ?>\n            <a href=\"<?php echo htmlspecialchars($Item->domain . \'/admin/?p=cms&sub=feedback&id=\' . $Item->parent->id)?>\"><?php echo htmlspecialchars($Item->parent->name)?></a>\n        <?php } else { ?>\n            <?php echo htmlspecialchars($Item->parent->name)?>\n        <?php } ?>\n      </small>\n    </p>\n<?php } ?>\n', 1),
(6, 1, '__raas_cache_interface', 'Стандартный интерфейс кэширования', '<?php\n/**\n * Стандартный интерфейс кэширования\n * @param Block $Block Текущий блок\n * @param Page $Page Текущая страница\n * @param mixed $OUT Данные, полученные от интерфейса блока\n */\nnamespace RAAS\\CMS;\n\n$interface = new CacheInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES,\n    $OUT\n);\nreturn $interface->process();\n', 1),
(7, 1, '__raas_watermark_interface', 'Стандартный интерфейс водяных знаков', '<?php\nnamespace RAAS\\CMS;\n\nuse SOME\\Graphics;\n\n$processImage = function($filename, $watermarkFilename, $ratio = 0.5, $quality = 90)\n{\n    list($sourceImgWidth, $sourceImgHeight, $sourceImgType) = @getimagesize($filename);\n    list($waterMarkImgWidth, $waterMarkImgHeight, $waterMarkImgType) = @getimagesize($watermarkFilename);\n    $sourceInputFunction = Graphics::image_type_to_input_function($sourceImgType);\n    $sourceOutputFunction = Graphics::image_type_to_output_function($sourceImgType);\n    $waterMarkInputFunction = Graphics::image_type_to_input_function($waterMarkImgType);\n    $sourceImg = $sourceInputFunction($filename);\n    $waterMarkImg = $waterMarkInputFunction($watermarkFilename);\n\n    $rate = $waterMarkImgWidth / $waterMarkImgHeight; // Коэфициент соотношения сторон\n    $newWidth  = $waterMarkImgWidth; // Ширина участка на исходном изображении, куда будет наложен вотермарк\n    $newHeight = $waterMarkImgHeight; // Высота участка на исходном изображении, куда будет наложен вотермарк\n    if (($sourceImgWidth * $ratio) < $waterMarkImgWidth) {\n        $newWidth = $sourceImgWidth * $ratio; // Ширина вотермарки\n        $newHeight = $newWidth / $rate; // Высота вотермарки\n    }\n    $xSource = ($sourceImgWidth - $newWidth) / 2; // Отступ по оси Х\n    $ySource = ($sourceImgHeight - $newHeight) / 2; // Отступ по оси Y\n\n    imagecopyresampled($sourceImg, $waterMarkImg, $xSource, $ySource, 0, 0, $newWidth, $newHeight, $waterMarkImgWidth, $waterMarkImgHeight);\n    if ($sourceOutputFunction == \'imagejpeg\') {\n        $sourceOutputFunction($sourceImg, $filename, $quality);\n    } else {\n        $sourceOutputFunction($sourceImg, $filename);\n    }\n    return true;\n};\n\n$watermarkImage = \'files/cms/common/image/watermark.png\';\nif (($t->datatype == \'image\') && is_file($watermarkImage)) {\n    $files = array();\n    if ($postProcess) {\n        if ($addedAttachments && is_array($addedAttachments)) {\n            foreach ($addedAttachments as $row) {\n                if ($row->image) {\n                    $files[] = $row->file;\n                }\n            }\n        }\n    } else {\n        $files = (array)$_FILES[$Field->name][\'tmp_name\'];\n        $files = array_filter($files, \'is_file\');\n        $files = array_values($files);\n    }\n    foreach ($files as $file) {\n        $processImage($file, $watermarkImage);\n    }\n}\n', 1),
(8, 0, 'dummy', '<Пустой>', '', 0),
(9, 2, 'banners', 'Баннеры', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nif ($Set) {\r\n    ?>\r\n    <div class=\"banners\">\r\n      <div id=\"banners<?php echo (int)$Block->id?>\" class=\"carousel slide banners__inner\" data-role=\"slider\" data-slider-carousel=\"bootstrap\" data-slider-autoscroll=\"true\">\r\n        <?php if (count($Set) > 1) { ?>\r\n            <ul class=\"carousel-indicators banners__nav\">\r\n              <?php for ($i = 0; $i < count($Set); $i++) { ?>\r\n                  <li data-target=\"#banners<?php echo (int)$Block->id?>\" data-slide-to=\"<?php echo (int)$i?>\" class=\"banners__nav-item <?php echo !$i ? \'active\' : \'\'?>\"></li>\r\n              <?php } ?>\r\n            </ul>\r\n        <?php } ?>\r\n        <div class=\"carousel-inner banners__list banners-list\">\r\n          <?php for ($i = 0; $i < count($Set); $i++) { $row = $Set[$i]; ?>\r\n              <div class=\"item <?php echo !$i ? \'active\' : \'\'?> banners-list__item\">\r\n                <div class=\"banners-item\">\r\n                  <a class=\"banners-item__image\" <?php echo $row->url ? \'href=\"\' . htmlspecialchars($row->url) . \'\"\' : \'\'?>>\r\n                    <img src=\"/<?php echo Package::tn($row->image->fileURL, 1920, 654)?>\" alt=\"<?php echo htmlspecialchars($row->image->name ?: $row->name)?>\" />\r\n                  </a>\r\n                  <?php if ($row->name[0] != \'.\') { ?>\r\n                      <div class=\"banners-item__text\">\r\n                        <div class=\"banners-item__title\">\r\n                          <?php echo htmlspecialchars($row->name)?>\r\n                        </div>\r\n                        <div class=\"banners-item__description\">\r\n                          <?php echo $row->description?>\r\n                        </div>\r\n                      </div>\r\n                  <?php } ?>\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n        </div>\r\n        <?php if (count($Set) > 1) { ?>\r\n            <a href=\"#banners<?php echo (int)$Block->id?>\" data-slide=\"prev\" class=\"left carousel-control banners__arrow banners__arrow_left\"></a>\r\n            <a href=\"#banners<?php echo (int)$Block->id?>\" data-slide=\"next\" class=\"right carousel-control banners__arrow banners__arrow_right\"></a>\r\n        <?php } ?>\r\n      </div>\r\n    </div>\r\n<?php } ?>\r\n', 0),
(10, 2, 'feedback', 'Обратная связь', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nif ($_POST[\'AJAX\'] && ($Item instanceof Feedback)) {\r\n    $result = array();\r\n    if ($success[(int)$Block->id]) {\r\n        $result[\'success\'] = 1;\r\n    }\r\n    if ($localError) {\r\n        $result[\'localError\'] = $localError;\r\n    }\r\n    ob_clean();\r\n    echo json_encode($result);\r\n    exit;\r\n} else { ?>\r\n    <div class=\"feedback\">\r\n      <form class=\"form-horizontal\" data-role=\"raas-ajaxform\" action=\"#feedback\" method=\"post\" enctype=\"multipart/form-data\">\r\n        <?php include Package::i()->resourcesDir . \'/form2.inc.php\'?>\r\n        <div data-role=\"notifications\" <?php echo ($success[(int)$Block->id] || $localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n          <div class=\"alert alert-success\" <?php echo ($success[(int)$Block->id]) ? \'\' : \'style=\"display: none\"\'?>><?php echo FEEDBACK_SUCCESSFULLY_SENT?></div>\r\n          <div class=\"alert alert-danger\" <?php echo ($localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n            <ul>\r\n              <?php foreach ((array)$localError as $key => $val) { ?>\r\n                  <li><?php echo htmlspecialchars($val)?></li>\r\n              <?php } ?>\r\n            </ul>\r\n          </div>\r\n        </div>\r\n\r\n        <div data-role=\"feedback-form\" <?php echo $success[(int)$Block->id] ? \'style=\"display: none\"\' : \'\'?>>\r\n          <p><?php echo ASTERISK_MARKED_FIELDS_ARE_REQUIRED?></p>\r\n          <?php if ($Form->signature) { ?>\r\n                <input type=\"hidden\" name=\"form_signature\" value=\"<?php echo md5(\'form\' . (int)$Form->id . (int)$Block->id)?>\" />\r\n          <?php } ?>\r\n          <?php if ($Form->antispam == \'hidden\' && $Form->antispam_field_name) { ?>\r\n                <input type=\"text\" autocomplete=\"off\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" value=\"<?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?>\" style=\"position: absolute; left: -9999px\" />\r\n          <?php } ?>\r\n          <?php foreach ($Form->fields as $row) { ?>\r\n              <?php if ($row->urn == \'agree\') { ?>\r\n                  <div class=\"form-group\">\r\n                    <div class=\"col-sm-9 col-sm-offset-3 col-md-offset-2\">\r\n                      <label class=\"checkbox\">\r\n                        <?php $getField($row, $DATA);?>\r\n                        <a href=\"/privacy/\" target=\"_blank\"><?php echo htmlspecialchars($row->name)?></a>\r\n                      </label>\r\n                    </div>\r\n                  </div>\r\n              <?php } else { ?>\r\n                  <div class=\"form-group\">\r\n                    <label<?php echo !$row->multiple ? \' for=\"\' . htmlspecialchars($row->urn . $row->id . \'_\' . $Block->id) . \'\"\' : \'\'?> class=\"control-label col-sm-3 col-md-2\"><?php echo htmlspecialchars($row->name . ($row->required ? \'*\' : \'\'))?></label>\r\n                    <div class=\"col-sm-9 col-md-4\">\r\n                      <?php $getField($row, $DATA);?>\r\n                    </div>\r\n                  </div>\r\n              <?php } ?>\r\n          <?php } ?>\r\n          <?php if ($Form->antispam == \'captcha\' && $Form->antispam_field_name) { ?>\r\n              <div class=\"form-group\">\r\n                <label for=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" class=\"control-label col-sm-3 col-md-2\"><?php echo CAPTCHA?></label>\r\n                <div class=\"col-sm-9 col-md-4\">\r\n                  <img src=\"/assets/kcaptcha/?<?php echo session_name() . \'=\' . session_id()?>\" /><br />\r\n                  <input type=\"text\" autocomplete=\"off\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" />\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n          <div class=\"form-group\">\r\n            <div class=\"col-sm-9 col-md-4 col-sm-offset-3 col-md-offset-2\">\r\n              <button class=\"btn btn-primary\" type=\"submit\"><?php echo SEND?></button>\r\n            </div>\r\n          </div>\r\n        </div>\r\n      </form>\r\n    </div>\r\n<?php } ?>\r\n', 0),
(11, 2, 'feedback_modal', 'Обратная связь (всплывающее окно)', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nif ($_POST[\'AJAX\'] && ($Item instanceof Feedback)) {\r\n    $result = array();\r\n    if ($success[(int)$Block->id]) {\r\n        $result[\'success\'] = 1;\r\n    }\r\n    if ($localError) {\r\n        $result[\'localError\'] = $localError;\r\n    }\r\n    ob_clean();\r\n    echo json_encode($result);\r\n    exit;\r\n} else { ?>\r\n    <!--noindex-->\r\n    <div class=\"feedback-modal\">\r\n      <div id=\"<?php echo htmlspecialchars($Widget->urn)?>\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\" class=\"modal fade\">\r\n        <div class=\"modal-dialog\">\r\n          <div class=\"modal-content\">\r\n            <form data-role=\"raas-ajaxform\" action=\"#feedback\" method=\"post\" enctype=\"multipart/form-data\">\r\n              <div class=\"modal-header\">\r\n                <button type=\"button\" data-dismiss=\"modal\" aria-hidden=\"true\" class=\"close\">×</button>\r\n                <div class=\"h4 modal-title\"><?php echo htmlspecialchars($Block->name)?></div>\r\n              </div>\r\n              <div class=\"modal-body\">\r\n                <div class=\"form-horizontal\">\r\n                  <?php include Package::i()->resourcesDir . \'/form2.inc.php\'?>\r\n                  <div data-role=\"notifications\" <?php echo ($success[(int)$Block->id] || $localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n                    <div class=\"alert alert-success\" <?php echo ($success[(int)$Block->id]) ? \'\' : \'style=\"display: none\"\'?>><?php echo FEEDBACK_SUCCESSFULLY_SENT?></div>\r\n                    <div class=\"alert alert-danger\" <?php echo ($localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n                      <ul>\r\n                        <?php foreach ((array)$localError as $key => $val) { ?>\r\n                            <li><?php echo htmlspecialchars($val)?></li>\r\n                        <?php } ?>\r\n                      </ul>\r\n                    </div>\r\n                  </div>\r\n\r\n                  <div data-role=\"feedback-form\" <?php echo $success[(int)$Block->id] ? \'style=\"display: none\"\' : \'\'?>>\r\n                    <p><?php echo ASTERISK_MARKED_FIELDS_ARE_REQUIRED?></p>\r\n                    <?php if ($Form->signature) { ?>\r\n                          <input type=\"hidden\" name=\"form_signature\" value=\"<?php echo md5(\'form\' . (int)$Form->id . (int)$Block->id)?>\" />\r\n                    <?php } ?>\r\n                    <?php if ($Form->antispam == \'hidden\' && $Form->antispam_field_name) { ?>\r\n                          <input type=\"text\" autocomplete=\"off\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" value=\"<?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?>\" style=\"position: absolute; left: -9999px\" />\r\n                    <?php } ?>\r\n                    <?php foreach ($Form->fields as $row) { ?>\r\n                        <?php if ($row->urn == \'agree\') { ?>\r\n                            <div class=\"form-group\">\r\n                              <div class=\"col-sm-9 col-sm-offset-3\">\r\n                                <label class=\"checkbox\">\r\n                                  <?php $getField($row, $DATA);?>\r\n                                  <a href=\"/privacy/\" target=\"_blank\"><?php echo htmlspecialchars($row->name)?></a>\r\n                                </label>\r\n                              </div>\r\n                            </div>\r\n                        <?php } else { ?>\r\n                            <div class=\"form-group\">\r\n                              <label<?php echo !$row->multiple ? \' for=\"\' . htmlspecialchars($row->urn . $row->id . \'_\' . $Block->id) . \'\"\' : \'\'?> class=\"control-label col-sm-3\"><?php echo htmlspecialchars($row->name . ($row->required ? \'*\' : \'\'))?>:</label>\r\n                              <div class=\"col-sm-9\">\r\n                                <?php $getField($row, $DATA); ?>\r\n                              </div>\r\n                            </div>\r\n                        <?php } ?>\r\n                    <?php } ?>\r\n                    <?php if ($Form->antispam == \'captcha\' && $Form->antispam_field_name) { ?>\r\n                        <div class=\"form-group\">\r\n                          <label for=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" class=\"control-label col-sm-3\"><?php echo CAPTCHA?></label>\r\n                          <div class=\"col-sm-9\">\r\n                            <img src=\"/assets/kcaptcha/?<?php echo session_name() . \'=\' . session_id()?>\" /><br />\r\n                            <input type=\"text\" autocomplete=\"off\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" />\r\n                          </div>\r\n                        </div>\r\n                    <?php } ?>\r\n                    <div class=\"form-group text-right\">\r\n                      <div class=\"col-sm-12\">\r\n                        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\"><?php echo CANCEL?></button>\r\n                        <button class=\"btn btn-primary\" type=\"submit\"><?php echo SEND?></button>\r\n                      </div>\r\n                    </div>\r\n                  </div>\r\n                </div>\r\n              </div>\r\n            </form>\r\n          </div>\r\n        </div>\r\n      </div>\r\n    </div>\r\n    <!--/noindex-->\r\n<?php } ?>\r\n', 0),
(12, 2, 'head', '<head>', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse SOME\\HTTP;\r\n\r\n?>\r\n<title><?php echo htmlspecialchars($Page->meta_title ? $Page->meta_title : $Page->name)?></title>\r\n<?php if ($Page->meta_keywords) { ?>\r\n    <meta name=\"keywords\" content=\"<?php echo htmlspecialchars($Page->meta_keywords)?>\" />\r\n<?php } ?>\r\n<?php if ($Page->meta_description) { ?>\r\n    <meta name=\"description\" content=\"<?php echo htmlspecialchars($Page->meta_description)?>\" />\r\n<?php } ?>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n<meta name=\"viewport\" content=\"width=device-width\">\r\n<link rel=\'stylesheet\' href=\'/css/application.css\'>\r\n<link rel=\'stylesheet\' href=\'/css/animate.css\'>\r\n<link rel=\'stylesheet\' href=\'/css/style.css\'>\r\n<link rel=\'stylesheet\' href=\'/custom.css\'>\r\n<!-- Core-->\r\n<script src=\"/js/application.js\"></script>\r\n<script src=\"/js/wow.min.js\"></script>\r\n<script>new WOW().init();</script>\r\n<script src=\"/js/jquery.jcarousel.min.js\"></script>\r\n<script src=\"/js/sliders.js\"></script>\r\n<script src=\"/js/menu-top.js\"></script>\r\n<script src=\"/js/setrawcookie.js\"></script>\r\n<script src=\"/js/setcookie.js\"></script>\r\n<?php if (class_exists(\'RAAS\\CMS\\Shop\\Module\')) { ?>\r\n    <script src=\"/js/cookiecart.js\"></script>\r\n    <script src=\"/js/ajaxcart.js\"></script>\r\n    <script src=\"/js/ajaxcatalog.js\"></script>\r\n    <script src=\"/js/modal.js\"></script>\r\n    <script src=\"/js/catalog.js\"></script>\r\n<?php } ?>\r\n<script src=\"/js/script.js\"></script>\r\n<?php if (is_file(\'favicon.ico\')) { ?>\r\n    <link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"/favicon.ico\" />\r\n<?php } ?>\r\n<?php if (HTTP::queryString()) { ?>\r\n    <link rel=\"canonical\" href=\"http<?php echo ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\')?>://<?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'] . parse_url($_SERVER[\'REQUEST_URI\'], PHP_URL_PATH))?>\">\r\n<?php } ?>\r\n<?php if ($Page->noindex || $Page->Material->noindex) { ?>\r\n    <meta name=\"robots\" content=\"noindex,nofollow\" />\r\n<?php } ?>\r\n', 0),
(13, 2, 'order_call_modal', 'Заказать звонок (всплывающее окно)', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nif ($_POST[\'AJAX\'] && ($Item instanceof Feedback)) {\r\n    $result = array();\r\n    if ($success[(int)$Block->id]) {\r\n        $result[\'success\'] = 1;\r\n    }\r\n    if ($localError) {\r\n        $result[\'localError\'] = $localError;\r\n    }\r\n    ob_clean();\r\n    echo json_encode($result);\r\n    exit;\r\n} else { ?>\r\n    <!--noindex-->\r\n    <div class=\"order-call-modal\">\r\n      <div id=\"<?php echo htmlspecialchars($Widget->urn)?>\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\" class=\"modal fade\">\r\n        <div class=\"modal-dialog\">\r\n          <div class=\"modal-content\">\r\n            <form data-role=\"raas-ajaxform\" action=\"#feedback\" method=\"post\" enctype=\"multipart/form-data\">\r\n              <div class=\"modal-header\">\r\n                <button type=\"button\" data-dismiss=\"modal\" aria-hidden=\"true\" class=\"close\">×</button>\r\n                <div class=\"h4 modal-title\"><?php echo ORDER_CALL?></div>\r\n              </div>\r\n              <div class=\"modal-body\">\r\n                <div class=\"form-horizontal\">\r\n                  <?php include Package::i()->resourcesDir . \'/form.inc.php\'?>\r\n                  <div data-role=\"notifications\" <?php echo ($success[(int)$Block->id] || $localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n                    <div class=\"alert alert-success\" <?php echo ($success[(int)$Block->id]) ? \'\' : \'style=\"display: none\"\'?>><?php echo FEEDBACK_SUCCESSFULLY_SENT?></div>\r\n                    <div class=\"alert alert-danger\" <?php echo ($localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n                      <ul>\r\n                        <?php foreach ((array)$localError as $key => $val) { ?>\r\n                            <li><?php echo htmlspecialchars($val)?></li>\r\n                        <?php } ?>\r\n                      </ul>\r\n                    </div>\r\n                  </div>\r\n\r\n                  <div data-role=\"feedback-form\" <?php echo $success[(int)$Block->id] ? \'style=\"display: none\"\' : \'\'?>>\r\n                    <?php if ($Form->signature) { ?>\r\n                          <input type=\"hidden\" name=\"form_signature\" value=\"<?php echo md5(\'form\' . (int)$Form->id . (int)$Block->id)?>\" />\r\n                    <?php } ?>\r\n                    <?php if ($Form->antispam == \'hidden\' && $Form->antispam_field_name) { ?>\r\n                          <input type=\"text\" autocomplete=\"off\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" value=\"<?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?>\" style=\"position: absolute; left: -9999px\" />\r\n                    <?php } ?>\r\n                    <?php $row = $Form->fields[\'phone_call\']; $row->placeholder = $row->name; ?>\r\n                    <div class=\"form-group\">\r\n                      <div class=\"col-xs-12 order-call__phone\">\r\n                        <?php $getField($row, $DATA)?>\r\n                        <button class=\"btn btn-primary\" type=\"submit\"><span class=\"fa fa-phone\"></span></button>\r\n                      </div>\r\n                    </div>\r\n                    <?php $row = $Form->fields[\'agree\'] ?>\r\n                    <div class=\"form-group\">\r\n                      <div class=\"col-xs-12\">\r\n                        <label class=\"checkbox\">\r\n                          <?php $getField($row, $DATA);?>\r\n                          <a href=\"/privacy/\" target=\"_blank\"><?php echo htmlspecialchars($row->name)?></a>\r\n                        </label>\r\n                      </div>\r\n                    </div>\r\n                    <?php if ($Form->antispam == \'captcha\' && $Form->antispam_field_name) { ?>\r\n                        <div class=\"form-group\">\r\n                          <label for=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" class=\"control-label col-sm-3\"><?php echo CAPTCHA?></label>\r\n                          <div class=\"col-sm-9\">\r\n                            <img src=\"/assets/kcaptcha/?<?php echo session_name() . \'=\' . session_id()?>\" /><br />\r\n                            <input type=\"text\" autocomplete=\"off\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" />\r\n                          </div>\r\n                        </div>\r\n                    <?php } ?>\r\n                  </div>\r\n                </div>\r\n              </div>\r\n            </form>\r\n          </div>\r\n        </div>\r\n      </div>\r\n    </div>\r\n    <!--/noindex-->\r\n<?php } ?>\r\n', 0),
(14, 2, 'sitemap_xml', 'sitemap.xml', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\n$getChangeFreq = function($row) {\r\n    $text = \'\';\r\n    if ($row->changefreq) {\r\n        $text .= \'<changefreq>\' . htmlspecialchars($row->changefreq) . \'</changefreq>\';\r\n    } else {\r\n        $d0 = max(0, strtotime($row->post_date));\r\n        $s = ((time() - $d0) / $row->modify_counter);\r\n        $text .= \'<changefreq>\';\r\n        if ($s < 1800) {\r\n            $text .= \'always\';\r\n        } elseif ($s < 2 * 3600) {\r\n            $text .= \'hourly\';\r\n        } elseif ($s < 2 * 86400) {\r\n            $text .= \'daily\';\r\n        } elseif ($s < 2 * 7 * 86400) {\r\n            $text .= \'weekly\';\r\n        } elseif ($s < 2 * 30 * 86400) {\r\n            $text .= \'monthly\';\r\n        } elseif ($s < 2 * 365 * 86400) {\r\n            $text .= \'yearly\';\r\n        } else {\r\n            $text .= \'never\';\r\n        }\r\n        $text .= \'</changefreq>\';\r\n    }\r\n    return $text;\r\n};\r\n\r\n$showItem = function ($row) use (&$getChangeFreq) {\r\n    $text = \' <url>\r\n                <loc>http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . htmlspecialchars($_SERVER[\'HTTP_HOST\'] . $row->url) . \'</loc>\';\r\n    if (strtotime($row->last_modified) > 0) {\r\n        $text .= \'<lastmod>\' . date(DATE_W3C, strtotime($row->last_modified)) . \'</lastmod>\';\r\n    }\r\n    $text .= $getChangeFreq($row);\r\n    $text .= \'<priority>\' . str_replace(\',\', \'.\', (float)$row->sitemaps_priority) . \'</priority>\';\r\n    $text .= \'</url>\';\r\n    return $text;\r\n};\r\n\r\n$showMenu = function(Page $page) use (&$showMenu, &$getChangeFreq, &$showItem) {\r\n    $children = $page->visChildren;\r\n    for ($i = 0; $i < count($children); $i++) {\r\n        $row = $children[$i];\r\n        if (!$row->response_code) {\r\n            $text .= $showItem($row);\r\n            foreach ($row->affectedMaterials as $row2) {\r\n                if ($row2->parent->id == $row->id) {\r\n                    $text .= $showItem($row2);\r\n                }\r\n            }\r\n            $text .= $showMenu($row);\r\n        }\r\n    }\r\n    return $text;\r\n};\r\n\r\nheader(\'Content-Type: application/xml; charset=UTF-8\');\r\necho \'<?xml version=\"1.0\" encoding=\"UTF-8\"?\' . \'>\r\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n  \' . $showItem($Page->Domain) . \'\r\n  \' . $showMenu($Page->Domain) . \'\r\n</urlset>\';\r\n', 0),
(15, 2, 'logo', 'Логотип', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\n$text = $Block->description;\r\n$text = str_replace(\' href=\"\' . htmlspecialchars($Page->initialURL) . \'\"\', \'\', $text);\r\necho $text;\r\n', 0),
(16, 2, 'features_main', 'Наши преимущества на главной', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse \\SOME\\Text;\r\n\r\n$translateAddresses = true;\r\n\r\nif ($Set) {\r\n    ?>\r\n    <div class=\"features-main\">\r\n      <div class=\"features-main__title h2\"><?php echo htmlspecialchars($Block->name)?></div>\r\n      <div class=\"features-main__list\">\r\n        <div class=\"features-main-list\">\r\n          <?php foreach ($Set as $row) { ?>\r\n              <div class=\"features-main-list__item\">\r\n                <div class=\"features-main-item\">\r\n                  <?php if ($row->image->id || $row->icon) { ?>\r\n                      <div class=\"features-main-item__image\">\r\n                        <?php if ($row->image->id) { ?>\r\n                            <img src=\"/<?php echo htmlspecialchars($row->image->fileURL)?>\" alt=\"<?php echo htmlspecialchars($row->image->name ?: $row->name)?>\" />\r\n                        <?php } elseif ($row->icon) { ?>\r\n                            <span class=\"fa fa-<?php echo htmlspecialchars($row->icon)?>\"></span>\r\n                        <?php } ?>\r\n                      </div>\r\n                  <?php } ?>\r\n                  <div class=\"features-main-item__text\">\r\n                    <div class=\"features-main-item__title\">\r\n                      <?php echo htmlspecialchars($row->name)?>\r\n                    </div>\r\n                    <div class=\"features-main-item__description\">\r\n                      <?php echo $row->description?>\r\n                    </div>\r\n                  </div>\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n        </div>\r\n      </div>\r\n    </div>\r\n<?php } ?>\r\n', 0),
(17, 2, 'robots', 'robots.txt', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nheader(\'Content-Type: text/plain; charset=UTF-8\');\r\necho $Block->description;\r\n', 0),
(18, 2, 'custom_css', 'custom.css', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nheader(\'Content-Type: text/css; charset=UTF-8\');\r\necho $Block->description;\r\n', 0),
(19, 2, 'menu_content', 'Карта сайта', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse SOME\\HTTP;\r\n\r\n$showMenu = function($node, Page $current) use (&$showMenu) {\r\n    static $level = 0;\r\n    if ($node instanceof Menu) {\r\n        $children = $node->visSubMenu;\r\n    } else {\r\n        $children = (isset($node[\'children\']) && is_array($node[\'children\'])) ? $node[\'children\'] : array();\r\n    }\r\n    for ($i = 0; $i < count($children); $i++) {\r\n        $row = $children[$i];\r\n        $level++;\r\n        $ch = $showMenu($row, $current);\r\n        $level--;\r\n        if ($node instanceof Menu) {\r\n            $url = $row->url;\r\n            $name = $row->name;\r\n        } else {\r\n            $url = $row[\'url\'];\r\n            $name = $row[\'name\'];\r\n        }\r\n        $active = ($url == HTTP::queryString(\'\', true));\r\n        $semiactive = preg_match(\'/^\' . preg_quote($url, \'/\') . \'/umi\', HTTP::queryString(\'\', true)) && ($url != \'/\') && !$active;\r\n        if (preg_match(\'/class=\"[\\\\w\\\\- ]*?active[\\\\w\\\\- ]*?\"/umi\', $ch)) {\r\n            $semiactive = true;\r\n        }\r\n        $liClasses = array(\r\n            \'menu-content__item\',\r\n            \'menu-content__item_\' . (!$level ? \'main\' : \'inner\'),\r\n            \'menu-content__item_level_\' . $level\r\n        );\r\n        $aClasses = array(\r\n            \'menu-content__link\',\r\n            \'menu-content__link_\' . (!$level ? \'main\' : \'inner\'),\r\n            \'menu-content__link_level_\' . $level\r\n        );\r\n        if ($active) {\r\n            $liClasses[] = \'menu-content__item_active\';\r\n            $aClasses[] = \'menu-content__link_active\';\r\n        } elseif ($semiactive) {\r\n            $liClasses[] = \'menu-content__item_semiactive\';\r\n            $aClasses[] = \'menu-content__link_semiactive\';\r\n        }\r\n        $text .= \'<li class=\"\' . implode(\' \', $liClasses) . \'\">\'\r\n              .  \'  <a class=\"\' . implode(\' \', $aClasses) . \'\" \' . ($active ? \'\' : \' href=\"\' . htmlspecialchars($url) . \'\"\') . \'>\' . htmlspecialchars($name) . \'</a>\'\r\n              .     $ch\r\n              .  \'</li>\';\r\n    }\r\n    $ulClasses = array(\r\n        \'menu-content__list\',\r\n        \'menu-content__list_\' . (!$level ? \'main\' : \'inner\'),\r\n        \'menu-content__list_level_\' . $level\r\n    );\r\n    return $text ? \'<ul class=\"\' . implode(\' \', $ulClasses) . \'\">\' . $text . \'</ul>\' : $text;\r\n};\r\n\r\necho \'<nav class=\"menu-content\">\' . $showMenu($menuArr ?: $Item, $Page) . \'</nav>\';\r\n', 0),
(20, 2, 'menu_top', 'Верхнее меню', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse SOME\\HTTP;\r\n\r\n$showMenu = function($node, Page $current) use (&$showMenu) {\r\n    static $level = 0;\r\n    if ($node instanceof Menu) {\r\n        $children = $node->visSubMenu;\r\n    } else {\r\n        $children = (isset($node[\'children\']) && is_array($node[\'children\'])) ? $node[\'children\'] : array();\r\n    }\r\n    for ($i = 0; $i < count($children); $i++) {\r\n        $row = $children[$i];\r\n        $level++;\r\n        $ch = $showMenu($row, $current);\r\n        $level--;\r\n        if ($node instanceof Menu) {\r\n            $url = $row->url;\r\n            $name = $row->name;\r\n        } else {\r\n            $url = $row[\'url\'];\r\n            $name = $row[\'name\'];\r\n        }\r\n        $active = ($url == HTTP::queryString(\'\', true));\r\n        $semiactive = preg_match(\'/^\' . preg_quote($url, \'/\') . \'/umi\', HTTP::queryString(\'\', true)) && ($url != \'/\') && !$active;\r\n        if (preg_match(\'/class=\"[\\\\w\\\\- ]*?active[\\\\w\\\\- ]*?\"/umi\', $ch)) {\r\n            $semiactive = true;\r\n        }\r\n        $liClasses = array(\r\n            \'menu-top__item\',\r\n            \'menu-top__item_\' . (!$level ? \'main\' : \'inner\'),\r\n            \'menu-top__item_level_\' . $level\r\n        );\r\n        $aClasses = array(\r\n            \'menu-top__link\',\r\n            \'menu-top__link_\' . (!$level ? \'main\' : \'inner\'),\r\n            \'menu-top__link_level_\' . $level\r\n        );\r\n        if ($active) {\r\n            $liClasses[] = \'menu-top__item_active\';\r\n            $aClasses[] = \'menu-top__link_active\';\r\n        } elseif ($semiactive) {\r\n            $liClasses[] = \'menu-top__item_semiactive\';\r\n            $aClasses[] = \'menu-top__link_semiactive\';\r\n        }\r\n        $text .= \'<li class=\"\' . implode(\' \', $liClasses) . \'\">\'\r\n              .  \'  <a class=\"\' . implode(\' \', $aClasses) . \'\" \' . ($active ? \'\' : \' href=\"\' . htmlspecialchars($url) . \'\"\') . \'>\' . htmlspecialchars($name) . \'</a>\'\r\n              .     $ch\r\n              .  \'</li>\';\r\n    }\r\n    $ulClasses = array(\r\n        \'menu-top__list\',\r\n        \'menu-top__list_\' . (!$level ? \'main\' : \'inner\'),\r\n        \'menu-top__list_level_\' . $level\r\n    );\r\n    return $text ? \'<ul class=\"\' . implode(\' \', $ulClasses) . \'\">\' . $text . \'</ul>\' : $text;\r\n};\r\n\r\necho \'<nav class=\"menu-top\">\' . $showMenu($menuArr ?: $Item, $Page) . \'</nav>\';\r\n', 0),
(21, 2, 'menu_bottom', 'Нижнее меню', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse SOME\\HTTP;\r\n\r\n$showMenu = function($node, Page $current) use (&$showMenu) {\r\n    static $level = 0;\r\n    if ($node instanceof Menu) {\r\n        $children = $node->visSubMenu;\r\n    } else {\r\n        $children = (isset($node[\'children\']) && is_array($node[\'children\'])) ? $node[\'children\'] : array();\r\n    }\r\n    for ($i = 0; $i < count($children); $i++) {\r\n        $row = $children[$i];\r\n        $level++;\r\n        $ch = $showMenu($row, $current);\r\n        $level--;\r\n        if ($node instanceof Menu) {\r\n            $url = $row->url;\r\n            $name = $row->name;\r\n        } else {\r\n            $url = $row[\'url\'];\r\n            $name = $row[\'name\'];\r\n        }\r\n        $active = ($url == HTTP::queryString(\'\', true));\r\n        $semiactive = preg_match(\'/^\' . preg_quote($url, \'/\') . \'/umi\', HTTP::queryString(\'\', true)) && ($url != \'/\') && !$active;\r\n        if (preg_match(\'/class=\"[\\\\w\\\\- ]*?active[\\\\w\\\\- ]*?\"/umi\', $ch)) {\r\n            $semiactive = true;\r\n        }\r\n        $liClasses = array(\r\n            \'menu-bottom__item\',\r\n            \'menu-bottom__item_\' . (!$level ? \'main\' : \'inner\'),\r\n            \'menu-bottom__item_level_\' . $level\r\n        );\r\n        $aClasses = array(\r\n            \'menu-bottom__link\',\r\n            \'menu-bottom__link_\' . (!$level ? \'main\' : \'inner\'),\r\n            \'menu-bottom__link_level_\' . $level\r\n        );\r\n        if ($active) {\r\n            $liClasses[] = \'menu-bottom__item_active\';\r\n            $aClasses[] = \'menu-bottom__link_active\';\r\n        } elseif ($semiactive) {\r\n            $liClasses[] = \'menu-bottom__item_semiactive\';\r\n            $aClasses[] = \'menu-bottom__link_semiactive\';\r\n        }\r\n        $text .= \'<li class=\"\' . implode(\' \', $liClasses) . \'\">\'\r\n              .  \'  <a class=\"\' . implode(\' \', $aClasses) . \'\" \' . ($active ? \'\' : \' href=\"\' . htmlspecialchars($url) . \'\"\') . \'>\' . htmlspecialchars($name) . \'</a>\'\r\n              .     $ch\r\n              .  \'</li>\';\r\n    }\r\n    $ulClasses = array(\r\n        \'menu-bottom__list\',\r\n        \'menu-bottom__list_\' . (!$level ? \'main\' : \'inner\'),\r\n        \'menu-bottom__list_level_\' . $level\r\n    );\r\n    return $text ? \'<ul class=\"\' . implode(\' \', $ulClasses) . \'\">\' . $text . \'</ul>\' : $text;\r\n};\r\n\r\necho \'<nav class=\"menu-bottom\">\' . $showMenu($menuArr ?: $Item, $Page) . \'</nav>\';\r\n', 0),
(22, 2, 'news', 'Новости', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse \\SOME\\Text;\r\nuse \\SOME\\HTTP;\r\n\r\nif ($Item) {\r\n    ?>\r\n    <div class=\"news\">\r\n      <div class=\"news-article\">\r\n        <?php if (($t = strtotime($Item->date)) > 0) { ?>\r\n            <div class=\"news-article__date\"><?php echo date(\'d\', $t) . \' \' . Text::$months[(int)date(\'m\', $t)] . \' \' . date(\'Y\', $t)?></div>\r\n        <?php } ?>\r\n        <?php if ($Item->visImages) { ?>\r\n            <div class=\"news-article__image\">\r\n              <a href=\"/<?php echo $Item->visImages[0]->fileURL?>\" data-lightbox-gallery=\"g\">\r\n                <img src=\"/<?php echo $Item->visImages[0]->tnURL?>\" alt=\"<?php echo htmlspecialchars($Item->visImages[0]->name ?: $row->name)?>\" /></a>\r\n            </div>\r\n        <?php } ?>\r\n        <div class=\"news-article__text\">\r\n          <div class=\"news-article__description\">\r\n            <?php echo $Item->description?>\r\n          </div>\r\n        </div>\r\n        <?php if (count($Item->visImages) > 1) { ?>\r\n            <div class=\"news-article__images\">\r\n              <div class=\"h2 news-article__images-title\">\r\n                Фотографии\r\n              </div>\r\n              <div class=\"news-article__images-inner\">\r\n                <?php for ($i = 1; $i < count($Item->visImages); $i++) { $row = $Item->visImages[$i]; ?>\r\n                    <div class=\"news-article__additional-image-container\">\r\n                      <a href=\"/<?php echo htmlspecialchars($row->fileURL)?>\" class=\"news-article__additional-image\" data-lightbox-gallery=\"g\">\r\n                        <img src=\"/<?php echo htmlspecialchars($row->tnURL)?>\" alt=\"<?php echo htmlspecialchars($row->name)?>\" /></a>\r\n                    </div>\r\n                <?php } ?>\r\n              </div>\r\n            </div>\r\n        <?php } ?>\r\n      </div>\r\n    </div>\r\n<?php } elseif ($Set) { ?>\r\n    <div class=\"news\">\r\n      <div class=\"news__list\">\r\n        <div class=\"news-list\">\r\n          <?php foreach ($Set as $row) { ?>\r\n              <div class=\"news-list__item\">\r\n                <div class=\"news-item\">\r\n                  <div class=\"news-item__image\">\r\n                    <a<?php echo ($Block->nat ? \' href=\"\' . htmlspecialchars($row->url) . \'\"\' : \'\') . (!$row->visImages ? \' class=\"no-image\"\' : \'\')?>>\r\n                      <?php if ($row->visImages) { ?>\r\n                          <img src=\"/<?php echo htmlspecialchars($row->visImages[0]->tnURL)?>\" alt=\"<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>\" />\r\n                      <?php } ?>\r\n                    </a>\r\n                  </div>\r\n                  <div class=\"news-item__text\">\r\n                    <div class=\"news-item__title\">\r\n                      <a<?php echo $Block->nat ? \' href=\"\' . htmlspecialchars($row->url) . \'\"\' : \'\'?>>\r\n                        <?php echo htmlspecialchars($row->name)?>\r\n                      </a>\r\n                    </div>\r\n                    <?php if (($t = strtotime($row->date)) > 0) { ?>\r\n                        <div class=\"news-item__date\">\r\n                          <?php echo date(\'d\', $t) . \' \' . Text::$months[(int)date(\'m\', $t)] . \' \' . date(\'Y\', $t)?>\r\n                        </div>\r\n                    <?php } ?>\r\n                    <div class=\"news-item__description\">\r\n                      <?php echo htmlspecialchars($row->brief ?: Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, \'UTF-8\'), 256, \'...\'))?>\r\n                    </div>\r\n                    <?php if ($Block->nat) { ?>\r\n                        <div class=\"news-item__more\">\r\n                          <a href=\"<?php echo htmlspecialchars($row->url)?>\">\r\n                            <?php echo SHOW_MORE?>\r\n                          </a>\r\n                        </div>\r\n                    <?php } ?>\r\n                  </div>\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n        </div>\r\n      </div>\r\n    </div>\r\n    <?php include Package::i()->resourcesDir . \'/pages.inc.php\'?>\r\n    <?php if ($Pages->pages > 1) { ?>\r\n        <ul class=\"pagination pull-right\">\r\n          <?php\r\n          echo $outputNav(\r\n              $Pages,\r\n              array(\r\n                  \'pattern\' => \'<li><a href=\"\' . HTTP::queryString(\'page={link}\') . \'\">{text}</a></li>\',\r\n                  \'pattern_active\' => \'<li class=\"active\"><a>{text}</a></li>\',\r\n                  \'ellipse\' => \'<li class=\"disabled\"><a>...</a></li>\'\r\n              )\r\n          );\r\n          ?>\r\n        </ul>\r\n    <?php } ?>\r\n<?php } ?>\r\n', 0);
INSERT INTO `cms_snippets` (`id`, `pid`, `urn`, `name`, `description`, `locked`) VALUES
(23, 2, 'news_main', 'Новости на главной', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse \\SOME\\Text;\r\n\r\n$translateAddresses = true;\r\n\r\nif ($Set) {\r\n    ?>\r\n    <div class=\"news-main left-block\">\r\n      <div class=\"news-main__title left-block__title\"><a href=\"/news/\">Новости</a></div>\r\n      <div class=\"news-main__list left-block__inner\">\r\n        <div class=\"news-main-list\">\r\n          <?php foreach ($Set as $row) { ?>\r\n              <div class=\"news-main-list__item\">\r\n                <div class=\"news-main-item\">\r\n                  <div class=\"news-main-item__image\">\r\n                    <a<?php echo ($translateAddresses ? \' href=\"\' . htmlspecialchars($row->url) . \'\"\' : \'\') . (!$row->visImages ? \' class=\"no-image\"\' : \'\')?>>\r\n                      <?php if ($row->visImages) { ?>\r\n                          <img src=\"/<?php echo Package::tn($row->visImages[0]->fileURL, 1920, 654)?>\" alt=\"<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>\" />\r\n                      <?php } ?>\r\n                    </a>\r\n                  </div>\r\n                  <div class=\"news-main-item__text\">\r\n                    <div class=\"news-main-item__title\">\r\n                      <a<?php echo $translateAddresses ? \' href=\"\' . htmlspecialchars($row->url) . \'\"\' : \'\'?>>\r\n                        <?php echo htmlspecialchars($row->name)?>\r\n                      </a>\r\n                    </div>\r\n                    <?php if (($t = strtotime($row->date)) > 0) { ?>\r\n                        <div class=\"news-main-item__date\">\r\n                          <?php echo date(\'d\', $t) . \' \' . Text::$months[(int)date(\'m\', $t)] . \' \' . date(\'Y\', $t)?>\r\n                        </div>\r\n                    <?php } ?>\r\n                    <div class=\"news-main-item__description\">\r\n                      <?php echo htmlspecialchars($row->brief ?: Text::cuttext(html_entity_decode(strip_tags($row->description), ENT_COMPAT | ENT_HTML5, \'UTF-8\'), 256, \'...\'))?>\r\n                    </div>\r\n                    <?php if ($translateAddresses) { ?>\r\n                        <div class=\"news-main-item__more\">\r\n                          <a href=\"<?php echo htmlspecialchars($row->url)?>\">\r\n                            <?php echo SHOW_MORE?>\r\n                          </a>\r\n                        </div>\r\n                    <?php } ?>\r\n                  </div>\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n        </div>\r\n      </div>\r\n    </div>\r\n<?php } ?>\r\n', 0),
(24, 1, '__raas_shop_cart_interface', 'Стандартный интерфейс корзины', '<?php\n/**\n * Стандартный интерфейс корзины\n * @param Block_Cart $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS\\Shop;\n\n$interface = new CartInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(25, 1, '__raas_shop_order_notify', 'Стандартное уведомление о заказе', '<?php\n/**\n * Стандартное уведомление о заказе\n * @param bool $SMS Уведомление отправляется по SMS\n * @param Order $Item Уведомление формы обратной связи\n * @param bool $forUser Отправка для пользователя\n */\nnamespace RAAS\\CMS\\Shop;\n\nuse RAAS\\CMS\\Form_Field;\n\n/**\n * Получает представление поля для отправки по SMS\n * @param Form_Field $field Поле для получения представления\n * @return string\n */\n$smsField = function (Form_Field $field) {\n    $values = $field->getValues(true);\n    $arr = array();\n    foreach ($values as $key => $val) {\n        $val = $field->doRich($val);\n        switch ($field->datatype) {\n            case \'date\':\n                $arr[$key] = date(DATEFORMAT, strtotime($val));\n                break;\n            case \'datetime-local\':\n                $arr[$key] = date(DATETIMEFORMAT, strtotime($val));\n                break;\n            case \'file\':\n            case \'image\':\n                $arr[$key] .= $val->filename;\n                break;\n            case \'htmlarea\':\n                $arr[$key] = strip_tags($val);\n                break;\n            case \'material\':\n                $arr[$key] = $val->name;\n                break;\n            default:\n                if (!$field->multiple && ($field->datatype == \'checkbox\')) {\n                    $arr[$key] = $val ? _YES : _NO;\n                } else {\n                    $arr[$key] = $val;\n                }\n                break;\n        }\n    }\n    return $field->name . \': \' . implode(\', \', $arr) . \"\\n\";\n};\n\n/**\n * Получает представление поля для отправки по электронной почте\n * @param Form_Field $field Поле для получения представления\n * @return string\n */\n$emailField = function (Form_Field $field) {\n    $values = $field->getValues(true);\n    $arr = array();\n    foreach ($values as $key => $val) {\n        $val = $field->doRich($val);\n        switch ($field->datatype) {\n            case \'date\':\n                $arr[$key] = date(DATEFORMAT, strtotime($val));\n                break;\n            case \'datetime-local\':\n                $arr[$key] = date(DATETIMEFORMAT, strtotime($val));\n                break;\n            case \'color\':\n                $arr[$key] = \'<span style=\"display: inline-block; height: 16px; width: 16px; background-color: \' . htmlspecialchars($val) . \'\"></span>\';\n                break;\n            case \'email\':\n                $arr[$key] .= \'<a href=\"mailto:\' . htmlspecialchars($val) . \'\">\' . htmlspecialchars($val) . \'</a>\';\n                break;\n            case \'url\':\n                $arr[$key] .= \'<a href=\"\' . (!preg_match(\'/^http(s)?:\\\\/\\\\//umi\', trim($val)) ? \'http://\' : \'\') . htmlspecialchars($val) . \'\">\' . htmlspecialchars($val) . \'</a>\';\n                break;\n            case \'file\':\n                $arr[$key] .= \'<a href=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . \'/\' . $val->fileURL . \'\">\' . htmlspecialchars($val->filename) . \'</a>\';\n                break;\n            case \'image\':\n                $arr[$key] .= \'<a href=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . \'/\' . $val->fileURL . \'\">\n                                 <img src=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . \'/\' . $val->tnURL. \'\" alt=\"\' . htmlspecialchars($val->filename) . \'\" title=\"\' . htmlspecialchars($val->filename) . \'\" />\n                               </a>\';\n                break;\n            case \'htmlarea\':\n                $arr[$key] = \'<div>\' . $val . \'</div>\';\n                break;\n            case \'material\':\n                $arr[$key] = \'<a href=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . htmlspecialchars($val->url) . \'\">\n                                \' . htmlspecialchars($val->name) . \'\n                              </a>\';\n                break;\n            default:\n                if (!$field->multiple && ($field->datatype == \'checkbox\')) {\n                    $arr[$key] = $val ? _YES : _NO;\n                } else {\n                    $arr[$key] = nl2br(htmlspecialchars($val));\n                }\n                break;\n        }\n    }\n    return \'<div>\' . htmlspecialchars($field->name) . \': \' . implode(\', \', $arr) . \'</div>\';\n};\n?>\n<?php if ($SMS) {\n    if ($forUser) {\n        echo sprintf(ORDER_SUCCESSFULLY_SENT, $Item->id);\n    } else {\n        echo date(DATETIMEFORMAT) . \' \' . sprintf(ORDER_STANDARD_HEADER_USER, $Item->id, $_SERVER[\'HTTP_HOST\']) . \"\\n\";\n        foreach ($Item->fields as $field) {\n            echo $smsField($field);\n        }\n    }\n} else { ?>\n    <div>\n      <?php\n      foreach ($Item->fields as $field) {\n          echo $emailField($field);\n      }\n      ?>\n    </div>\n    <?php if ($Item->items) { ?>\n      <br />\n      <table style=\"width: 100%\" border=\"1\">\n        <thead>\n          <tr>\n            <th><?php echo NAME?></th>\n            <th><?php echo ADDITIONAL_INFO?></th>\n            <th><?php echo PRICE?></th>\n            <th><?php echo AMOUNT?></th>\n            <th><?php echo SUM?></th>\n          </tr>\n        </thead>\n        <tbody>\n          <?php\n          $sum = 0;\n          foreach ($Item->items as $row) {\n              $url = ($forUser ? $row->url : \'/admin/?p=cms&sub=main&action=edit_material&id=\' . $row->id . \'&pid=\' . ($row->material_type->affectedPages[0]->id)); ?>\n              <tr>\n                <td>\n                  <?php if ($url) { ?>\n                      <a href=\"http<?php echo ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\')?>://<?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'] . $url)?>\">\n                        <?php echo htmlspecialchars($row->name)?>\n                      </a>\n                  <?php } else { ?>\n                      <?php echo htmlspecialchars($row->name)?>\n                  <?php } ?>\n                </td>\n                <td><?php echo htmlspecialchars($row->__get(\'meta\'))?>&nbsp;</td>\n                <td style=\"text-align: right; white-space: nowrap;\"><?php echo number_format($row->realprice, 2, \'.\', \' \')?></td>\n                <td><?php echo (int)$row->amount?></td>\n                <td style=\"text-align: right; white-space: nowrap;\"><?php echo number_format($row->amount * $row->realprice, 2, \'.\', \' \')?></td>\n              </tr>\n          <?php $sum += $row->amount * $row->realprice; } ?>\n          <tr>\n            <th colspan=\"4\" style=\"text-align: right\"><?php echo TOTAL_SUM?>:</th>\n            <th style=\"text-align: right; white-space: nowrap;\"><?php echo number_format($sum, 2, \'.\', \' \')?></th>\n          </tr>\n        </tbody>\n      </table>\n    <?php } ?>\n    <?php if (!$forUser) { ?>\n        <p><a href=\"http<?php echo ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\')?>://<?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'] . \'/admin/?p=cms&m=shop&sub=orders&action=view&id=\' . $Item->id)?>\"><?php echo VIEW?></a></p>\n        <p>\n          <small>\n            <?php echo IP_ADDRESS?>: <?php echo htmlspecialchars($Item->ip)?><br />\n            <?php echo USER_AGENT?>: <?php echo htmlspecialchars($Item->user_agent)?><br />\n            <?php echo PAGE?>:\n            <?php if ($Item->page->parents) { ?>\n                <?php foreach ($Item->page->parents as $row) { ?>\n                    <a href=\"<?php echo htmlspecialchars($Item->domain . $row->url)?>\"><?php echo htmlspecialchars($row->name)?></a> /\n                <?php } ?>\n            <?php } ?>\n            <a href=\"<?php echo htmlspecialchars($Item->domain . $Item->page->url)?>\"><?php echo htmlspecialchars($Item->page->name)?></a>\n            <br />\n            <?php echo CART_TYPE?>:\n            <a href=\"<?php echo htmlspecialchars($Item->domain . \'/admin/?p=cms&m=shop&sub=orders&id=\' . $Item->parent->id)?>\"><?php echo htmlspecialchars($Item->parent->name)?></a>\n          </small>\n        </p>\n    <?php } ?>\n<?php } ?>\n', 1),
(26, 1, '__raas_shop_imageloader_interface', 'Стандартный интерфейс загрузчика изображений', '<?php\nnamespace RAAS\\CMS\\Shop;\n\nuse \\RAAS\\CMS\\Material;\nuse \\RAAS\\CMS\\Package;\nuse \\RAAS\\CMS\\Sub_Main as Package_Sub_Main;\nuse \\RAAS\\Application;\nuse \\RAAS\\Attachment;\n\nini_set(\'max_execution_time\', 300);\n$st = microtime(true);\nif ($_SERVER[\'REQUEST_METHOD\'] == \'POST\') {\n    // Загрузка картинок\n    if (!$files) {\n        return array(\'localError\' => array(array(\'name\' => \'MISSING\', \'value\' => \'files\', \'description\' => Module::i()->view->_(\'UPLOAD_FILES_REQUIRED\'))));\n    } else {\n        // Ищем задействованные типы\n        $mtypes = array_merge(array((int)$Loader->Material_Type->id), (array)$Loader->Material_Type->all_children_ids);\n        $mtypes = array_map(\'intval\', $mtypes);\n        $articles = array();\n        if ($Loader->Unique_Field->id) {\n            $SQL_query = \"SELECT tM.id, tD.value \";\n        } elseif ($Loader->ufid) {\n            $SQL_query = \"SELECT id, \" . $Loader->ufid . \" AS value \";\n        }\n        $SQL_query .= \" FROM \" . Material::_tablename() . \" AS tM \";\n        if ($Loader->Unique_Field->id) {\n            $SQL_query .= \" JOIN \" . Material::_dbprefix() . \"cms_data AS tD ON tD.pid = tM.id \";\n        }\n        $SQL_query .= \" WHERE tM.pid IN (\" . implode(\", \", $mtypes ?: array(0)) . \") \";\n        if ($Loader->Unique_Field->id) {\n            $SQL_query .= \" AND TRIM(tD.value) != \'\' AND tD.fid = \" . (int)$Loader->Unique_Field->id;\n        } else {\n            $SQL_query .= \" AND \" . $Loader->ufid . \" != \'\' \";\n        }\n        $SQL_result = Material::_SQL()->get($SQL_query);\n        foreach ($SQL_result as $row) {\n            if (trim($row[\'value\'])) {\n                $articles[$row[\'id\']] = \\SOME\\Text::beautify(trim($row[\'value\']));\n            }\n        }\n\n        // Подготовить реальные файлы к загрузке\n        $processFile = function ($file) use (&$processFile, $mtypes, $Loader, $articles) {\n            $ext = strtolower(pathinfo($file[\'name\'], PATHINFO_EXTENSION));\n            $proceedFiles = array();\n            switch ($ext) {\n                case \'jpg\':\n                case \'jpeg\':\n                case \'png\':\n                case \'gif\':\n                    if ($type = getimagesize($file[\'tmp_name\'])) {\n                        $file[\'type\'] = image_type_to_mime_type($type[2]);\n                        $temp = array();\n                        $filename = pathinfo($file[\'name\'], PATHINFO_FILENAME);\n                        foreach ($articles as $id => $article) {\n                            if (preg_match(\'/^\' . preg_quote($article) . \'($|\' . preg_quote($Loader->sep_string) . \')/i\', $filename)) {\n                                $temp[] = $id;\n                                break;\n                            }\n                        }\n                        if ($temp) {\n                            $file[\'materials\'] = $temp;\n                            $ext2 = image_type_to_extension($type[2]);\n                            $file[\'original_name\'] = $file[\'name\'];\n                            $file[\'name\'] = $filename . $ext2;\n                        }\n                        $proceedFiles[] = $file;\n                    }\n                    break;\n                case \'zip\':\n                    $files = array();\n                    $z = new \\SOME\\ZipArchive();\n                    if ($z->open($file[\'tmp_name\']) === true) {\n                        for ($i = 0; $i < $z->numFiles; $i++) {\n                            $tmpname = tempnam(sys_get_temp_dir(), \'\');\n                            file_put_contents($tmpname, $z->getFromIndex($i));\n                            $files[] = array(\'name\' => basename($z->getNameIndex($i)), \'tmp_name\' => $tmpname);\n                        }\n                    }\n                    $z->close();\n                    foreach ($files as $f) {\n                        $proceedFiles = array_merge($proceedFiles, $processFile($f));\n                    }\n                    break;\n            }\n            return $proceedFiles;\n        };\n\n        $proceedFiles = array();\n        $affectedMaterials = array();\n        foreach ($files as $file) {\n            $proceedFiles = array_merge($proceedFiles, $processFile($file));\n        }\n        if (!$proceedFiles) {\n            return array(\'localError\' => array(array(\'name\' => \'INVALID\', \'value\' => \'files\', \'description\' => Module::i()->view->_(\'ALLOWED_FORMATS_JPG_JPEG_PNG_GIF_ZIP\'))));\n        }\n        if ($clear) {\n            // Ищем материалы для очистки изображений\n            foreach ($proceedFiles as $file) {\n                if ($file[\'materials\']) {\n                    $affectedMaterials = array_merge($affectedMaterials, (array)$file[\'materials\']);\n                }\n            }\n            $affectedMaterials = array_unique($affectedMaterials);\n            $affectedMaterials = array_values($affectedMaterials);\n\n            // Ищем attachment\'ы для удаления\n            $attachmentsToClear = array();\n            $SQL_query = \"SELECT value FROM \" . Material::_dbprefix() . \"cms_data\n                           WHERE pid IN (\" . implode(\", \", $affectedMaterials ?: array(0)) . \") AND fid = \" . (int)$Loader->Image_Field->id;\n            $SQL_result = Material::_SQL()->getcol($SQL_query);\n            foreach ($SQL_result as $val) {\n                if (preg_match(\'/\"attachment\":(\\\\d+)/i\', $val, $regs)) {\n                    $attachmentsToClear[] = (int)$regs[1];\n                }\n            }\n            $SQL_query = \"SELECT realname FROM \" . Attachment::_tablename() . \" WHERE id IN (\" . implode(\", \", $attachmentsToClear ?: array(0)) . \")\";\n            $filesToClear = Material::_SQL()->getcol($SQL_query);\n\n            if (!$test) {\n                // Очищаем данные\n                $SQL_query = \"DELETE tD\n                                FROM \" . Material::_dbprefix() . \"cms_data AS tD\n                               WHERE tD.fid = \" . (int)$Loader->Image_Field->id . \" AND tD.pid IN (\" . implode(\", \", $affectedMaterials ?: array(0)) . \")\";\n                Material::_SQL()->query($SQL_query);\n\n                // Чистим файлы\n                foreach ($filesToClear as $val) {\n                    $val = realpath(Package::i()->filesDir) . \'/\' . str_replace(\'.\', \'*.\', $val);\n                    $arr = glob($val);\n                    foreach ($arr as $row) {\n                        unlink($row);\n                    }\n                }\n\n                // Чистим сами attachment\'ы\n                $SQL_query = \"DELETE FROM \" . Attachment::_tablename() . \" WHERE id IN (\" . implode(\", \", $attachmentsToClear ?: array(0)) . \")\";\n                Material::_SQL()->query($SQL_query);\n            } else {\n                foreach ($attachmentsToClear as $val) {\n                    $row = new Attachment($val);\n                    $log[] = array(\n                        \'time\' => (microtime(true) - $st),\n                        \'text\' => sprintf(Module::i()->view->_(\'LOG_DELETE_ATTACHMENTS\'), \'/\' . Package::i()->filesURL . \'/\' . $row->realname, $row->realname)\n                    );\n                }\n                foreach ($affectedMaterials as $val) {\n                    $row = new Material($val);\n                    $log[] = array(\n                        \'time\' => (microtime(true) - $st),\n                        \'text\' => sprintf(Module::i()->view->_(\'LOG_DELETE_MATERIAL_IMAGES\'), Package_Sub_Main::i()->url . \'&action=edit_material&id=\' . $row->id, $row->name)\n                    );\n                }\n            }\n            $log[] = array(\'time\' => (microtime(true) - $st), \'text\' => Module::i()->view->_(\'LOG_OLD_MATERIAL_IMAGES_CLEARED\'));\n        }\n        foreach ($proceedFiles as $file) {\n            if ($file[\'materials\']) {\n                $att = new Attachment();\n                $att->upload = $file[\'tmp_name\'];\n                $att->filename = $file[\'name\'];\n                $att->mime = $file[\'type\'];\n                $att->parent = $Loader->Image_Field;\n                $att->image = 1;\n                if ($temp = (int)Application::i()->context->registryGet(\'maxsize\')) {\n                    $att->maxWidth = $att->maxHeight = $temp;\n                }\n                if ($temp = (int)Application::i()->context->registryGet(\'tnsize\')) {\n                    $att->tnsize = $temp;\n                }\n                if (!$test) {\n                    $att->commit();\n                }\n                $row = array(\'vis\' => 1, \'name\' => \'\', \'description\' => \'\', \'attachment\' => (int)$att->id);\n                foreach ($file[\'materials\'] as $id) {\n                    $Item = new Material($id);\n                    if (!$test) {\n                        $Item->fields[$Loader->Image_Field->urn]->addValue(json_encode($row));\n                    }\n                    $log[] = array(\n                        \'time\' => (microtime(true) - $st),\n                        \'text\' => sprintf(\n                            Module::i()->view->_(\'LOG_ADD_MATERIAL_IMAGE\'),\n                            \'/\' . Package::i()->filesURL . \'/\' . $att->realname,\n                            $att->filename,\n                            $file[\'original_name\'],\n                            Package_Sub_Main::i()->url . \'&action=edit_material&id=\' . $Item->id,\n                            $Item->name\n                        )\n                    );\n                }\n            }\n        }\n    }\n    return array(\'log\' => $log, \'ok\' => true);\n} else {\n    // Выгрузка картинок\n    $st = microtime(true);\n    $mtypes = array_merge(array((int)$Loader->Material_Type->id), (array)$Loader->Material_Type->all_children_ids);\n    if ($Loader->Image_Field->id) {\n        $SQL_query = \"SELECT tM.*, \";\n        if ($Loader->Unique_Field->id) {\n            $SQL_query .= \" tD.value \";\n        } else {\n            $SQL_query .= $Loader->ufid;\n        }\n        $SQL_query .= \" AS ufield\n                      FROM \" . Material::_tablename() . \" AS tM \";\n        if ($Loader->Unique_Field->id) {\n            $SQL_query .= \" JOIN \" . Material::_dbprefix() . \"cms_data AS tD ON tD.pid = tM.id AND tD.fid = \" . (int)$Loader->Unique_Field->id;\n        }\n        $SQL_query .= \" WHERE tM.pid IN (\" . implode(\", \", $mtypes) . \") \";\n        if ($Loader->Unique_Field->id) {\n            $SQL_query .= \" AND tD.value != \'\' \";\n        }\n        $SQL_query .= \" GROUP BY ufield\";\n        $SQL_result = Material::_SQL()->get($SQL_query);\n        $DATA = array();\n        foreach ($SQL_result as $row2) {\n            $row = new Material($row2);\n            if ($attachments = $row->fields[$Loader->Image_Field->urn]->doRich()) {\n                if (!is_array($attachments)) {\n                    $attachments = array($attachments);\n                }\n                foreach ($attachments as $attachment) {\n                    if ($attachment->id) {\n                        $filename = array();\n                        $article = \\SOME\\Text::beautify(trim($row->{$Loader->Unique_Field->id ? $Loader->Unique_Field->urn : $Loader->ufid}));\n                        $realname = $attachment->realname;\n                        if (preg_match(\'/^\' . preg_quote($article) . \'(\' . preg_quote($Loader->sep_string) . \'|$)/umi\', $realname)) {\n                            $filename = $realname;\n                        } else {\n                            $filename[] = $article;\n                            $filename[] = trim($Loader->sep_string);\n                            $filename[] = trim(str_replace($Loader->sep_string, \'\', $realname));\n                            $filename = array_filter($filename);\n                            $filename = trim(implode(\'\', $filename));\n                            while (in_array($filename, $DATA)) {\n                                $filename .= $Loader->sep_string . $attachment->id;\n                            }\n                        }\n                        $DATA[$attachment->file] = trim($filename);\n                    }\n                }\n            }\n            $row->rollback();\n            unset($row);\n        }\n        // header(\'Content-Type: text/plain\');\n        // print_r ($DATA);\n        // exit;\n        if ($DATA) {\n            $tmpname = tempnam(sys_get_temp_dir(), \'\');\n            $z = new \\SOME\\ZipArchive();\n            $z->open($tmpname, \\SOME\\ZipArchive::CREATE);\n            foreach ($DATA as $key => $val) {\n                $z->addFile($key, $val);\n            }\n            $z->close();\n            header(\'Content-Type: application/zip\');\n            header(\'Content-Disposition: attachment; filename=\"\' . $Loader->Material_Type->name . \' - \' . $Loader->Image_Field->name . \'.zip\"\');\n            echo file_get_contents($tmpname);\n            exit;\n        } else {\n            return array(\'localError\' => array(array(\'name\' => \'INVALID\', \'value\' => \'loader\', \'description\' => Module::i()->view->_(\'IMAGES_NOT_FOUND\'))));\n        }\n    } else {\n        return array(\'localError\' => array(array(\'name\' => \'INVALID\', \'value\' => \'loader\', \'description\' => Module::i()->view->_(\'LOADER_HAS_NO_IMAGE_FIELD\'))));\n    }\n}\n', 1),
(27, 1, '__raas_shop_priceloader_interface', 'Стандартный интерфейс загрузчика прайсов', '<?php\n/**\n * Сниппет интерфейса загрузчика прайсов\n *\n * @param PriceLoader $Loader Загрузчик прайсов\n * @param Page $Page Страница, в которую загружаем\n * @param int $rows Сколько строк отступать\n * @param int $cols Сколько колонок отступать\n *\n * Параметры для загрузки:\n * @param [\n *            \'tmp_name\' => string путь к файлу,\n *            \'name\' => string Имя файла\n *        ]|null $file загружаемый файл\n * @param bool $test Тестовый режим\n * @param int $clear Очищать старые материалы и/или страницы\n *                   (константа из PriceLoader::DELETE_PREVIOUS_MATERIALS_...)\n *\n * Параметры для выгрузки:\n * @param \'csv\'|\'xls\'|\'xlsx\' $type Формат, в котором выгружаем\n * @param string $encoding Кодировка для формата CSV,\n *                         в которой выгружаем (совместимо с iconv)\n */\nnamespace RAAS\\CMS\\Shop;\n\nuse RAAS\\Application;\nuse \\RAAS\\CMS\\Page;\n\n$interface = new PriceloaderInterface($Loader);\nif ($_SERVER[\'REQUEST_METHOD\'] == \'POST\') {\n    $type = strtolower(pathinfo($file[\'name\'], PATHINFO_EXTENSION));\n    return $interface->upload(\n        $file[\'tmp_name\'],\n        $type,\n        $Page,\n        $test,\n        $clear,\n        $rows,\n        $cols\n    );\n} else {\n    return $interface->download($Page, $rows, $cols, $type, $encoding);\n}\n', 1),
(28, 1, '__raas_shop_yml_interface', 'Стандартный интерфейс Яндекс.Маркета', '<?php\n/**\n * Сниппет интерфейса Яндекс.Маркета\n * @param Block_YML $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS\\Shop;\n\n$interface = new YMLInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER\n);\n$interface->process(true, 300);\n', 1),
(29, 1, '__raas_robokassa_interface', 'Интерфейс платежной системы \"ROBOKASSA\"', '<?php\nnamespace RAAS\\CMS\\Shop;\n\nuse RAAS\\CMS\\Snippet;\nuse RAAS\\Application;\n\nif (in_array($_GET[\'action\'], array(\'result\', \'success\', \'fail\')) && $_REQUEST[\'InvId\']) {\n    // Подписанное значение - либо RESULT URL, либо SUCCESS URL, либо FAIL URL\n    $inv_id = $_REQUEST[\'InvId\'];\n    $Item = new Order($inv_id);\n    $crc = isset($_REQUEST[\'SignatureValue\']) ? strtoupper($_REQUEST[\'SignatureValue\']) : null;\n    switch ($_GET[\'action\']) {\n        case \'result\':\n            while (ob_get_level()) {\n                ob_end_clean();\n            }\n            $my_crc = strtoupper(md5($_REQUEST[\'OutSum\'] . \':\' . $inv_id . \':\' . $Block->epay_pass2));\n            if ($my_crc != $crc) {\n                echo \'Invalid signature\';\n            } elseif (!$Item->id) {\n                echo \'Invalid order ID#\';\n            } else {\n                // Все ок\n                $history = new Order_History();\n                $history->uid = Application::i()->user->id;\n                $history->order_id = (int)$Item->id;\n                $history->status_id = (int)$Item->status_id;\n                $history->paid = 1;\n                $history->post_date = date(\'Y-m-d H:i:s\');\n                $history->description = PAID_VIA_ROBOKASSA;\n                $history->commit();\n\n                $Item->paid = 1;\n                $Item->commit();\n                echo \'OK\' . (int)$Item->id;\n            }\n            exit;\n            break;\n        case \'success\':\n            $my_crc = strtoupper(md5($_REQUEST[\'OutSum\'] . \':\' . $inv_id . \':\' . $Block->epay_pass1));\n            if ($Item->id) {\n                $OUT[\'epayWidget\'] = Snippet::importByURN(\'robokassa\');\n                $OUT[\'Item\'] = $Item;\n                if ($crc == $my_crc) {\n                    $OUT[\'success\'][(int)$Block->id] = sprintf(ORDER_SUCCESSFULLY_PAID, $Item->id);\n                } else {\n                    $OUT[\'localError\'] = array(\'crc\' => INVALID_CRC);\n                }\n            }\n            break;\n        case \'fail\':\n            if ($Item->id) {\n                $OUT[\'epayWidget\'] = Snippet::importByURN(\'robokassa\');\n                $OUT[\'Item\'] = $Item;\n                $OUT[\'localError\'] = array(\'order\' => sprintf(ORDER_HAS_NOT_BEEN_PAID, $Item->id));\n            }\n            break;\n    }\n} elseif ($Item->id && $_POST[\'epay\']) {\n    $OUT[\'epayWidget\'] = Snippet::importByURN(\'robokassa\');\n    $OUT[\'paymentURL\'] = $Block->epay_test ? \'https://auth.robokassa.ru/Merchant/Index.aspx?IsTest=1\' : \'https://auth.robokassa.ru/Merchant/Index.aspx\';\n    $OUT[\'requestForPayment\'] = true;\n    $crc = $Block->epay_login . \':\' . number_format($Item->sum, 2, \'.\', \'\') . \':\' . (int)$Item->id;\n    if (!$Block->epay_test && $Block->epay_currency && ($Block->epay_currency != \'RUR\')) {\n        $crc .= \':\' . $Block->epay_currency;\n    }\n    $crc .= \':\' . $Block->epay_pass1;\n    $crc = md5($crc);\n    $OUT[\'crc\'] = $crc;\n}\n', 1),
(30, 1, '__raas_my_orders_interface', 'Стандартный интерфейс истории заказов', '<?php\n/**\n * Стандартный интерфейс сервиса \"Мои заказы\"\n * @param Block_PHP $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS\\Shop;\n\n$interface = new MyOrdersInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(31, 1, 'catalog_interface', 'Интерфейс каталога', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\n$IN = (array)$_GET;\r\nparse_str(trim($Block->params), $temp);\r\n$IN = array_merge($IN, (array)$temp);\r\n\r\n$OUT = array();\r\nif ($Page->Material && $Block->nat) {\r\n    $Item = $Page->Material;\r\n    if ($Page->initialURL != $Item->url) {\r\n        // Адреса не совпадают\r\n        if ((int)$Block->legacy && ($Item->pid == $Block->material_type)) {\r\n            // Установлена переадресация\r\n            header(\"HTTP/1.1 301 Moved Permanently\");\r\n            header(\'Location: http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . $Item->url);\r\n            exit;\r\n        } else {\r\n            return;\r\n        }\r\n    }\r\n    $OUT[\'Item\'] = $Item;\r\n\r\n    // Отзывы\r\n    // $commentsMType = Material_Type::importByURN(\'goods_comments\');\r\n    // $commentMaterialField = $commentsMType->fields[\'material\'];\r\n    // $SQL_query = \"SELECT tM.*\r\n    //                 FROM \" . Material::_tablename() . \" AS tM\r\n    //                 JOIN cms_data AS tD ON tD.fid = \" . (int)$commentMaterialField->id . \" AND tD.pid = tM.id\r\n    //                WHERE tM.pid = \" . (int)$commentsMType->id . \" AND tM.vis AND tD.value = \" . (int)$Page->Material->id\r\n    //            . \" ORDER BY tM.post_date DESC\";\r\n    // $comments = Material::getSQLSet($SQL_query);\r\n    // $OUT[\'comments\'] = $comments;\r\n    // $OUT[\'commentFormBlock\'] = Block::spawn(71);\r\n\r\n    foreach (array(\'name\', \'meta_title\', \'meta_keywords\', \'meta_description\', \'h1\') as $key) {\r\n        $Page->{\'old\' . ucfirst($key)} = $Page->$key;\r\n        $Page->$key = $Item->$key;\r\n    }\r\n    $Item->proceed = true;\r\n    $_SESSION[\'visited\'] = (array)$_SESSION[\'visited\'];\r\n    $_SESSION[\'visited\'][] = (int)$Item->id;\r\n    $_SESSION[\'visited\'] = array_unique($_SESSION[\'visited\']);\r\n    $_SESSION[\'visited\'] = array_filter($_SESSION[\'visited\']);\r\n    $_SESSION[\'visited\'] = array_values($_SESSION[\'visited\']);\r\n} else {\r\n    $MType = new Material_Type($Block->material_type);\r\n    $cc = new Catalog_Cache($MType);\r\n    if (!$cc->load()) {\r\n        $cc->getCache();\r\n        $cc->save();\r\n    }\r\n    $Set = $cc->data;\r\n\r\n    if (!$MType->global_type) {\r\n        if ($Page->pid) {\r\n            $pages_ids = array_merge(array((int)$Page->id), (array)$Page->all_children_ids);\r\n        } else {\r\n            $pages_ids = array($Page->id);\r\n        }\r\n        $Set = array_filter(\r\n            $Set,\r\n            function ($row) use ($pages_ids) {\r\n                return (bool)array_intersect((array)$row[\'pages_ids\'], $pages_ids);\r\n            }\r\n        );\r\n    }\r\n\r\n    $doSearch = false;\r\n\r\n    if ($Page->pid) {\r\n        // Точное соответствие\r\n        foreach (array(/*...*/) as $key) {\r\n            if ($IN[$key]) {\r\n                $doSearch = true;\r\n                $Set = array_filter(\r\n                    $Set,\r\n                    function ($x) use ($IN, $key) {\r\n                        return (bool)array_intersect((array)$x[$key], (array)$IN[$key]);\r\n                    }\r\n                );\r\n            }\r\n        }\r\n\r\n        // Вхождение подстроки\r\n        $getValueSubstringFilterFunction = function ($IN, $key) {\r\n            return function ($y) use ($IN, $key) {\r\n                return (bool)stristr($y, $IN[$key]);\r\n            };\r\n        };\r\n        foreach (array(\'article\') as $key) {\r\n            if ($IN[$key]) {\r\n                $doSearch = true;\r\n                $Set = array_filter(\r\n                    $Set,\r\n                    function ($x) use ($IN, $key, $getValueSubstringFilterFunction) {\r\n                        return (bool)array_filter((array)$x[$key], $getValueSubstringFilterFunction($IN, $key));\r\n                    }\r\n                );\r\n            }\r\n        }\r\n\r\n        // От .. до\r\n        $getValueFromToFilterFunction = function ($IN, $key, $from = true) {\r\n            if ($from) {\r\n                return function ($y) use ($IN, $key) {\r\n                    return $y >= $IN[$key . \'_from\'];\r\n                };\r\n            } else {\r\n                return function ($y) use ($IN, $key) {\r\n                    return $y <= $IN[$key . \'_to\'];\r\n                };\r\n            }\r\n        };\r\n\r\n        $getRowFromToFilterFunction = function ($IN, $key, $from = true) use ($getValueFromToFilterFunction) {\r\n            $f = $getValueFromToFilterFunction($IN, $key, $from);\r\n            return function ($x) use ($key, $f) {\r\n                return (bool)array_filter((array)$x[$key], $f);\r\n            };\r\n        };\r\n        foreach (array(\'price\') as $key) {\r\n            if ($IN[$key . \'_from\']) {\r\n                $doSearch = true;\r\n                $Set = array_filter($Set, $getRowFromToFilterFunction($IN, $key, true));\r\n            }\r\n            if ($IN[$key . \'_to\']) {\r\n                $doSearch = true;\r\n                $Set = array_filter($Set, $getRowFromToFilterFunction($IN, $key, false));\r\n            }\r\n        }\r\n    }\r\n\r\n    if ($Page->visChildren && !$doSearch && $Page->pid) {\r\n        $cats = $Page->visChildren;\r\n        $temp = array();\r\n        foreach ($cats as $cat) {\r\n            $ids = array_merge(array($cat->id), (array)$cat->all_children_ids);\r\n            foreach ($Set as $row) {\r\n                if ((bool)array_intersect((array)$row[\'pages_ids\'], $ids)) {\r\n                    $temp[$cat->id]++;\r\n                }\r\n            }\r\n            $cat->reload();\r\n            unset($cat);\r\n        }\r\n        $cats = array();\r\n        foreach ($temp as $key => $val) {\r\n            $cats[$key] = new Page($key);\r\n            $cats[$key]->counter = $val;\r\n        }\r\n        $OUT[\'subCats\'] = array_values($cats);\r\n    }\r\n\r\n    if ($Set) {\r\n        $Set = array_filter(\r\n            $Set,\r\n            function ($row) use ($Page) {\r\n                return in_array($Page->id, (array)$row[\'pages_ids\']);\r\n            }\r\n        );\r\n        $sortFunction = array();\r\n        if (in_array($IN[\'sort\'], array(\'price\'))) {\r\n            // Вариант для сортировки из фильтра\r\n            $sortFunction[] = \'(\' . ($IN[\'order\'] == \'desc\' ? \'-1 * \' : \'\') . \'((int)$a[\"\' . $IN[\'sort\'] . \'\"] - (int)$b[\"\' . $IN[\'sort\'] . \'\"]))\';\r\n        } else {\r\n            $sortFunction[] = \'((int)(bool)$b[\"priority\"] - (int)(bool)$a[\"priority\"])\';\r\n            $sortFunction[] = \'((int)$a[\"priority\"] - (int)$b[\"priority\"])\';\r\n            // Вариант для статической сортировки\r\n            if (isset($Block->sort_var_name, $IN[(string)$Block->sort_var_name])) {\r\n                $sortKey = $IN[(string)$Block->sort_var_name];\r\n                if ($f = $MType->fields[$sortKey]->id) {\r\n                    $sortFunction[] = \'strnatcmp($a[\"\' . addslashes($sortKey) . \'\"], $b[\"\' . addslashes($sortKey) . \'\"])\';\r\n                }\r\n            }\r\n            $sortFunction[] = \'((int)$a[\"price\"] - (int)$b[\"price\"])\';\r\n        }\r\n        $sortFunction = \'return (\' . implode(\' ?: \', $sortFunction) . \');\';\r\n        $sortFunction = create_function(\'$a, $b\', $sortFunction);\r\n        usort($Set, $sortFunction);\r\n\r\n        if (isset($Block->pages_var_name, $Block->rows_per_page) && (int)$Block->rows_per_page) {\r\n            $Pages = new \\SOME\\Pages(isset($IN[$Block->pages_var_name]) ? (int)$IN[$Block->pages_var_name] : 1, (int)$Block->rows_per_page);\r\n        }\r\n\r\n        $Set = \\SOME\\SOME::getArraySet($Set, $Pages);\r\n        $nativeFields = Material::_classes();\r\n        $nativeFields = $nativeFields[\'RAAS\\\\CMS\\\\Material\'][\'fields\'];\r\n        $Set = array_map(function ($row) use ($nativeFields) {\r\n            $native = array_intersect_key($row, array_flip($nativeFields));\r\n            $row2 = new Material($native);\r\n            $row2->metacache = $row;\r\n            return $row2;\r\n        }, $Set);\r\n\r\n        $OUT[\'Set\'] = $Set;\r\n        if ($Pages !== null) {\r\n            $OUT[\'Pages\'] = $Pages;\r\n        }\r\n    }\r\n\r\n    $OUT[\'MType\'] = $MType;\r\n    $OUT[\'doSearch\'] = $doSearch;\r\n}\r\nreturn $OUT;\r\n', 0),
(32, 2, 'cart', 'Корзина', '<?php\r\nnamespace RAAS\\CMS\\Shop;\r\n\r\nuse \\RAAS\\CMS\\Material;\r\nuse \\RAAS\\CMS\\Package;\r\n\r\nfunction formatPrice($price)\r\n{\r\n    $remainder = (float)$price - (float)(int)$price;\r\n    return str_replace(\' \', \'&nbsp;\', number_format((float)$price, ($remainder > 0) ? 2 : 0, \',\', \' \' ));\r\n}\r\n\r\nif ($_GET[\'AJAX\']) {\r\n    $temp = array();\r\n    $temp[\'count\'] = (int)$Cart->count;\r\n    $temp[\'sum\'] = (float)$Cart->sum;\r\n    $temp[\'no_amount\'] = (int)$Cart->no_amount;\r\n    foreach ($Cart->items as $row) {\r\n        $row2 = new Material($row->id);\r\n        $temp[\'items\'][] = array(\r\n            \'id\' => $row->id,\r\n            \'meta\' => $row->meta,\r\n            \'amount\' => $row->amount,\r\n            \'price\' => $row->realprice,\r\n            \'name\' => $row->name,\r\n            \'url\' => $row2->url,\r\n			      \'image\' => \'/\' . $row2->visImages[0]->smallURL,\r\n            \'min\' => $row2->min,\r\n        );\r\n    }\r\n    echo json_encode($temp);\r\n    exit;\r\n} elseif ($epayWidget && ($epayWidget instanceof \\RAAS\\CMS\\Snippet)) {\r\n    eval(\'?\' . \'>\' . $epayWidget->description);\r\n} elseif ($success[(int)$Block->id]) {\r\n    ?>\r\n    <div class=\"notifications\">\r\n      <div class=\"alert alert-success\"><?php echo sprintf(ORDER_SUCCESSFULLY_SENT, $Item->id)?></div>\r\n    </div>\r\n<?php } elseif ($Cart->items) { ?>\r\n    <div class=\"cart\">\r\n      <form action=\"#feedback\" method=\"post\" enctype=\"multipart/form-data\">\r\n        <div class=\"cart__inner\">\r\n          <table class=\"table table-striped cart-table\" data-role=\"cart-table\">\r\n            <thead>\r\n              <tr>\r\n                <th class=\"cart-table__image-col\"><?php echo IMAGE?></th>\r\n                <th class=\"cart-table__name-col\"><?php echo NAME?></th>\r\n                <th class=\"cart-table__price-col\"><?php echo PRICE?></th>\r\n                <?php if (!$Cart->cartType->no_amount) { ?>\r\n                    <th class=\"cart-table__amount-col\"><?php echo AMOUNT?></th>\r\n                    <th class=\"cart-table__sum-col\"><?php echo SUM?></th>\r\n                <?php } ?>\r\n                <th class=\"cart-table__actions-col\"></th>\r\n              </tr>\r\n            </thead>\r\n            <tbody data-role=\"cart__body_main\">\r\n              <?php $sum = $am = 0; foreach ($Cart->items as $row) { $row2 = new Material((int)$row->id); ?>\r\n                <tr data-role=\"cart-item\" data-id=\"<?php echo (int)$row->id?>\" data-price=\"<?php echo number_format($row->realprice, 2, \'.\', \'\')?>\">\r\n                  <td class=\"cart-table__image-col\">\r\n                    <?php if ($row2->visImages) { ?>\r\n                        <a <?php echo $row2->url ? \'href=\"\' . htmlspecialchars($row2->url) . \'\"\' : \'\'?>>\r\n                          <img src=\"/<?php echo htmlspecialchars(addslashes($row2->visImages[0]->smallURL))?>\" style=\"max-width: 48px\" alt=\"<?php echo htmlspecialchars($row2->visImages[0]->name ?: $row->name)?>\" /></a>\r\n                    <?php } ?>\r\n                  </td>\r\n                  <td class=\"cart-table__name-col\"><a <?php echo $row2->url ? \'href=\"\' . htmlspecialchars($row2->url) . \'\"\' : \'\'?>><?php echo htmlspecialchars($row->name)?></a></td>\r\n                  <td data-role=\"price\" class=\"cart-table__price-col\">\r\n                    <?php echo formatPrice($row->realprice)?> <span class=\"fa fa-rub\"></span>\r\n                    <?php if ($Cart->cartType->no_amount) { ?>\r\n                        <input type=\"hidden\" name=\"amount[<?php echo htmlspecialchars((int)$row->id . \'_\' . $row->meta)?>]\" value=\"<?php echo (int)$row->amount?>\" />\r\n                    <?php } ?>\r\n                  </td>\r\n                  <?php if (!$Cart->cartType->no_amount) { ?>\r\n                      <td class=\"cart-table__amount-col\"><input type=\"number\" class=\"form-control\" style=\"max-width: 8em\" data-role=\"amount\" name=\"amount[<?php echo htmlspecialchars((int)$row->id . \'_\' . $row->meta)?>]\" min=\"<?php echo (int)$row2->min ?: 1?>\" value=\"<?php echo (int)$row->amount?>\" /></td>\r\n                      <td class=\"cart-table__sum-col\"><span data-role=\"sum\"><?php echo formatPrice($row->amount * $row->realprice)?></span> <span class=\"fa fa-rub\"></span></td>\r\n                  <?php } ?>\r\n                  <td class=\"cart-table__actions-col\">\r\n                    <a href=\"?action=delete&id=<?php echo (int)$row->id . ($row->meta ? \'&meta=\' . htmlspecialchars($row->meta) : \'\')?>\" data-role=\"delete-item\">\r\n                      <i class=\"fa fa-remove\" title=\"<?php echo DELETE?>\"></i>\r\n                    </a>\r\n                  </td>\r\n                </tr>\r\n              <?php $sum += $row->amount * $row->realprice; $am += $row->amount; } ?>\r\n			</tbody>\r\n			<tbody>\r\n              <?php if ($Form->id) { ?>\r\n                  <tr>\r\n                    <th colspan=\"<?php echo !$Cart->cartType->no_amount ? \'3\' : \'2\'?>\"><?php echo TOTAL_SUM?>:</th>\r\n                    <?php if (!$Cart->cartType->no_amount) { ?>\r\n                        <th class=\"cart-table__amount-col\"><span data-role=\"total-amount\"><?php echo (int)$am; ?></span></td>\r\n                    <?php } ?>\r\n                    <th class=\"cart-table__sum-col\"><span data-role=\"total-sum\"><?php echo formatPrice($sum)?></span>&nbsp;<span class=\"fa fa-rub\"></span></th>\r\n                    <th class=\"cart-table__actions-col\"></th>\r\n                  </tr>\r\n              <?php } ?>\r\n            </tbody>\r\n          </table>\r\n        </div>\r\n        <?php if ($Form->id) { ?>\r\n            <div class=\"form-horizontal\">\r\n              <?php include Package::i()->resourcesDir . \'/form2.inc.php\'?>\r\n              <div data-role=\"notifications\" <?php echo ($success[(int)$Block->id] || $localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n                <div class=\"alert alert-success\" <?php echo ($success[(int)$Block->id]) ? \'\' : \'style=\"display: none\"\'?>><?php echo FEEDBACK_SUCCESSFULLY_SENT?></div>\r\n                <div class=\"alert alert-danger\" <?php echo ($localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n                  <ul>\r\n                    <?php foreach ((array)$localError as $key => $val) { ?>\r\n                        <li><?php echo htmlspecialchars($val)?></li>\r\n                    <?php } ?>\r\n                  </ul>\r\n                </div>\r\n              </div>\r\n\r\n              <div data-role=\"feedback-form\" <?php echo $success[(int)$Block->id] ? \'style=\"display: none\"\' : \'\'?>>\r\n                <p><?php echo ASTERISK_MARKED_FIELDS_ARE_REQUIRED?></p>\r\n                <?php if ($Form->signature) { ?>\r\n                      <input type=\"hidden\" name=\"form_signature\" value=\"<?php echo md5(\'form\' . (int)$Form->id . (int)$Block->id)?>\" />\r\n                <?php } ?>\r\n                <?php if ($Form->antispam == \'hidden\' && $Form->antispam_field_name) { ?>\r\n                      <input type=\"text\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" value=\"<?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?>\" style=\"position: absolute; left: -9999px\" />\r\n                <?php } ?>\r\n                <?php foreach ($Form->fields as $row) { ?>\r\n                    <div class=\"form-group\">\r\n                      <label<?php echo !$row->multiple ? \' for=\"\' . htmlspecialchars($row->urn . $row->id . \'_\' . $Block->id) . \'\"\' : \'\'?> class=\"control-label col-sm-3 col-md-2\"><?php echo htmlspecialchars($row->name . ($row->required ? \'*\' : \'\'))?></label>\r\n                      <div class=\"col-sm-9 col-md-4\">\r\n                        <?php $getField($row, $DATA); ?>\r\n                      </div>\r\n                    </div>\r\n                <?php } ?>\r\n                <?php if ($Form->antispam == \'captcha\' && $Form->antispam_field_name) { ?>\r\n                    <div class=\"form-group\">\r\n                      <label for=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" class=\"control-label col-sm-3 col-md-2\"><?php echo CAPTCHA?></label>\r\n                      <div class=\"col-sm-9 col-md-4\">\r\n                        <img src=\"/assets/kcaptcha/?<?php echo session_name() . \'=\' . session_id()?>\" /><br />\r\n                        <input type=\"text\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" />\r\n                      </div>\r\n                    </div>\r\n                <?php } ?>\r\n                <?php if ($Block->EPay_Interface->id && !$Form->fields[\'epay\']) { ?>\r\n                    <div class=\"form-group\">\r\n                      <label for=\"name\" class=\"control-label col-sm-3 col-md-2\"><?php echo PAYMENT_METHOD?></label>\r\n                      <div class=\"col-sm-9 col-md-4\">\r\n                        <label><input type=\"radio\" name=\"epay\" value=\"0\" <?php echo !$DATA[\'epay\'] ? \'checked=\"checked\"\' : \'\'?> /> <?php echo PAY_ON_DELIVERY?></label>\r\n                        <label><input type=\"radio\" name=\"epay\" value=\"1\" <?php echo $DATA[\'epay\'] ? \'checked=\"checked\"\' : \'\'?> /> <?php echo PAY_BY_EPAY?></label>\r\n                      </div>\r\n                    </div>\r\n                <?php } ?>\r\n                <div class=\"form-group\">\r\n                  <div class=\"col-sm-9 col-md-4 col-sm-offset-3 col-md-offset-2\"><button class=\"btn btn-primary\" type=\"submit\"><?php echo SEND?></button></div>\r\n                </div>\r\n              </div>\r\n            </div>\r\n        <?php } else { ?>\r\n          <p><a href=\"?action=clear\" data-role=\"clear-cart-trigger\"><?php echo CLEAR_FAVORITES?></a></p>\r\n        <?php } ?>\r\n      </form>\r\n    </div>\r\n    <script src=\"/js/cart.js\"></script>\r\n<?php\r\n} else {\r\n    if ($Form->id) {\r\n        echo YOUR_CART_IS_EMPTY;\r\n    } else {\r\n        echo YOUR_FAVORITES_IS_EMPTY;\r\n    }\r\n}\r\n?>\r\n', 0);
INSERT INTO `cms_snippets` (`id`, `pid`, `urn`, `name`, `description`, `locked`) VALUES
(33, 2, 'robokassa', 'ROBOKASSA', '<?php\r\nnamespace RAAS\\CMS\\Shop;\r\n\r\nuse \\RAAS\\CMS\\Material;\r\nuse \\RAAS\\CMS\\Package;\r\n\r\nif ($success[(int)$Block->id] || $localError) {\r\n    ?>\r\n    <div class=\"notifications\">\r\n      <?php if ($success[(int)$Block->id]) { ?>\r\n          <div class=\"alert alert-success\"><?php echo htmlspecialchars($success[(int)$Block->id])?></div>\r\n      <?php } elseif ($localError) { ?>\r\n          <div class=\"alert alert-danger\">\r\n            <ul>\r\n              <?php foreach ((array)$localError as $key => $val) { ?>\r\n                  <li><?php echo htmlspecialchars($val)?></li>\r\n              <?php } ?>\r\n            </ul>\r\n          </div>\r\n      <?php } ?>\r\n    </div>\r\n<?php } ?>\r\n<?php if ($Item->id) { ?>\r\n    <section class=\"cart\">\r\n      <h2><?php echo htmlspecialchars(sprintf(ORDER_NUM, (int)$Item->id, $_SERVER[\'HTTP_HOST\']))?></h2>\r\n      <table class=\"table table-striped cart-table\" data-role=\"cart-table\">\r\n        <thead>\r\n          <tr>\r\n            <th class=\"cart-table__image-col\"><?php echo IMAGE?></th>\r\n            <th class=\"cart-table__name-col\"><?php echo NAME?></th>\r\n            <th class=\"cart-table__price-col\"><?php echo PRICE?></th>\r\n            <th class=\"cart-table__amount-col\"><?php echo AMOUNT?></th>\r\n            <th class=\"cart-table__sum-col\"><?php echo SUM?></th>\r\n          </tr>\r\n        </thead>\r\n        <tbody>\r\n          <?php foreach ($Item->items as $row) { $row2 = new Material((int)$row->id); ?>\r\n            <tr data-role=\"cart-item\">\r\n              <td class=\"text-center cart-table__image-col\">\r\n                <?php if ($row2->visImages) { ?>\r\n                    <a <?php echo $row2->url ? \'href=\"\' . htmlspecialchars($row2->url) . \'\" target=\"_blank\"\' : \'\'?>>\r\n                      <img src=\"/<?php echo htmlspecialchars(addslashes($row2->visImages[0]->tnURL))?>\" style=\"max-width: 48px\" alt=\"<?php echo htmlspecialchars($row2->visImages[0]->name ?: $row->name)?>\" target=\"_blank\" /></a>\r\n                <?php } ?>\r\n              </td>\r\n              <td class=\"cart-table__name-col\">\r\n                <a <?php echo $row2->url ? \'href=\"\' . htmlspecialchars($row2->url) . \'\" target=\"_blank\"\' : \'\'?>><?php echo htmlspecialchars($row->name)?></a>\r\n              </td>\r\n              <td data-role=\"price\" class=\"cart-table__price-col\">\r\n                <?php echo formatPrice($row->realprice)?> <span class=\"fa fa-rub\"></span>\r\n              </td>\r\n              <td class=\"cart-table__amount-col\"><?php echo (int)$row->amount?></td>\r\n              <td class=\"cart-table__sum-col\"><span data-role=\"sum\"><?php echo formatPrice($row->amount * $row->realprice)?></span> <span class=\"fa fa-rub\"></span></td>\r\n            </tr>\r\n          <?php } ?>\r\n          <tr>\r\n            <th colspan=\"3\"><?php echo TOTAL_SUM?>:</th>\r\n            <th class=\"cart-table__amount-col\"><span data-role=\"total-amount\"><?php echo (int)$Item->count; ?></span></td>\r\n            <th class=\"cart-table__sum-col\"><span data-role=\"total-sum\"><?php echo formatPrice($Item->sum)?></span>&nbsp;<span class=\"fa fa-rub\"></span></th>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n      <div class=\"form-horizontal\">\r\n        <div data-role=\"feedback-form\">\r\n          <div class=\"form-group\">\r\n            <label class=\"control-label col-sm-3 col-md-2\"><?php echo YOUR_ORDER_ID?></label>\r\n            <div class=\"col-sm-9 col-md-4\"><strong><?php echo (int)$Item->id?></strong></div>\r\n          </div>\r\n          <?php foreach ($Item->fields as $row) { ?>\r\n              <div class=\"form-group\">\r\n                <label class=\"control-label col-sm-3 col-md-2\"><?php echo htmlspecialchars($row->name)?></label>\r\n                <div class=\"col-sm-9 col-md-4\"><?php echo htmlspecialchars($Item->fields[$row->urn]->doRich())?></div>\r\n              </div>\r\n          <?php } ?>\r\n          <?php if ($requestForPayment) { ?>\r\n              <div class=\"form-group\">\r\n                <form action=\"<?php echo htmlspecialchars($paymentURL)?>\" method=\"post\" enctype=\"multipart/form-data\">\r\n                  <input type=\"hidden\" name=\"MrchLogin\" value=\"<?php echo htmlspecialchars($Block->epay_login)?>\" />\r\n                  <input type=\"hidden\" name=\"OutSum\" value=\"<?php echo number_format((float)$Item->sum, 2, \'.\', \'\')?>\" />\r\n                  <input type=\"hidden\" name=\"InvId\" value=\"<?php echo (int)$Item->id?>\" />\r\n                  <input type=\"hidden\" name=\"Desc\" value=\"<?php echo sprintf(ORDER_NUM, (int)$Item->id, $_SERVER[\'HTTP_HOST\'])?>\" />\r\n                  <input type=\"hidden\" name=\"SignatureValue\" value=\"<?php echo htmlspecialchars($crc)?>\" />\r\n                  <?php if (!$Block->epay_test && $Block->epay_currency && ($Block->epay_currency != \'RUR\')) { ?>\r\n                      <input type=\"hidden\" name=\"OutSumCurrency\" value=\"<?php echo htmlspecialchars($Block->epay_currency)?>\" />\r\n                  <?php } ?>\r\n                  <input type=\"hidden\" name=\"Culture\" value=\"<?php echo htmlspecialchars($Page->lang)?>\" />\r\n                  <input type=\"hidden\" name=\"Encoding\" value=\"UTF-8\" />\r\n                  <div class=\"col-sm-9 col-md-4 col-sm-offset-3 col-md-offset-2\"><button class=\"btn btn-primary\" type=\"submit\"><?php echo PAY?></button></div>\r\n                </form>\r\n              </div>\r\n          <?php } ?>\r\n        </div>\r\n      </div>\r\n    </section>\r\n<?php } ?>\r\n', 0),
(34, 2, 'yml', 'Яндекс.Маркет', '<?php\r\nnamespace RAAS\\CMS\\Shop;\r\n\r\nuse \\DOMDocument;\r\n\r\nheader(\'Content-Type: application/xml\');\r\n// echo $yml->asXML();\r\n// $dom = new DOMDocument(\'1.0\');\r\n// $dom->preserveWhiteSpace = false;\r\n// $dom->formatOutput = true;\r\n// $dom->loadXML($yml->asXML());\r\n// echo $dom->saveXML();\r\n', 0),
(35, 2, 'item_inc', 'Блок товара', '<?php\r\nnamespace RAAS\\CMS\\Shop;\r\n\r\n$formatPrice = function($price) {\r\n    $remainder = (float)$price - (float)(int)$price;\r\n    return str_replace(\' \', \'&nbsp;\', number_format((float)$price, ($remainder > 0) ? 2 : 0, \',\', \' \' ));\r\n};\r\n\r\n$showItem = function($row) use ($formatPrice)\r\n{\r\n    ?>\r\n    <div class=\"catalog-item\">\r\n      <div class=\"catalog-item__title\">\r\n        <a href=\"<?php echo $row->url?>\"><?php echo htmlspecialchars($row->name)?></a>\r\n      </div>\r\n      <?php if ($row->article) { ?>\r\n          <div class=\"catalog-item__article\">\r\n            <?php echo ARTICLE_SHORT?>\r\n            <a href=\"<?php echo $row->url?>\"><?php echo htmlspecialchars($row->article)?></a>\r\n          </div>\r\n      <?php } ?>\r\n      <a href=\"<?php echo $row->url?>\" class=\"catalog-item__image<?php echo !$row->visImages ? \' catalog-item__image_nophoto\' : \'\'?>\">\r\n        <?php if ($row->visImages) { ?>\r\n            <img src=\"/<?php echo htmlspecialchars(addslashes($row->visImages[0]->smallURL))?>\" alt=\"<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>\" />\r\n        <?php } ?>\r\n      </a>\r\n      <form action=\"/cart/\" class=\"catalog-item__controls\" data-role=\"add-to-cart-form\" data-id=\"<?php echo (int)$row->id?>\" data-price=\"<?php echo (int)$row->price?>\">\r\n        <?php if ($row->available) { ?>\r\n            <input type=\"hidden\" name=\"action\" value=\"add\" />\r\n            <input type=\"hidden\" name=\"id\" value=\"<?php echo (int)$row->id?>\" />\r\n            <input type=\"hidden\" name=\"back\" value=\"1\" />\r\n            <?php /* <input type=\"hidden\" name=\"amount\" value=\"1\" /> */?>\r\n        <?php } ?>\r\n        <div class=\"catalog-item__text\">\r\n          <div class=\"catalog-item__price-container\" data-price=\"<?php echo (float)$row->price?>\">\r\n            <?php if ($row->price_old && ($row->price_old != $row->price)) { ?>\r\n                <span class=\"catalog-item__price catalog-item__price_old\"><?php echo $formatPrice((float)$row->price_old)?></span>\r\n            <?php } ?>\r\n            <span class=\"catalog-item__price <?php echo ($row->price_old && ($row->price_old != $row->price)) ? \' catalog-item__price_new\' : \'\'?>\">\r\n              <span data-role=\"price-container\">\r\n                <?php echo $formatPrice((float)$row->price)?>\r\n              </span>\r\n              <i class=\"fa fa-rub\"></i>\r\n            </span>\r\n          </div>\r\n          <div class=\"catalog-item__available\"><?php echo $row->available ? \'<span class=\"text-success\">В наличии</span>\' : \'<span class=\"text-danger\">Под заказ</span>\'?></div>\r\n        </div>\r\n        <div class=\"catalog-item__read-more\">\r\n          <!--noindex-->\r\n          <div class=\"catalog-item__add-to-cart\">\r\n            <?php if ($row->available) { ?>\r\n                <input type=\"number\" class=\"form-control\" autocomplete=\"off\" name=\"amount\" min=\"<?php echo (int)$row->min ?: 1?>\" step=\"<?php echo (int)$row->step ?: 1?>\" value=\"<?php echo (int)$row->min ?: 1?>\" />\r\n                <button type=\"submit\" class=\"btn btn-danger\" title=\"<?php echo TO_CART?>\"><span class=\"fa fa-shopping-cart\"></span></button>\r\n                <?php /* <a href=\"/cart/?action=add&id=<?php echo (int)$row->id?>\" class=\"btn btn-danger\" data-role=\"add-to-cart-trigger\" data-id=\"<?php echo (int)$row->id?>\" data-price=\"<?php echo (int)$row->price?>\" title=\"<?php echo TO_CART?>\" data-active-title=\"<?php echo DELETE_FROM_CART?>\"><span class=\"fa fa-shopping-cart\"></span></a> */ ?>\r\n            <?php } ?>\r\n            <a href=\"/favorites/?action=add&id=<?php echo (int)$row->id?>\" class=\"btn btn-info\" data-role=\"add-to-favorites-trigger\" data-id=\"<?php echo (int)$row->id?>\" title=\"<?php echo TO_FAVORITES?>\" data-active-title=\"<?php echo DELETE_FROM_FAVORITES?>\" rel=\"nofollow\"><span class=\"fa fa-star\"></span></a>\r\n          </div>\r\n          <!--/noindex-->\r\n        </div>\r\n      </form>\r\n    </div>\r\n    <?php\r\n};\r\n', 0),
(36, 2, 'category_inc', 'Блок категории', '<?php\r\nnamespace RAAS\\CMS\\Shop;\r\n\r\n$showCategory = function($row)\r\n{\r\n    $queryString = http_build_query(array_intersect_key($_GET, array_flip(array(\'brand\', \'model\', \'engine\'))));\r\n    $queryString = $queryString ? \'?\' . $queryString : \'\';\r\n    ?>\r\n    <a class=\"catalog-category\" href=\"<?php echo $row->url . $queryString ?>\">\r\n      <div class=\"catalog-category__image<?php echo !$row->image->id ? \' catalog-category__image_nophoto\' : \'\'?>\">\r\n        <?php if ($row->image->id) { ?>\r\n            <img src=\"/<?php echo htmlspecialchars(addslashes($row->image->smallURL))?>\" alt=\"<?php echo htmlspecialchars($row->image->name ?: $row->name)?>\" />\r\n        <?php } ?>\r\n      </div>\r\n      <div class=\"catalog-category__text\">\r\n        <div class=\"catalog-category__title\">\r\n          <?php echo htmlspecialchars($row->name . ((int)$row->counter ? \' (\' . (int)$row->counter . \')\' : \'\'))?>\r\n        </div>\r\n      </div>\r\n    </a>\r\n    <?php\r\n};\r\n', 0),
(37, 2, 'catalog', 'Каталог продукции', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse RAAS\\CMS\\Shop\\Video;\r\nuse RAAS\\Attachment;\r\n\r\neval(\'?\' . \'>\' . Snippet::importByURN(\'category_inc\')->description);\r\neval(\'?\' . \'>\' . Snippet::importByURN(\'item_inc\')->description);\r\neval(\'?\' . \'>\' . Snippet::importByURN(\'file_inc\')->description);\r\n$formatPrice = function($price) {\r\n    $remainder = (float)$price - (float)(int)$price;\r\n    return str_replace(\' \', \'&nbsp;\', number_format((float)$price, ($remainder > 0) ? 2 : 0, \',\', \' \' ));\r\n};\r\n\r\n\r\nif ($Item) {\r\n    ?>\r\n    <div class=\"catalog\">\r\n      <div class=\"catalog-article\" itemscope itemtype=\"http://schema.org/Product\">\r\n        <meta itemprop=\"name\" content=\"<?php echo htmlspecialchars($Item->name)?>\" />\r\n        <div class=\"catalog-article__article\">\r\n          <?php echo ARTICLE_SHORT?> <span itemprop=\"productID\"><?php echo htmlspecialchars($Item->article)?></span>\r\n        </div>\r\n        <div class=\"row\">\r\n          <?php if ($Item->visImages) { ?>\r\n              <div class=\"col-sm-6 col-lg-5\">\r\n                <div class=\"catalog-article__images-container\">\r\n                  <div class=\"catalog-article__image\">\r\n                    <?php for ($i = 0; $i < count($Item->visImages); $i++) { ?>\r\n                        <a itemprop=\"image\" href=\"/<?php echo $Item->visImages[$i]->fileURL?>\" <?php echo $i ? \'style=\"display: none\"\' : \'\'?> data-image-num=\"<?php echo (int)$i?>\" data-lightbox-gallery=\"g\">\r\n                          <img src=\"/<?php echo htmlspecialchars($Item->visImages[$i]->tnURL)?>\" alt=\"<?php echo htmlspecialchars($Item->visImages[$i]->name ?: $row->name)?>\" /></a>\r\n                    <?php } ?>\r\n                  </div>\r\n                  <?php if (count($Item->visImages) > 1) { ?>\r\n                      <div class=\"catalog-article__images hidden-xs\">\r\n                        <?php for ($i = 0; $i < count($Item->visImages); $i++) { $row = $Item->visImages[$i]; ?>\r\n                            <div data-href=\"/<?php echo htmlspecialchars(addslashes($row->fileURL))?>\" class=\"catalog-article__additional-image\" data-image-num=\"<?php echo (int)$i?>\">\r\n                              <img src=\"/<?php echo htmlspecialchars($row->tnURL)?>\" alt=\"<?php echo htmlspecialchars($row->name)?>\" /></div>\r\n                        <?php } ?>\r\n                      </div>\r\n                  <?php } ?>\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n          <div class=\"col-sm-6 col-lg-7\">\r\n            <div class=\"catalog-article__details\">\r\n              <div itemprop=\"offers\" itemscope itemtype=\"http://schema.org/Offer\">\r\n                <div class=\"catalog-article__text\">\r\n                  <div class=\"catalog-article__price-container\" data-price=\"<?php echo (float)$Item->price?>\">\r\n                    <?php if ($Item->price_old && ($Item->price_old != $Item->price)) { ?>\r\n                        <span class=\"catalog-article__price catalog-article__price_old\"><?php echo $formatPrice((float)$Item->price_old)?></span>\r\n                    <?php } ?>\r\n                    <span class=\"catalog-article__price <?php echo ($Item->price_old && ($Item->price_old != $Item->price)) ? \' catalog-article__price_new\' : \'\'?>\">\r\n                      <span data-role=\"price-container\" itemprop=\"price\" content=\"<?php echo (float)$Item->price?>\">\r\n                        <?php echo $formatPrice((float)$Item->price)?>\r\n                      </span>\r\n                      <i class=\"fa fa-rub\" itemprop=\"priceCurrency\" content=\"RUB\"></i>\r\n                    </span>\r\n                  </div>\r\n                </div>\r\n                <div class=\"catalog-article__available\">\r\n                  <link itemprop=\"availability\" href=\"http://schema.org/<?php echo $Item->available ? \'InStock\' : \'PreOrder\'?>\" />\r\n                  <?php echo $Item->available ? \'<span class=\"text-success\">\' . AVAILABLE . \'</span>\' : \'<span class=\"text-danger\">\' . AVAILABLE_CUSTOM . \'</span>\'?>\r\n                </div>\r\n              </div>\r\n              <!--noindex-->\r\n              <form action=\"/cart/\" class=\"catalog-article__controls\" data-role=\"add-to-cart-form\" data-id=\"<?php echo (int)$Item->id?>\" data-price=\"<?php echo (int)$Item->price?>\">\r\n                <?php if ($Item->available) { ?>\r\n                    <input type=\"hidden\" name=\"action\" value=\"add\" />\r\n                    <input type=\"hidden\" name=\"id\" value=\"<?php echo (int)$Item->id?>\" />\r\n                    <input type=\"hidden\" name=\"back\" value=\"1\" />\r\n                    <input type=\"number\" class=\"form-control\" autocomplete=\"off\" name=\"amount\" min=\"<?php echo (int)$Item->min ?: 1?>\" step=\"<?php echo (int)$Item->step ?: 1?>\" value=\"<?php echo (int)$Item->min ?: 1?>\" />\r\n                    <button type=\"submit\" class=\"btn btn-danger\"><?php echo TO_CART?></button>\r\n                    <?php /* <a href=\"/cart/?action=add&id=<?php echo (int)$Item->id?>\" class=\"btn btn-danger\" data-role=\"add-to-cart-trigger\" data-id=\"<?php echo (int)$Item->id?>\" data-price=\"<?php echo (int)$Item->price?>\" data-active-html=\"<?php echo DELETE_FROM_CART?>\"><?php echo TO_CART?></button> */ ?>\r\n                <?php } ?>\r\n                <a href=\"/favorites/?action=add&id=<?php echo (int)$Item->id?>\" class=\"btn btn-info\" data-role=\"add-to-favorites-trigger\" data-id=\"<?php echo (int)$Item->id?>\" data-active-html=\"<?php echo DELETE_FROM_FAVORITES?>\" rel=\"nofollow\"><?php echo TO_FAVORITES?></a>\r\n              </form>\r\n              <!--/noindex-->\r\n              <!--noindex-->\r\n              <div class=\"share\">\r\n                <script type=\"text/javascript\" src=\"//yastatic.net/share/share.js\" charset=\"utf-8\"></script>\r\n                <?php echo SHARE?>: <div class=\"yashare-auto-init\" style=\"display: inline-block; vertical-align: middle\" data-yashareL10n=\"ru\" data-yashareQuickServices=\"vkontakte,facebook,twitter,odnoklassniki,moimir\" data-yashareTheme=\"counter\"></div>\r\n              </div>\r\n              <!--/noindex-->\r\n              <?php\r\n              $propsText = \'\';\r\n              $brands = $models = array();\r\n              foreach ((array)$Item->model as $val) {\r\n                  $brands[$val->brand->id] = $val->brand->name;\r\n                  $models[$val->id] = $val->name;\r\n              }\r\n              unset($temp);\r\n              foreach ($Item->fields as $key => $val) {\r\n                  if (\r\n                      !in_array(\r\n                          $val->urn,\r\n                          array(\'images\', \'brief\', \'videos\', \'videos_url\', \'files\', \'onmain\', \'article\', \'price\', \'price_old\', \'available\', \'min\', \'step\')\r\n                      ) &&\r\n                      !in_array($val->datatype, array(\'image\', \'file\', \'material\', \'checkbox\'))\r\n                  ) {\r\n                      if ($val->doRich()) {\r\n                          $v = implode(\', \', array_map(function($x) use ($val) { return $val->doRich($x); }, $val->getValues(true)));\r\n                          switch ($key) {\r\n                              case \'width\': case \'height\':\r\n                                  $propsText .= \' <tr>\r\n                                                    <th>\' . htmlspecialchars($val->name) . \': </th>\r\n                                                    <td itemprop=\"<?php echo $key?>\" itemtype=\"http://schema.org/QuantitativeValue\">\r\n                                                      <span itemprop=\"value\">\' . $v . \'</span>\r\n                                                    </td>\r\n                                                  </tr>\';\r\n                                  break;\r\n                              case \'article\':\r\n                                  $propsText .= \' <tr>\r\n                                                    <th>\' . htmlspecialchars($val->name) . \': </th>\r\n                                                    <td itemprop=\"productID\">\' . $val[\'doRich\'] . \'</td>\r\n                                                  </tr>\';\r\n                                  break;\r\n                              case \'brand\':\r\n                                  $propsText .= \' <tr>\r\n                                                    <th>\' . htmlspecialchars($val->name) . \': </th>\r\n                                                    <td itemprop=\"brand\" itemscope itemtype=\"http://schema.org/Brand\">\r\n                                                      <span itemprop=\"name\">\' . $v . \'</span>\r\n                                                    </td>\r\n                                                  </tr>\';\r\n                                  break;\r\n                              default:\r\n                                  $propsText .= \' <tr itemprop=\"additionalProperty\" itemscope itemtype=\"http://schema.org/PropertyValue\">\r\n                                                    <th itemprop=\"name\">\' . htmlspecialchars($val->name) . \': </th>\r\n                                                    <td itemprop=\"value\">\' . $v . \'</td>\r\n                                                  </tr>\';\r\n                                  break;\r\n                          }\r\n                      }\r\n                  }\r\n              }\r\n              if ($propsText) {\r\n                  echo \'<div class=\"catalog-article__props\">\r\n                          <table class=\"table table-striped\"><tbody>\' . $propsText . \'</tbody></table>\r\n                        </div>\r\n                        <div class=\"clearfix\"></div>\';\r\n              }\r\n              ?>\r\n            </div>\r\n          </div>\r\n        </div>\r\n        <?php\r\n        $tabs = array();\r\n        foreach (array(\'description\', \'files\', \'videos\', \'reviews\', \'related\') as $key) {\r\n            $text = \'\';\r\n            $name = $Item->fields[$key]->name;\r\n            switch ($key) {\r\n                case \'description\':\r\n                    $name = DESCRIPTION;\r\n                    $text = \'<div itemprop=\"description\">\' . trim($Item->description) . \'</div>\';\r\n                    break;\r\n                case \'files\':\r\n                    if ($Item->files) {\r\n                        $text = \'<div class=\"catalog-article__files\">\';\r\n                        foreach ($Item->files as $file) {\r\n                            $text .= \'<div class=\"catalog-article__file\">\r\n                                        <a href=\"/\' . htmlspecialchars($file->fileURL) . \'\">\'\r\n                                  .  \'    <span class=\"fa \' . $getFileIcon($file) . \'\"></span> \'\r\n                                  .       htmlspecialchars($file->name ?: basename($file->fileURL))\r\n                                  . \'   </a>\r\n                                      </div>\';\r\n                        }\r\n                        $text .= \'</div>\';\r\n                    }\r\n                    break;\r\n                case \'videos\':\r\n                    if ($Item->videos) {\r\n                        $text .= \'<div class=\"catalog-article__videos\">\';\r\n                        for ($i = 0; $i < (count($Item->videos) / 4); $i++) {\r\n                            $text .= \'<div class=\"row\">\';\r\n                            for ($j = $i * 4; $j < ($i + 1) * 4; $j++) {\r\n                                if ($val = $Item->videos[$j]) {\r\n                                    $ytid = $ytname = \'\';\r\n                                    if (preg_match(\'/^(.*?)((http(s?):\\\\/\\\\/.*?(((\\\\?|&)v=)|(embed\\\\/)|(youtu\\\\.be\\\\/)))([\\\\w\\\\-\\\\_]+).*?)$/\', $val, $regs)) {\r\n                                        $ytname = trim($regs[1]);\r\n                                        $ytid = trim($regs[10]);\r\n                                    }\r\n                                    if (!$ytname) {\r\n                                        $url = \'https://www.googleapis.com/youtube/v3/videos?part=snippet&id=\' . $ytid . \'&key=AIzaSyCJgMFQqq6Ax9WlGhuslTz4viyG3RbPEic\';\r\n                                        $json = file_get_contents($url);\r\n                                        $json = json_decode($json, true);\r\n                                        if (isset($json[\'items\'][0][\'snippet\'][\'title\'])) {\r\n                                            $ytname = trim($json[\'items\'][0][\'snippet\'][\'title\']);\r\n                                        }\r\n                                    }\r\n                                    $text .= \'<div class=\"col-sm-3\">\r\n                                                <div class=\"catalog-article__video\">\r\n                                                  <a href=\"https://youtube.com/embed/\' . $ytid . \'\" data-lightbox-gallery=\"v\" title=\"\' . htmlspecialchars($ytname) . \'\">\r\n                                                    <img src=\"https://i.ytimg.com/vi/\' . htmlspecialchars($ytid) . \'/hqdefault.jpg\" alt=\"\' . htmlspecialchars($ytname) . \'\">\r\n                                                  </a>\r\n                                                </div>\r\n                                              </div>\';\r\n\r\n                                }\r\n                            }\r\n                            $text .= \'</div>\';\r\n                        }\r\n                        $text .= \'</div>\';\r\n                    }\r\n                    break;\r\n                case \'reviews\':\r\n                    $name = REVIEWS . ($comments ? \' (\' . count($comments) . \')\' : \'\');\r\n                    ob_start();\r\n                    if ($comments) {\r\n                        eval(\'?\' . \'>\' . Snippet::importByURN(\'goods_comments\')->description);\r\n                    }\r\n                    if ($commentFormBlock) {\r\n                        $commentFormBlock->process($Page);\r\n                    }\r\n                    $text .= ob_get_clean();\r\n                    break;\r\n                case \'related\':\r\n                    if ($Item->related) {\r\n                        $text .= \'<div class=\"row catalog-list catalog-list_related\">\';\r\n                        foreach ($Item->related as $row) {\r\n                            $text .= \'<div class=\"catalog-list__item\">\';\r\n                            ob_start();\r\n                            $showItem($row);\r\n                            $text .= ob_get_clean();\r\n                            $text .= \'</div>\';\r\n                        }\r\n                        $text .= \'</div>\';\r\n                    }\r\n                    break;\r\n            }\r\n            if ($text) {\r\n                $tabs[$key] = array(\'name\' => $name, \'description\' => $text);\r\n            }\r\n        }\r\n        if ($tabs) {\r\n            ?>\r\n            <ul class=\"nav nav-tabs\" role=\"tablist\">\r\n              <?php $i = 0; foreach ($tabs as $key => $row) { ?>\r\n                  <li<?php echo !$i ? \' class=\"active\"\' : \'\'?>>\r\n                    <a href=\"#<?php echo $key?>\" aria-controls=\"<?php echo $key?>\" role=\"tab\" data-toggle=\"tab\">\r\n                      <?php echo htmlspecialchars($row[\'name\'])?>\r\n                    </a>\r\n                  </li>\r\n              <?php $i++; } ?>\r\n            </ul>\r\n            <div class=\"tab-content\" style=\"padding: 15px 0;\">\r\n              <?php $i = 0; foreach ($tabs as $key => $row) { ?>\r\n                  <div class=\"tab-pane<?php echo !$i ? \' active\' : \'\'?>\" id=\"<?php echo $key?>\"><?php echo $row[\'description\']?></div>\r\n              <?php $i++; } ?>\r\n            </div>\r\n        <?php } ?>\r\n      </div>\r\n    </div>\r\n<?php } else { ?>\r\n    <div class=\"catalog\">\r\n      <?php if ($Page->pid) { ?>\r\n          <div class=\"catalog__filter\">\r\n            <?php eval(\'?\' . \'>\' . Snippet::importByURN(\'catalog_filter\')->description)?>\r\n          </div>\r\n          <?php\r\n      }\r\n      ?>\r\n      <div class=\"catalog__inner\">\r\n        <?php\r\n        if ($Set || $subCats) {\r\n            if ($subCats) {\r\n                ?>\r\n                <div class=\"catalog__categories-list\">\r\n                  <div class=\"catalog-categories-list\">\r\n                    <?php foreach ($subCats as $row) { ?>\r\n                        <div class=\"catalog-categories-list__item\">\r\n                          <?php $showCategory($row);?>\r\n                        </div>\r\n                    <?php } ?>\r\n                  </div>\r\n                </div>\r\n                <?php\r\n            }\r\n            if ($Set) {\r\n                ?>\r\n                <div class=\"catalog__list\">\r\n                  <div class=\"catalog-list\">\r\n                    <?php foreach ($Set as $row) { ?>\r\n                        <div class=\"catalog-list__item\">\r\n                          <?php $showItem($row)?>\r\n                        </div>\r\n                    <?php } ?>\r\n                  </div>\r\n                </div>\r\n            <?php } ?>\r\n        <?php } else { ?>\r\n            <p><?php echo NO_RESULTS_FOUND?></p>\r\n        <?php } ?>\r\n      </div>\r\n      <?php\r\n      if ($Set) {\r\n          include \\RAAS\\CMS\\Package::i()->resourcesDir . \'/pages.inc.php\';\r\n          if ($Pages->pages > 1) {\r\n              ?>\r\n              <div data-pages=\"<?php echo $Pages->pages?>\">\r\n                <ul class=\"pagination pull-right\">\r\n                  <?php\r\n                  echo $outputNav(\r\n                      $Pages,\r\n                      array(\r\n                          \'pattern\' => \'<li><a href=\"\' . \\SOME\\HTTP::queryString(\'page={link}\') . \'\">{text}</a></li>\',\r\n                          \'pattern_active\' => \'<li class=\"active\"><a>{text}</a></li>\',\r\n                          \'ellipse\' => \'<li class=\"disabled\"><a>...</a></li>\'\r\n                      )\r\n                  );\r\n                  ?>\r\n                </ul>\r\n              </div>\r\n              <div class=\"clearfix\"></div>\r\n          <?php } ?>\r\n      <?php } ?>\r\n    </div>\r\n<?php } ?>\r\n', 0),
(38, 2, 'catalog_filter', 'Фильтр каталога', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\n$mt = microtime(1);\r\n$DATA = $_GET;\r\n$MType = Material_Type::importByURN(\'catalog\');\r\n$cc = new Catalog_Cache($MType);\r\nif (!$cc->load()) {\r\n    $cc->getCache();\r\n    $cc->save();\r\n}\r\n$cc = $cc->data;\r\n$cc = array_filter($cc, function($x) use ($Page) { return array_intersect(array_merge(array($Page->id), (array)$Page->all_children_ids), (array)$x[\'pages_ids\']); });\r\n\r\n$filterProps = array(/*\'example\' => \'Пример\', */);\r\n$filterRaw = array();\r\n$filter = array();\r\n\r\n// $dictionaryExample = new Dictionary(1);\r\n// $dictionaryExample = $dictionaryExample->children;\r\n// foreach ($dictionaryExample as $row) {\r\n//     $filterRaw[\'example\'][] = array(\'value\' => $row->urn, \'name\' => $row->name);\r\n// }\r\n\r\n\r\nforeach ($filterRaw as $key => $arr) {\r\n    $filterSet = $cc;\r\n    foreach ($filterProps as $key2 => $name) {\r\n        if (($key2 != $key) && $DATA[$key2]) {\r\n            $filterSet = array_filter(\r\n                $filterSet,\r\n                function ($x) use ($key2, $DATA) {\r\n                    return $x[$key2] == $DATA[$key2];\r\n                }\r\n            );\r\n        }\r\n    }\r\n    $filterSet = array_map(\r\n        function ($x) use ($key) {\r\n            return $x[$key];\r\n        },\r\n        $filterSet\r\n    );\r\n    $filter[$key] = array_filter(\r\n        $arr,\r\n        function ($x) use ($filterSet) {\r\n            return in_array($x[\'value\'], $filterSet);\r\n        }\r\n    );\r\n}\r\n\r\n$maxPrice = array_map(\r\n    function ($x) {\r\n        return (int)$x[\'price\'];\r\n    },\r\n    $cc\r\n);\r\n$maxPrice = max($maxPrice);\r\n$priceStep = 1000;\r\n$maxPrice = ceil($maxPrice / (int)$priceStep) * (int)$priceStep;\r\nif ($DATA[\'price_to\'] && $DATA[\'price_to\'] > $maxPrice) {\r\n    $DATA[\'price_to\'] = $maxPrice;\r\n}\r\n$price1 = floor($DATA[\'price_from\'] / (int)$priceStep) * (int)$priceStep;\r\n$price2 = ceil(($DATA[\'price_to\'] ?: $maxPrice) / (int)$priceStep) * (int)$priceStep;\r\n\r\n// echo microtime(1) - $mt;\r\n?>\r\n<!--noindex-->\r\n<div class=\"catalog-filter\">\r\n  <form action=\"\" method=\"get\" data-page-id=\"<?php echo (int)$Page->id?>\">\r\n    <div class=\"row\">\r\n      <div class=\"col-sm-3\">\r\n        <div><label><?php echo ARTICLE?></label></div>\r\n        <div class=\"catalog-filter__property\">\r\n          <input type=\"text\" class=\"form-control\" name=\"article\" placeholder=\"Артикул\" value=\"<?php echo htmlspecialchars($_GET[\'article\'])?>\">\r\n        </div>\r\n      </div>\r\n      <div class=\"col-sm-4\">\r\n        <div><label><?php echo PRICE?></label></div>\r\n        <div class=\"catalog-filter__property catalog-filter__price\">\r\n          <div class=\"row\">\r\n            <div class=\"col-xs-1\">от</div>\r\n            <div class=\"col-xs-5\"><input type=\"number\" class=\"form-control\" min=\"0\" max=\"<?php echo (int)$maxPrice?>\" step=\"<?php echo (int)$priceStep?>\" name=\"price_from\" placeholder=\"От\" value=\"<?php echo htmlspecialchars($price1)?>\"></div>\r\n            <div class=\"col-xs-1\">до</div>\r\n            <div class=\"col-xs-5\"><input type=\"number\" class=\"form-control\" min=\"0\" max=\"<?php echo (int)$maxPrice?>\" step=\"<?php echo (int)$priceStep?>\" name=\"price_to\" placeholder=\"До\" value=\"<?php echo htmlspecialchars($price2)?>\"></div>\r\n          </div>\r\n          <div class=\"catalog-filter__slider\" id=\"catalog-filter__slider\"></div>\r\n        </div>\r\n      </div>\r\n      <div class=\"col-sm-3\">\r\n        <div class=\"hidden-xs\"><label>&nbsp;</label></div>\r\n        <div class=\"catalog-filter__property catalog-filter__property_search\">\r\n          <button type=\"submit\" class=\"btn btn-primary\"><?php echo DO_SEARCH?></button>\r\n          <a href=\"<?php echo htmlspecialchars($Page->url)?>\" class=\"btn btn-default\"><?php echo RESET?></a>\r\n        </div>\r\n      </div>\r\n    </div>\r\n    <div class=\"row\">\r\n      <div class=\"col-sm-3\"></div>\r\n      <div class=\"col-sm-4\"></div>\r\n      <div class=\"col-sm-1\"></div>\r\n    </div>\r\n  </form>\r\n  <script src=\"/js/catalog_filter.js\"></script>\r\n</div>\r\n<!--/noindex-->\r\n', 0),
(39, 2, 'cart_main', 'Информер корзины', '<?php\r\nnamespace RAAS\\CMS\\Shop;\r\n\r\n?>\r\n<!--noindex-->\r\n<a href=\"/cart/\" data-role=\"cart-block\" class=\"cart-main\" style=\"display: none\" rel=\"nofollow\">\r\n  <?php echo IN_YOUR_CART?><br />\r\n  <strong data-role=\"cart-block-amount\"></strong><br />\r\n  <?php echo TO_AMOUNT_OF?> <strong data-role=\"cart-block-sum\"></strong> <i class=\"fa fa-rub\"></i>\r\n</a>\r\n<!--/noindex-->\r\n', 0),
(40, 2, 'favorites_main', 'Информер избранного', '<?php\r\nnamespace RAAS\\CMS\\Shop;\r\n\r\n?>\r\n<!--noindex-->\r\n<a href=\"/favorites/\" data-role=\"favorites-block\" class=\"favorites-main\" style=\"display: none\" rel=\"nofollow\">\r\n  <?php echo IN_FAVORITES?><br />\r\n  <strong data-role=\"favorites-block-amount\"></strong>\r\n</a>\r\n<!--/noindex-->\r\n', 0),
(41, 2, 'menu_left', 'Левое меню', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse SOME\\HTTP;\r\n\r\n$showMenu = function($node, Page $current) use (&$showMenu) {\r\n    static $level = 0;\r\n    if ($node instanceof Menu) {\r\n        $children = $node->visSubMenu;\r\n    } else {\r\n        $children = (isset($node[\'children\']) && is_array($node[\'children\'])) ? $node[\'children\'] : array();\r\n    }\r\n    for ($i = 0; $i < count($children); $i++) {\r\n        $row = $children[$i];\r\n        $level++;\r\n        $ch = $showMenu($row, $current);\r\n        $level--;\r\n        if ($node instanceof Menu) {\r\n            $url = $row->url;\r\n            $name = $row->name;\r\n        } else {\r\n            $url = $row[\'url\'];\r\n            $name = $row[\'name\'];\r\n        }\r\n        $active = ($url == HTTP::queryString(\'\', true));\r\n        $semiactive = preg_match(\'/^\' . preg_quote($url, \'/\') . \'/umi\', HTTP::queryString(\'\', true)) && ($url != \'/\') && !$active;\r\n        if (preg_match(\'/class=\"[\\\\w\\\\- ]*?active[\\\\w\\\\- ]*?\"/umi\', $ch)) {\r\n            $semiactive = true;\r\n        }\r\n        $liClasses = array(\r\n            \'menu-left__item\',\r\n            \'menu-left__item_\' . (!$level ? \'main\' : \'inner\'),\r\n            \'menu-left__item_level_\' . $level\r\n        );\r\n        $aClasses = array(\r\n            \'menu-left__link\',\r\n            \'menu-left__link_\' . (!$level ? \'main\' : \'inner\'),\r\n            \'menu-left__link_level_\' . $level\r\n        );\r\n        if ($active) {\r\n            $liClasses[] = \'menu-left__item_active\';\r\n            $aClasses[] = \'menu-left__link_active\';\r\n        } elseif ($semiactive) {\r\n            $liClasses[] = \'menu-left__item_semiactive\';\r\n            $aClasses[] = \'menu-left__link_semiactive\';\r\n        }\r\n        $text .= \'<li class=\"\' . implode(\' \', $liClasses) . \'\">\'\r\n              .  \'  <a class=\"\' . implode(\' \', $aClasses) . \'\" \' . ($active ? \'\' : \' href=\"\' . htmlspecialchars($url) . \'\"\') . \'>\' . htmlspecialchars($name) . \'</a>\'\r\n              .     $ch\r\n              .  \'</li>\';\r\n    }\r\n    $ulClasses = array(\r\n        \'menu-left__list\',\r\n        \'menu-left__list_\' . (!$level ? \'main\' : \'inner\'),\r\n        \'menu-left__list_level_\' . $level\r\n    );\r\n    return $text ? \'<ul class=\"\' . implode(\' \', $ulClasses) . \'\">\' . $text . \'</ul>\' : $text;\r\n};\r\n?>\r\n<div class=\"menu-left__outer left-block\">\r\n  <div class=\"menu-left__title left-block__title\">\r\n    <a href=\"/catalog/\"><?php echo CATALOG?></a>\r\n  </div>\r\n  <nav class=\"menu-left\"><?php echo $showMenu($menuArr ?: $Item, $Page)?></nav>\r\n</div>\r\n', 0),
(42, 2, 'file_inc', 'Блок файла', '<?php\r\nnamespace RAAS\\CMS\\Shop;\r\n\r\n$getFileIcon = function($row)\r\n{\r\n    $ext = pathinfo($row->fileURL, PATHINFO_EXTENSION);\r\n    switch ($ext) {\r\n        case \'zip\': case \'rar\': case \'tar\': case \'gz\': case \'7z\':\r\n            $icon = \'archive\';\r\n            break;\r\n        case \'mp3\': case \'wav\': case \'ogg\': case \'mid\': case \'rmi\':\r\n            $icon = \'audio\';\r\n            break;\r\n        case \'php\': case \'html\': case \'cs\': case \'class\': case \'inc\': case \'js\': case \'css\':\r\n            $icon = \'code\';\r\n            break;\r\n        case \'jpg\': case \'gif\': case \'jpeg\': case \'pjpeg\': case \'bmp\': case \'tif\': case \'tiff\':\r\n            $icon = \'picture\';\r\n            break;\r\n        case \'pdf\':\r\n            $icon = \'pdf\';\r\n            break;\r\n        case \'ppt\': case \'pptx\':\r\n            $icon = \'powerpoint\';\r\n            break;\r\n        case \'avi\': case \'mpg\': case \'flv\':\r\n            $icon = \'video\';\r\n            break;\r\n        case \'doc\': case \'docx\': case \'rft\':\r\n            $icon = \'word\';\r\n            break;\r\n        default:\r\n            $icon = \'text\';\r\n            break;\r\n    }\r\n    return \'fa-file-\' . htmlspecialchars($icon) . \'-o\';\r\n};\r\n', 0),
(43, 2, 'spec', 'Спецпредложение', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\n$MType = Material_Type::importByURN(\'catalog\');\r\n$Field = $MType->fields[\'spec\'];\r\n$SQL_query = \"SELECT tM.* FROM \" . Material::_tablename() . \" AS tM\r\n                JOIN cms_data AS tD ON tD.pid = tM.id\r\n               WHERE tD.fid = \" . (int)$Field->id . \" AND tM.pid IN (\" . implode(\", \", array_merge(array((int)$MType->id), (array)$MType->all_children_ids)) . \")\r\n            GROUP BY tM.id\r\n            ORDER BY RAND()\r\n               LIMIT 20\";\r\n$Set = Material::getSQLSet($SQL_query);\r\neval(\'?\' . \'>\' . Snippet::importByURN(\'item_inc\')->description);\r\n?>\r\n<?php if ($Set) { ?>\r\n    <div class=\"spec\">\r\n      <div class=\"spec__list\" data-role=\"slider\" data-slider-carousel=\"jcarousel\" data-slider-duration=\"800\" data-slider-interval=\"3000\" data-slider-autoscroll=\"true\">\r\n        <div class=\"spec-list\">\r\n          <?php foreach ((array)$Set as $row) { ?>\r\n              <div class=\"spec-list__item\">\r\n                <?php $showItem($row); ?>\r\n              </div>\r\n          <?php } ?>\r\n        </div>\r\n      </div>\r\n      <a href=\"#\" class=\"spec__arrow spec__arrow_left\" data-role=\"slider-prev\"></a>\r\n      <a href=\"#\" class=\"spec__arrow spec__arrow_right\" data-role=\"slider-next\"></a>\r\n    </div>\r\n<?php } ?>\r\n', 0);
INSERT INTO `cms_snippets` (`id`, `pid`, `urn`, `name`, `description`, `locked`) VALUES
(44, 2, 'my_orders', 'Мои заказы', '<?php\r\nnamespace RAAS\\CMS\\Shop;\r\n\r\nfunction formatPrice($price)\r\n{\r\n    $remainder = (float)$price - (float)(int)$price;\r\n    return str_replace(\' \', \'&nbsp;\', number_format((float)$price, ($remainder > 0) ? 2 : 0, \',\', \' \' ));\r\n}\r\n\r\n$getField = function($row) {\r\n    $arr = array();\r\n    $val = $row->doRich();\r\n    switch ($row->datatype) {\r\n        case \'date\':\r\n            $arr[$key] = date(DATEFORMAT, strtotime($val));\r\n            break;\r\n        case \'datetime-local\':\r\n            $arr[$key] = date(DATETIMEFORMAT, strtotime($val));\r\n            break;\r\n        case \'color\':\r\n            $arr[$key] = \'<span style=\"display: inline-block; height: 16px; width: 16px; background-color: \' . htmlspecialchars($val) . \'\"></span>\';\r\n            break;\r\n        case \'email\':\r\n            $arr[$key] .= \'<a href=\"mailto:\' . htmlspecialchars($val) . \'\">\' . htmlspecialchars($val) . \'</a>\';\r\n            break;\r\n        case \'url\':\r\n            $arr[$key] .= \'<a href=\"\' . (!preg_match(\'/^http(s)?:\\\\/\\\\//umi\', trim($val)) ? \'http://\' : \'\') . htmlspecialchars($val) . \'\">\' . htmlspecialchars($val) . \'</a>\';\r\n            break;\r\n        case \'file\':\r\n            $arr[$key] .= \'<a href=\"/\' . $val->fileURL . \'\">\' . htmlspecialchars($val->name) . \'</a>\';\r\n            break;\r\n        case \'image\':\r\n            $arr[$key] .= \'<a href=\"/\' . $val->fileURL . \'\"><img src=\"/\' . $val->tnURL. \'\" alt=\"\' . htmlspecialchars($val->name) . \'\" title=\"\' . htmlspecialchars($val->name) . \'\" /></a>\';\r\n            break;\r\n        case \'htmlarea\':\r\n            $arr[$key] = \'<div>\' . $val . \'</div>\';\r\n            break;\r\n        default:\r\n            if (!$row->multiple && ($row->datatype == \'checkbox\')) {\r\n                $arr[$key] = $val ? _YES : _NO;\r\n            } else {\r\n                $arr[$key] = nl2br(htmlspecialchars($val));\r\n            }\r\n            break;\r\n    }\r\n    return implode(\', \', $arr);\r\n};\r\n\r\n?>\r\n<div class=\"my-orders\">\r\n  <?php if ($Item) { ?>\r\n      <?php if (!$Item->status_id && !$Item->paid && !$Item->vis) { ?>\r\n          <p class=\"text-right\">\r\n            <a href=\"#\" data-id=\"<?php echo (int)$Item->id?>\" data-toggle=\"modal\" data-target=\"#confirmDeleteOrderModal\"><span class=\"fa fa-close\"></span> Удалить заказ</a>\r\n          </p>\r\n      <?php } ?>\r\n      <div class=\"form-horizontal\">\r\n        <div data-role=\"feedback-form\">\r\n          <div class=\"form-group\">\r\n            <label class=\"control-label col-sm-3 col-md-2\" style=\"padding-top: 0\"><?php echo STATUS?>:</label>\r\n            <div class=\"col-sm-9 col-md-4\"><?php echo $Item->paid ? PAYMENT_PAID : PAYMENT_NOT_PAID?></div>\r\n          </div>\r\n          <?php\r\n          foreach ($Item->fields as $row) {\r\n              if ($val = $getField($row)) {\r\n                  ?>\r\n                  <div class=\"form-group\">\r\n                    <label class=\"control-label col-sm-3 col-md-2\" style=\"padding-top: 0\"><?php echo htmlspecialchars($row->name)?>:</label>\r\n                    <div class=\"col-sm-9 col-md-4\"><?php echo $val?></div>\r\n                  </div>\r\n                  <?php\r\n              }\r\n          }\r\n          ?>\r\n        </div>\r\n      </div>\r\n      <p>&nbsp;</p>\r\n      <table class=\"table table-striped cart-table\" data-role=\"cart-table\">\r\n        <tbody>\r\n          <?php $sum = $am = 0; foreach ($Item->items as $row) {?>\r\n            <tr data-role=\"cart-item\">\r\n              <td class=\"cart-table__image-col\">\r\n                <?php if ($row->visImages) { ?>\r\n                    <a <?php echo $row->url ? \'href=\"\' . htmlspecialchars($row->url) . \'\"\' : \'\'?>>\r\n                      <img src=\"/<?php echo htmlspecialchars(addslashes($row->visImages[0]->tnURL))?>\" alt=\"<?php echo htmlspecialchars($row->visImages[0]->name ?: $row->name)?>\" /></a>\r\n                <?php } ?>\r\n              </td>\r\n              <td class=\"cart-table__name-col\">\r\n                <h3><a <?php echo $row->url ? \'href=\"\' . htmlspecialchars($row->url) . \'\"\' : \'\'?>><?php echo htmlspecialchars($row->name)?></a></h3>\r\n                <?php if ($Cart->cartType->no_amount) { ?>\r\n                    <input type=\"hidden\" name=\"amount[<?php echo htmlspecialchars((int)$row->id . \'_\' . $row->meta)?>]\" value=\"<?php echo (int)$row->amount?>\" />\r\n                <?php } else { ?>\r\n                    <p><?php echo AMOUNT . \': \' . (int)$row->amount?></p>\r\n                    <?php\r\n                }\r\n                foreach ($row->fields as $f) {\r\n                    if ($val = array_filter(array_map(array($f, \'doRich\'), $f->getValues(true)))) {\r\n                        ?>\r\n                        <p>\r\n                          <?php echo htmlspecialchars($f->name)?>:\r\n                          <?php\r\n                          if (in_array($key, array(\'metal\'))) {\r\n                              echo htmlspecialchars(mb_strtolower(implode(\', \', $val)));\r\n                          } else {\r\n                              echo htmlspecialchars(implode(\', \', $val));\r\n                          }\r\n                          ?>\r\n                        </p>\r\n                        <?php\r\n                    }\r\n                }\r\n                ?>\r\n              </td>\r\n              <td class=\"cart-table__sum-col\"><span data-role=\"sum\"><?php echo formatPrice($row->amount * $row->realprice)?></span> <span class=\"fa fa-rub\"></span></td>\r\n            </tr>\r\n          <?php $sum += $row->amount * $row->realprice; $am += $row->amount; } ?>\r\n          <tr>\r\n            <th class=\"cart-table__image-col\"></th>\r\n            <th class=\"cart-table__name-col\"><?php echo TOTAL_SUM?>:</th>\r\n            <th class=\"cart-table__sum-col\"><span data-role=\"total-sum\"><?php echo formatPrice($sum)?></span>&nbsp;<span class=\"fa fa-rub\"></span></th>\r\n          </tr>\r\n        </tbody>\r\n      </table>\r\n\r\n  <?php } elseif ($Set) { ?>\r\n      <table class=\"table table-striped my-orders__table\">\r\n        <tbody>\r\n          <?php foreach ($Set as $row) { ?>\r\n              <tr>\r\n                <td><a href=\"<?php echo $row->urn?>/\"><?php echo Lang::i()->_(\'ORDER_NUMBER\', $Page)?> <?php echo (int)$row->id?></td>\r\n                <td><a href=\"<?php echo $row->urn?>/\"><?php echo date(Lang::i()->_(\'DATETIME_FORMAT\', $Page), strtotime($row->post_date))?></a></td>\r\n                <td>\r\n                  <a href=\"<?php echo $row->urn?>/\">\r\n                  <?php\r\n                  $temp = array();\r\n                  foreach ($row->items as $row2) {\r\n                      $arr = $row2->name;\r\n                      if ($row2->amount > 1) {\r\n                          $arr .= \' - \' . (int)$row2->amount . \'x\' . formatPrice($row2->realprice) . \'<span class=\"fa fa-rub\"></span> = \' . formatPrice($row2->amount * $row2->realprice) . \' <span class=\"fa fa-rub\"></span>\';\r\n                      } else {\r\n                          $arr .= \' = \' . formatPrice($row2->realprice) . \' <span class=\"fa fa-rub\"></span>\';\r\n                      }\r\n                      $temp[] = $arr;\r\n                  }\r\n                  echo implode(\'<br />\', $temp);\r\n                  ?>\r\n                  </a>\r\n                </td>\r\n                <td>\r\n                  <?php\r\n                  $temp = array();\r\n                  if ($row->status->id) {\r\n                      $temp[] = $row->status->name;\r\n                  } else {\r\n                      // $temp[] = ORDER_STATUS_NEW;\r\n                  }\r\n                  if ($row->paid) {\r\n                      $temp[] = PAYMENT_PAID;\r\n                  } else {\r\n                      $temp[] = PAYMENT_NOT_PAID;\r\n                  }\r\n                  echo implode(\'<br />\', $temp);\r\n                  ?>\r\n                </td>\r\n                <td class=\"my-orders__sum\">\r\n                  <a href=\"<?php echo $row->urn?>/\"><?php echo formatPrice($row->sum)?> <span class=\"fa fa-rub\"></span></a>\r\n                </td>\r\n                <td>\r\n                  <?php if (!$row->status_id && !$row->paid && !$row->vis) { ?>\r\n                      <a href=\"#\" data-id=\"<?php echo (int)$row->id?>\" data-back=\"true\" data-toggle=\"modal\" data-target=\"#confirmDeleteOrderModal\"><span class=\"fa fa-close\"></span></a>\r\n                  <?php } ?>\r\n                </td>\r\n              </tr>\r\n          <?php } ?>\r\n        </tbody>\r\n      </table>\r\n  <?php } else { ?>\r\n    <p><?php echo Lang::i()->_(\'YOU_HAVE_NO_ORDERS_YET\', $Page)?></p>\r\n  <?php } ?>\r\n</div>\r\n\r\n<div class=\"modal fade\" id=\"confirmDeleteOrderModal\" tabindex=\"-1\" role=\"dialog\">\r\n  <div class=\"modal-dialog\" role=\"document\">\r\n    <div class=\"modal-content\">\r\n      <div class=\"modal-header\" style=\"border-bottom: none\">\r\n        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\r\n        <h4 class=\"modal-title\"><?php echo Lang::i()->_(\'ARE_YOU_SURE_TO_DELETE_THIS_ORDER\', $Page)?></h4>\r\n      </div>\r\n      <div class=\"modal-footer\">\r\n        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\"><?php echo CANCEL?></button>\r\n        <a href=\"#\" class=\"btn btn-primary\"><?php echo DELETE?></a>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n\r\n<script>\r\njQuery(document).ready(function($) {\r\n    var $confirmDeleteOrderModal = $(\'#confirmDeleteOrderModal\');\r\n    $(\'body\').append(\'confirmDeleteOrderModal\');\r\n    $(\'a[data-target=\"#confirmDeleteOrderModal\"][data-toggle=\"modal\"]\').on(\'click\', function() {\r\n        $(\'.modal-footer a\').attr(\'href\', \'?action=delete&id=\' + parseInt($(this).attr(\'data-id\')) + ($(this).attr(\'data-back\') ? \'&back=1\' : \'\'));\r\n    });\r\n});\r\n</script>\r\n', 0),
(45, 1, '__raas_users_register_interface', 'Стандартный интерфейс регистрации', '<?php\n/**\n * Стандартный интерфейс регистрации\n * @param Block_Register $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS\\Users;\n\n$interface = new RegisterInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(46, 1, '__raas_users_activation_interface', 'Стандартный интерфейс активации', '<?php\n/**\n * Стандартный интерфейс активации учетной записи\n * @param Block_Activation $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS\\Users;\n\n$interface = new ActivationInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(47, 1, '__raas_users_login_interface', 'Стандартный интерфейс входа в систему', '<?php\n/**\n * Стандартный интерфейс входа в систему\n * @param Block_LogIn $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS\\Users;\n\n$interface = new LogInInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(48, 1, '__raas_users_recovery_interface', 'Стандартный интерфейс восстановления пароля', '<?php\n/**\n * Стандартный интерфейс восстановления пароля\n * @param Block_Recovery $Block Текущий блок\n * @param Page $Page Текущая страница\n */\nnamespace RAAS\\CMS\\Users;\n\n$interface = new RecoveryInterface(\n    $Block,\n    $Page,\n    $_GET,\n    $_POST,\n    $_COOKIE,\n    $_SESSION,\n    $_SERVER,\n    $_FILES\n);\nreturn $interface->process();\n', 1),
(49, 1, '__raas_users_register_notify', 'Стандартное уведомление о регистрации', '<?php\nnamespace RAAS\\CMS;\n\nuse RAAS\\CMS\\Users\\Block_Register;\nuse RAAS\\CMS\\Users\\Block_Activation;\n\n$smsField = function ($field) {\n    $values = $field->getValues(true);\n    $arr = array();\n    foreach ($values as $key => $val) {\n        $val = $field->doRich($val);\n        switch ($field->datatype) {\n            case \'date\':\n                $arr[$key] = date(DATEFORMAT, strtotime($val));\n                break;\n            case \'datetime-local\':\n                $arr[$key] = date(DATETIMEFORMAT, strtotime($val));\n                break;\n            case \'file\':\n            case \'image\':\n                $arr[$key] .= $val->name;\n                break;\n            case \'htmlarea\':\n                $arr[$key] = strip_tags($val);\n                break;\n            default:\n                if (!$field->multiple && ($field->datatype == \'checkbox\')) {\n                    $arr[$key] = $val ? _YES : _NO;\n                } else {\n                    $arr[$key] = $val;\n                }\n                break;\n        }\n    }\n    return $field->name . \': \' . implode(\', \', $arr) . \"\\n\";\n};\n\n\n$emailField = function ($field, $formField = null) {\n    if (!$formField) {\n        $formField = $field;\n    }\n    $values = $field->getValues(true);\n    $arr = array();\n    foreach ($values as $key => $val) {\n        $val = $formField->doRich($val);\n        switch ($field->datatype) {\n            case \'date\':\n                $arr[$key] = date(DATEFORMAT, strtotime($val));\n                break;\n            case \'datetime-local\':\n                $arr[$key] = date(DATETIMEFORMAT, strtotime($val));\n                break;\n            case \'color\':\n                $arr[$key] = \'<span style=\"display: inline-block; height: 16px; width: 16px; background-color: \' . htmlspecialchars($val) . \'\"></span>\';\n                break;\n            case \'email\':\n                $arr[$key] .= \'<a href=\"mailto:\' . htmlspecialchars($val) . \'\">\' . htmlspecialchars($val) . \'</a>\';\n                break;\n            case \'url\':\n                $arr[$key] .= \'<a href=\"http://\' . (!preg_match(\'/^http(s)?:\\\\/\\\\//umi\', trim($val)) ? \'http://\' : \'\') . htmlspecialchars($val) . \'\">\' . htmlspecialchars($val) . \'</a>\';\n                break;\n            case \'file\':\n                $arr[$key] .= \'<a href=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . \'/\' . $val->fileURL . \'\">\' . htmlspecialchars($val->name) . \'</a>\';\n                break;\n            case \'image\':\n                $arr[$key] .= \'<a href=\"http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . \'/\' . $val->fileURL . \'\">\n                                 <img src=\"http://\' . $_SERVER[\'HTTP_HOST\'] . \'/\' . $val->tnURL. \'\" alt=\"\' . htmlspecialchars($val->name) . \'\" title=\"\' . htmlspecialchars($val->name) . \'\" />\n                               </a>\';\n                break;\n            case \'htmlarea\':\n                $arr[$key] = \'<div>\' . $val . \'</div>\';\n                break;\n            default:\n                if (!$field->multiple && ($field->datatype == \'checkbox\')) {\n                    $arr[$key] = $val ? _YES : _NO;\n                } else {\n                    $arr[$key] = nl2br(htmlspecialchars($val));\n                }\n                break;\n        }\n    }\n    return \'<div>\' . htmlspecialchars($formField->name) . \': \' . implode(\', \', $arr) . \'</div>\';\n};\n?>\n<p><?php echo sprintf($ADMIN ? NEW_USER_REGISTERED_ON_SITE : YOU_HAVE_SUCCESSFULLY_REGISTERED_ON_WEBSITE, $_SERVER[\'HTTP_HOST\'], $_SERVER[\'HTTP_HOST\'])?></p>\n<?php if ($SMS) {\n    foreach ($Form->fields as $field) {\n        if (in_array($field->urn, array(\'login\', \'email\'))) {\n            echo $field->name . \': \' . $User->{$field->urn} . \"\\n\";\n        } elseif (isset($User->fields[$field->urn]) && ($field = $User->fields[$field->urn])) {\n            echo $smsField($field);\n        }\n    }\n} else { ?>\n    <div>\n      <?php\n      foreach ($Form->fields as $field) {\n          if ($field->urn == \'login\') {\n              echo \'<div>\' . htmlspecialchars($field->name) . \': \' . htmlspecialchars($User->{$field->urn}) . \'</div>\';\n          } elseif ($field->urn == \'email\') {\n              echo \'<div>\' . htmlspecialchars($field->name) . \': <a href=\"mailto:\' . htmlspecialchars($User->{$field->urn}) . \'\">\' . htmlspecialchars($User->{$field->urn}) . \'</a></div>\';\n          } elseif (!$ADMIN && ($field->urn == \'password\')) {\n              echo \'<div>\' . htmlspecialchars($field->name) . \': \' . htmlspecialchars($User->{$field->urn}) . \'</div>\';\n          } elseif (isset($User->fields[$field->urn]) && ($formField = $field) && ($field = $User->fields[$field->urn])) {\n              // 2017-03-28, AVS: добавил поле формы, для случая языковых версий - чтобы можно было отправлять письма на разных языках\n              echo $emailField($field, $formField);\n          }\n      }\n      ?>\n    </div>\n    <?php if ($ADMIN) { ?>\n        <?php if ($User && $User->id) { ?>\n            <p>\n              <a href=\"http<?php echo ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\')?>://<?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'] . \'/admin/?p=cms&m=users&action=edit&id=\' . (int)$User->id)?>\">\n                <?php echo VIEW?>\n              </a>\n            </p>\n        <?php } ?>\n        <p>\n          <small>\n            <?php echo IP_ADDRESS?>: <?php echo htmlspecialchars($User->ip)?><br />\n            <?php echo USER_AGENT?>: <?php echo htmlspecialchars($User->user_agent)?><br />\n            <?php echo PAGE?>:\n            <?php if ($User->page->parents) { ?>\n                <?php foreach ($User->page->parents as $row) { ?>\n                    <a href=\"<?php echo htmlspecialchars($User->domain . $row->url)?>\"><?php echo htmlspecialchars($row->name)?></a> /\n                <?php } ?>\n            <?php } ?>\n            <a href=\"<?php echo htmlspecialchars($User->domain . $User->page->url)?>\"><?php echo htmlspecialchars($User->page->name)?></a>\n          </small>\n        </p>\n        <?php\n    } else {\n        switch ($config[\'activation_type\']) {\n            case Block_Register::ACTIVATION_TYPE_ALREADY_ACTIVATED:\n                echo \'<p>\' . NOW_YOU_CAN_LOG_IN_INTO_THE_SYSTEM . \'</p>\';\n                break;\n            case Block_Register::ACTIVATION_TYPE_ADMINISTRATOR:\n                echo \'<p>\' . PLEASE_WAIT_FOR_ADMINISTRATOR_TO_ACTIVATE . \'</p>\';\n                break;\n            case Block_Register::ACTIVATION_TYPE_USER:\n                $activationBlocks = Block_Activation::getSet(array(\n                    \'where\' => \"block_type = \'RAAS\\\\\\\\CMS\\\\\\\\Users\\\\\\\\Block_Activation\'\",\n                    \'orderBy\' => \'id\'\n                ));\n                $activationPages = array();\n                if ($activationBlocks) {\n                    $activationPages = array();\n                    foreach ($activationBlocks as $activationBlock) {\n                        $activationPages = array_merge($activationPages, $activationBlock->pages);\n                    }\n                }\n                $p = $Page->parent;\n                $activationPage = null;\n                while ($p->id) {\n                    $nearestActivationPages = array_filter($activationPages, function ($x) use ($p) {\n                        return $x->pid == $p->id;\n                    });\n                    if ($nearestActivationPages) {\n                        $activationPage = array_shift($nearestActivationPages);\n                        break;\n                    }\n                    $p = $p->parent;\n                }\n                if (!$activationPage->id && $activationPages) {\n                    $activationPage = array_shift($activationPages);\n                }\n                if ($activationPage->id) {\n                    $link = \'http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . $activationPage->url . \'?key=\' . $User->activationKey;\n                    echo \'<p>\' . sprintf(ACTIVATION_LINK, $link, $link) . \'</p>\';\n                }\n                break;\n        }\n        ?>\n        <p>--</p>\n        <p>\n          <?php echo WITH_RESPECT?>,<br />\n          <?php echo ADMINISTRATION_OF_SITE?> <a href=\"http<?php echo ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\')?>://<?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'])?>\"><?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'])?></a>\n        </p>\n        <?php\n    }\n}\n?>\n', 1),
(50, 1, '__raas_users_recovery_notify', 'Стандартное уведомление о восстановлении пароля', '<?php\nnamespace RAAS\\CMS;\n\n$link = \'http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . $Page->url . \'?key=\' . $User->recoveryKey;\n?>\n<p><?php echo date(DATETIMEFORMAT) . \' \' . sprintf(YOU_HAVE_ASKED_PASSWORD_RECOVERY_ON_SITE, $_SERVER[\'HTTP_HOST\'], $_SERVER[\'HTTP_HOST\'])?></p>\n<?php\n\n?>\n<p><?php echo sprintf(RECOVERY_LINK, $link, $link)?></p>\n<p><?php echo IF_IT_WAS_NOT_YOU_JUST_IGNORE?></p>\n<p>--</p>\n<p>\n  <?php echo WITH_RESPECT?>,<br />\n  <?php echo ADMINISTRATION_OF_SITE?> <a href=\"http<?php echo ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\')?>://<?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'])?>\"><?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'])?></a>\n</p>\n', 1),
(51, 2, 'register', 'Регистрация', '<?php\r\nnamespace RAAS\\CMS\\Users;\r\n\r\nuse RAAS\\CMS\\Feedback;\r\nuse RAAS\\CMS\\SocialProfile;\r\n\r\nif ($_POST[\'AJAX\']) {\r\n    $result = array();\r\n    if ($success[(int)$Block->id]) {\r\n        $result[\'success\'] = 1;\r\n    }\r\n    if ($localError) {\r\n        $result[\'localError\'] = $localError;\r\n    }\r\n    if ($social) {\r\n        $result[\'social\'] = trim($social);\r\n    }\r\n    if ($social) {\r\n        $result[\'socialNetwork\'] = trim($socialNetwork);\r\n    }\r\n    ob_clean();\r\n    echo json_encode($result);\r\n    exit;\r\n} else { ?>\r\n    <a name=\"feedback\"></a>\r\n    <div class=\"feedback\">\r\n      <form class=\"form-horizontal\" <?php /*data-role=\"raas-ajaxform\"*/?> action=\"\" method=\"post\" enctype=\"multipart/form-data\">\r\n        <?php include \\RAAS\\CMS\\Package::i()->resourcesDir . \'/form2.inc.php\'?>\r\n        <div data-role=\"notifications\" <?php echo ($success[(int)$Block->id] || $localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n          <div class=\"alert alert-success\" <?php echo ($success[(int)$Block->id]) ? \'\' : \'style=\"display: none\"\'?>>\r\n            <?php\r\n            echo YOU_HAVE_SUCCESSFULLY_REGISTERED . \' \';\r\n            switch ($config[\'activation_type\']) {\r\n                case Block_Register::ACTIVATION_TYPE_ALREADY_ACTIVATED:\r\n                    echo NOW_YOU_CAN_LOG_IN_INTO_THE_SYSTEM;\r\n                    break;\r\n                case Block_Register::ACTIVATION_TYPE_ADMINISTRATOR:\r\n                    echo PLEASE_WAIT_FOR_ADMINISTRATOR_TO_ACTIVATE;\r\n                    break;\r\n                case Block_Register::ACTIVATION_TYPE_USER:\r\n                    echo PLEASE_ACTIVATE_BY_EMAIL;\r\n                    break;\r\n            }\r\n            ?>\r\n          </div>\r\n          <div class=\"alert alert-danger\" <?php echo ($localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n            <ul>\r\n              <?php foreach ((array)$localError as $key => $val) { ?>\r\n                  <li><?php echo htmlspecialchars($val)?></li>\r\n              <?php } ?>\r\n            </ul>\r\n          </div>\r\n        </div>\r\n\r\n        <div data-role=\"feedback-form\" <?php echo $success[(int)$Block->id] ? \'style=\"display: none\"\' : \'\'?>>\r\n          <p><?php echo ASTERISK_MARKED_FIELDS_ARE_REQUIRED?></p>\r\n          <?php if ($Form->signature) { ?>\r\n                <input type=\"hidden\" name=\"form_signature\" value=\"<?php echo md5(\'form\' . (int)$Form->id . (int)$Block->id)?>\" />\r\n          <?php } ?>\r\n          <?php if ($Form->antispam == \'hidden\' && $Form->antispam_field_name && !$User->id) { ?>\r\n                <input type=\"text\" autocomplete=\"off\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" value=\"<?php echo htmlspecialchars($DATA[$Form->antispam_field_name])?>\" style=\"position: absolute; left: -9999px\" />\r\n          <?php } ?>\r\n          <?php foreach ($Form->fields as $row) { ?>\r\n              <div class=\"form-group\">\r\n                <label<?php echo !$row->multiple ? \' for=\"\' . htmlspecialchars($row->urn . $row->id . \'_\' . $Block->id) . \'\"\' : \'\'?> class=\"control-label col-sm-3\"><?php echo htmlspecialchars($row->name . ($row->required ? \'*\' : \'\'))?></label>\r\n                <div class=\"col-sm-9 col-md-4\"><?php $getField($row, $DATA)?></div>\r\n              </div>\r\n          <?php } ?>\r\n          <?php if ($config[\'allow_edit_social\']) { ?>\r\n              <style type=\"text/css\">\r\n              .raas-social { display: inline-block; width: 16px; height: 16px; background-image: url(\'http://ulogin.ru/img/small.png?version=1.3.00\'); }\r\n              .raas-social<?php echo SocialProfile::SN_VK?> { background-position: 0 -19px; }\r\n              .raas-social<?php echo SocialProfile::SN_FB?> { background-position: 0 -88px; }\r\n              .raas-social<?php echo SocialProfile::SN_OK?> { background-position: 0 -42px; }\r\n              .raas-social<?php echo SocialProfile::SN_MR?> { background-position: 0 -65px; }\r\n              .raas-social<?php echo SocialProfile::SN_TW?> { background-position: 0 -111px; }\r\n              .raas-social<?php echo SocialProfile::SN_LJ?> { background-position: 0 -180px; }\r\n              .raas-social<?php echo SocialProfile::SN_GO?> { background-position: 0 -134px; }\r\n              .raas-social<?php echo SocialProfile::SN_YA?> { background-position: 0 -157px; }\r\n              .raas-social<?php echo SocialProfile::SN_WM?> { background-position: 0 -410px; }\r\n              .raas-social<?php echo SocialProfile::SN_YT?> { background-position: 0 -433px; }\r\n              </style>\r\n              <script src=\"//ulogin.ru/js/ulogin.js\"></script>\r\n              <div class=\"col-sm-offset-3 col-sm-9 col-md-6\" style=\"margin-bottom: 25px\">\r\n                <h3><?php echo SOCIAL_NETWORKS?></h3>\r\n                <div data-role=\"raas-social-network-container\" style=\"margin: 20px 0\">\r\n                  <?php foreach ((array)$DATA[\'social\'] as $i => $temp) { ?>\r\n                      <div data-role=\"raas-repo-element\" class=\"clearfix\">\r\n                        <input type=\"hidden\" name=\"social[]\" value=\"<?php echo htmlspecialchars($temp)?>\" />\r\n                        <a href=\"<?php echo htmlspecialchars($temp)?>\" target=\"_blank\">\r\n                          <span class=\"raas-social raas-social<?php echo (int)SocialProfile::getSocialNetwork($temp)?>\"></span>\r\n                          <?php echo htmlspecialchars($temp)?>\r\n                        </a>\r\n                        <a href=\"#\" class=\"close\" style=\"float: right;\" data-role=\"raas-repo-del\">&times;</a>\r\n                      </div>\r\n                  <?php } ?>\r\n                </div>\r\n                <div id=\"uLogin\" data-ulogin=\"display=panel;fields=first_name,last_name;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=twitter,google,yandex,livejournal,youtube,webmoney;redirect_uri=;callback=RAAS_CMS_social_login\"></div>\r\n              </div>\r\n              <script>\r\n              jQuery(document).ready(function($) {\r\n                  $(\'[data-role=\"raas-social-network-container\"]\').on(\'click\', \'[data-role=\"raas-repo-del\"]\', function() {\r\n                      $(this).closest(\'[data-role=\"raas-repo-element\"]\').remove();\r\n                      return false;\r\n                  });\r\n                  RAAS_CMS_social_login = function(token)\r\n                  {\r\n                      $.post(location.toString(), {\'token\': token, \'AJAX\': 1}, function(data) {\r\n                          var isFound = false;\r\n                          $(\'[data-role=\"raas-social-network-container\"] input:hidden\').each(function() {\r\n                              if ($.trim($(this).val()) == $.trim(data.social)) {\r\n                                  isFound = true;\r\n                              }\r\n                          });\r\n                          if (!isFound) {\r\n                              var text = \'<div data-role=\"raas-repo-element\" class=\"clearfix\">\'\r\n                                       + \'  <input type=\"hidden\" name=\"social[]\" value=\"\' + data.social + \'\" />\'\r\n                                       + \'  <a href=\"\' + data.social + \'\" target=\"_blank\"><span class=\"raas-social raas-social\' + data.socialNetwork + \'\"></span> \' + data.social + \'</a>\'\r\n                                       + \'  <a href=\"#\" class=\"close\" style=\"float: right;\" data-role=\"raas-repo-del\">&times;</a>\'\r\n                                       + \'</div>\';\r\n                              $(\'[data-role=\"raas-social-network-container\"]\').append(text);\r\n                          }\r\n                      }, \'json\');\r\n                  }\r\n              });\r\n              </script>\r\n          <?php } ?>\r\n          <?php if ($Form->antispam == \'captcha\' && $Form->antispam_field_name && !$User->id) { ?>\r\n              <div class=\"form-group\">\r\n                <label for=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" class=\"control-label col-sm-3\"><?php echo CAPTCHA?></label>\r\n                <div class=\"col-sm-9 col-md-4\">\r\n                  <img src=\"/assets/kcaptcha/?<?php echo session_name() . \'=\' . session_id()?>\" /><br />\r\n                  <input type=\"text\" name=\"<?php echo htmlspecialchars($Form->antispam_field_name)?>\" class=\"form-control\" />\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n          <div class=\"form-group\">\r\n            <div class=\"col-sm-9 col-md-4 col-sm-offset-3\"><button class=\"btn btn-primary\" type=\"submit\"><?php echo $User->id ? SAVE : DO_REGISTER?></button></div>\r\n          </div>\r\n        </div>\r\n      </form>\r\n    </div>\r\n<?php } ?>\r\n', 0),
(52, 2, 'activation', 'Активация', '<?php\r\nnamespace RAAS\\CMS\\Users;\r\n\r\nif ($_POST[\'AJAX\']) {\r\n    $result = array();\r\n    if ($success) {\r\n        $result[\'success\'] = 1;\r\n    }\r\n    if ($localError) {\r\n        $result[\'localError\'] = $localError;\r\n    }\r\n    ob_clean();\r\n    echo json_encode($result);\r\n    exit;\r\n} else {\r\n    ?>\r\n    <div data-role=\"notifications\">\r\n      <div class=\"alert alert-success\" <?php echo ($success) ? \'\' : \'style=\"display: none\"\'?>><?php echo YOUR_ACCOUNT_HAS_BEEN_SUCCESSFULLY_ACTIVATED?></div>\r\n      <div class=\"alert alert-danger\" <?php echo ($localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n        <ul>\r\n          <?php foreach ((array)$localError as $key => $val) { ?>\r\n              <li><?php echo htmlspecialchars($val)?></li>\r\n          <?php } ?>\r\n        </ul>\r\n      </div>\r\n    </div>\r\n    <?php\r\n}\r\n', 0),
(53, 2, 'login', 'Вход в систему', '<?php\r\nnamespace RAAS\\CMS\\Users;\r\n\r\nif ($_POST[\'AJAX\']) {\r\n    $result = array();\r\n    if ($success[(int)$Block->id]) {\r\n        $result[\'success\'] = 1;\r\n    }\r\n    if ($localError) {\r\n        $result[\'localError\'] = $localError;\r\n    }\r\n    if ($social) {\r\n        $result[\'social\'] = trim($social);\r\n    }\r\n    if ($social) {\r\n        $result[\'socialNetwork\'] = trim($socialNetwork);\r\n    }\r\n    if ($User) {\r\n        $result[\'User\'] = $User->getArrayCopy();\r\n        $result[\'User\'][\'last_name\'] = $User->last_name;\r\n        $result[\'User\'][\'first_name\'] = $User->first_name;\r\n        $result[\'User\'][\'full_name\'] = $User->full_name;\r\n    }\r\n    ob_clean();\r\n    echo json_encode($result);\r\n    exit;\r\n} else {\r\n    ?>\r\n    <div class=\"feedback\">\r\n      <form class=\"form-horizontal\" method=\"post\" enctype=\"multipart/form-data\">\r\n        <div data-role=\"notifications\" <?php echo ($success || $localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n          <div class=\"alert alert-danger\" <?php echo ($localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n            <ul>\r\n              <?php foreach ((array)$localError as $key => $val) { ?>\r\n                  <li><?php echo htmlspecialchars($val)?></li>\r\n              <?php } ?>\r\n            </ul>\r\n          </div>\r\n        </div>\r\n        <div data-role=\"feedback-form\" <?php echo $success ? \'style=\"display: none\"\' : \'\'?>>\r\n          <div class=\"form-group\">\r\n            <label for=\"login_<?php echo (int)$Block->id?>\" class=\"control-label col-sm-3 col-md-2\"><?php echo LOGIN?>:</label>\r\n            <div class=\"col-sm-9 col-md-4\"><input type=\"text\" class=\"form-control\" name=\"login\" id=\"login_<?php echo (int)$Block->id?>\" /></div>\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label for=\"password_<?php echo (int)$Block->id?>\" class=\"control-label col-sm-3 col-md-2\"><?php echo PASSWORD?>:</label>\r\n            <div class=\"col-sm-9 col-md-4\"><input type=\"password\" class=\"form-control\" name=\"password\" id=\"password_<?php echo (int)$Block->id?>\" /></div>\r\n          </div>\r\n          <?php if (($config[\'password_save_type\'] == Block_LogIn::SAVE_PASSWORD_SAVE_PASSWORD) || ($config[\'password_save_type\'] == Block_LogIn::SAVE_PASSWORD_FOREIGN_COMPUTER)) { ?>\r\n              <div class=\"form-group\">\r\n                <div class=\"col-sm-9 col-md-4 col-sm-offset-3 col-md-offset-2\">\r\n                  <label>\r\n                    <?php if ($config[\'password_save_type\'] == Block_LogIn::SAVE_PASSWORD_SAVE_PASSWORD) { ?>\r\n                        <input type=\"checkbox\" name=\"save_password\" value=\"1\" /> <?php echo SAVE_PASSWORD?>\r\n                    <?php } elseif ($config[\'password_save_type\'] == Block_LogIn::SAVE_PASSWORD_FOREIGN_COMPUTER) { ?>\r\n                        <input type=\"checkbox\" name=\"foreign_computer\" value=\"1\" /> <?php echo FOREIGN_COMPUTER?>\r\n                    <?php } ?>\r\n                  </label>\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n          <div class=\"form-group\">\r\n            <label class=\"control-label col-sm-3 col-md-2\">&nbsp;</label>\r\n            <div class=\"col-sm-9 col-md-4\"><a href=\"/recovery/\"><?php echo LOST_PASSWORD?></a></div>\r\n          </div>\r\n          <?php if ($config[\'social_login_type\']) { ?>\r\n              <div class=\"col-sm-offset-3 col-md-offset-2\" style=\"margin-bottom: 25px\">\r\n                <script src=\"//ulogin.ru/js/ulogin.js\"></script>\r\n                <div id=\"uLogin\" data-ulogin=\"display=panel;optional=first_name,last_name,phone,email,sex,nickname,bdate,city,country;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=twitter,google,yandex,livejournal,youtube,webmoney;redirect_uri=<?php echo urlencode(\'http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'] . $_SERVER[\'REQUEST_URI\'])?>\"></div>\r\n              </div>\r\n          <?php } ?>\r\n          <div class=\"form-group\"><div class=\"col-sm-9 col-md-4 col-sm-offset-3 col-md-offset-2\"><button class=\"btn btn-primary\" type=\"submit\"><?php echo DO_LOGIN?></button></div></div>\r\n        </div>\r\n      </form>\r\n    </div>\r\n<?php } ?>\r\n', 0),
(54, 2, 'recovery', 'Восстановление пароля', '<?php\r\nnamespace RAAS\\CMS\\Users;\r\n\r\nif ($_POST[\'AJAX\']) {\r\n    $result = array();\r\n    if ($success) {\r\n        $result[\'success\'] = 1;\r\n    }\r\n    if ($localError) {\r\n        $result[\'localError\'] = $localError;\r\n    }\r\n    ob_clean();\r\n    echo json_encode($result);\r\n    exit;\r\n} else {\r\n    ?>\r\n    <div class=\"feedback\">\r\n      <form class=\"form-horizontal\" method=\"post\" enctype=\"multipart/form-data\">\r\n        <div data-role=\"notifications\" <?php echo ($success || $localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n          <div class=\"alert alert-success\" <?php echo ($success) ? \'\' : \'style=\"display: none\"\'?>>\r\n            <?php echo $proceed ? YOUR_PASSWORD_WAS_SUCCESSFULLY_CHANGED : RECOVERY_KEY_WAS_SENT?>\r\n          </div>\r\n          <div class=\"alert alert-danger\" <?php echo ($localError) ? \'\' : \'style=\"display: none\"\'?>>\r\n            <ul>\r\n              <?php foreach ((array)$localError as $key => $val) { ?>\r\n                  <li><?php echo htmlspecialchars($val)?></li>\r\n              <?php } ?>\r\n            </ul>\r\n          </div>\r\n        </div>\r\n        <div data-role=\"feedback-form\" <?php echo $success ? \'style=\"display: none\"\' : \'\'?>>\r\n          <?php if ($proceed) { ?>\r\n              <?php if (!$key_is_invalid) { ?>\r\n                  <div class=\"form-group\">\r\n                    <label for=\"password_<?php echo (int)$Block->id?>\" class=\"control-label col-sm-3 col-md-2\"><?php echo PASSWORD?></label>\r\n                    <div class=\"col-sm-9 col-md-4\"><input type=\"password\" class=\"form-control\" name=\"password\" id=\"password_<?php echo (int)$Block->id?>\" /></div>\r\n                  </div>\r\n                  <div class=\"form-group\">\r\n                    <label for=\"password_confirm_<?php echo (int)$Block->id?>\" class=\"control-label col-sm-3 col-md-2\"><?php echo PASSWORD_CONFIRM?></label>\r\n                    <div class=\"col-sm-9 col-md-4\"><input type=\"password\" class=\"form-control\" name=\"password@confirm\" id=\"password_confirm_<?php echo (int)$Block->id?>\" /></div>\r\n                  </div>\r\n                  <div class=\"form-group\"><div class=\"col-sm-9 col-md-4 col-sm-offset-3 col-md-offset-2\"><button class=\"btn btn-primary\" type=\"submit\"><?php echo CHANGE?></button></div></div>\r\n              <?php } ?>\r\n          <?php } else { ?>\r\n              <div class=\"form-group\">\r\n                <label for=\"login_<?php echo (int)$Block->id?>\" class=\"control-label col-sm-3\"><?php echo ENTER_LOGIN_OR_EMAIL?></label>\r\n                <div class=\"col-sm-9 col-md-4\"><input type=\"text\" name=\"login\" class=\"form-control\" id=\"login_<?php echo (int)$Block->id?>\" /></div>\r\n              </div>\r\n              <div class=\"form-group\"><div class=\"col-sm-9 col-md-4 col-sm-offset-3\"><button class=\"btn btn-primary\" type=\"submit\"><?php echo SEND?></button></div></div>\r\n          <?php } ?>\r\n        </div>\r\n      </form>\r\n    </div>\r\n<?php } ?>\r\n', 0),
(55, 2, 'goods_faq', 'Вопрос-ответ к товарам', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse \\SOME\\Text;\r\nuse \\SOME\\HTTP;\r\n\r\nif ($Set) { ?>\r\n    <div class=\"goods-faq\">\r\n      <div class=\"goods-faq__list\">\r\n        <div class=\"goods-faq-list\">\r\n          <?php foreach ($Set as $row) { ?>\r\n              <div class=\"goods-faq-list__item\">\r\n                <div class=\"goods-faq-item\">\r\n                  <div class=\"goods-faq-item__text goods-faq-item__text_question\">\r\n                    <div class=\"goods-faq-item__title\">\r\n                      <span class=\"goods-faq-item__name\">\r\n                        <?php echo htmlspecialchars($row->name)?>\r\n                      </span>\r\n                      <?php\r\n                      $t = strtotime($row->date);\r\n                      if ($t <= 0) {\r\n                          $t = strtotime($row->post_date);\r\n                      }\r\n                      if ($t > 0) {\r\n                          ?>\r\n                          <span class=\"goods-faq-item__date\">\r\n                            <?php echo date(\'d\', $t) . \' \' . Text::$months[(int)date(\'m\', $t)] . \' \' . date(\'Y\', $t)?>\r\n                          </span>\r\n                      <?php } ?>\r\n                    </div>\r\n                    <div class=\"goods-faq-item__description\">\r\n                      <?php echo $row->description?>\r\n                    </div>\r\n                  </div>\r\n                  <?php if ($row->answer) { ?>\r\n                      <div class=\"goods-faq-item__text goods-faq-item__text_answer goods-faq-item__text_slider\">\r\n                        <div class=\"goods-faq-item__title\">\r\n                          <span class=\"goods-faq-item__name\">\r\n                            <?php echo ANSWER?>\r\n                          </span>\r\n                          <?php\r\n                          $t = strtotime($row->answer_date);\r\n                          if ($t <= 0) {\r\n                              $t = strtotime($row->modify_date);\r\n                          }\r\n                          if ($t > 0) {\r\n                              ?>\r\n                              <span class=\"goods-faq-item__date\">\r\n                                <?php echo date(\'d\', $t) . \' \' . Text::$months[(int)date(\'m\', $t)] . \' \' . date(\'Y\', $t)?>\r\n                              </span>\r\n                          <?php } ?>\r\n                        </div>\r\n                        <div class=\"goods-faq-item__description\">\r\n                          <div class=\"goods-faq-item__brief-description\">\r\n                            <?php echo Text::cuttext(html_entity_decode(strip_tags($row->answer), ENT_COMPAT | ENT_HTML5, \'UTF-8\'), 256, \'...\')?>\r\n                          </div>\r\n                          <div class=\"goods-faq-item__full-description\"><?php echo $row->answer?></div>\r\n                        </div>\r\n                        <div class=\"goods-faq-item__more\">\r\n                          <a href=\"#\" class=\"goods-faq-item__more-trigger\" data-show=\"<?php echo READ_ANSWER?>\" data-hide=\"<?php echo HIDE?>\">\r\n                            <?php echo READ_ANSWER?>\r\n                          </a>\r\n                        </div>\r\n                      </div>\r\n                  <?php } ?>\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n        </div>\r\n      </div>\r\n      <script src=\"/js/goods-faq.js?v=<?php echo date(\'Y-m-d\', filemtime(\'js/goods-faq.js\'))?>\"></script>\r\n    </div>\r\n<?php } ?>', 0),
(56, 2, 'goods_reviews', 'Отзывы к товарам', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\nuse \\SOME\\Text;\r\nuse \\SOME\\HTTP;\r\n\r\nif ($Set) { ?>\r\n    <div class=\"goods-reviews\">\r\n      <div class=\"goods-reviews__list\">\r\n        <div class=\"goods-reviews-list\">\r\n          <?php foreach ($Set as $row) { ?>\r\n              <div class=\"goods-reviews-list__item\">\r\n                <div class=\"goods-reviews-item\">\r\n                  <div class=\"goods-reviews-item__text goods-reviews-item__text_question\">\r\n                    <div class=\"goods-reviews-item__title\">\r\n                      <span class=\"goods-reviews-item__name\">\r\n                        <?php echo htmlspecialchars($row->name)?>\r\n                      </span>\r\n                      <?php\r\n                      $t = strtotime($row->date);\r\n                      if ($t <= 0) {\r\n                          $t = strtotime($row->post_date);\r\n                      }\r\n                      if ($t > 0) {\r\n                          ?>\r\n                          <span class=\"goods-reviews-item__date\">\r\n                            <?php echo date(\'d\', $t) . \' \' . Text::$months[(int)date(\'m\', $t)] . \' \' . date(\'Y\', $t)?>\r\n                          </span>\r\n                      <?php } ?>\r\n                    </div>\r\n                    <div class=\"goods-reviews-item__description\">\r\n                      <?php echo $row->description?>\r\n                    </div>\r\n                  </div>\r\n                  <?php if ($row->answer) { ?>\r\n                      <div class=\"goods-reviews-item__text goods-reviews-item__text_answer goods-reviews-item__text_slider\">\r\n                        <div class=\"goods-reviews-item__title\">\r\n                          <span class=\"goods-reviews-item__name\">\r\n                            <?php echo ANSWER?>\r\n                          </span>\r\n                          <?php\r\n                          $t = strtotime($row->answer_date);\r\n                          if ($t <= 0) {\r\n                              $t = strtotime($row->modify_date);\r\n                          }\r\n                          if ($t > 0) {\r\n                              ?>\r\n                              <span class=\"goods-reviews-item__date\">\r\n                                <?php echo date(\'d\', $t) . \' \' . Text::$months[(int)date(\'m\', $t)] . \' \' . date(\'Y\', $t)?>\r\n                              </span>\r\n                          <?php } ?>\r\n                        </div>\r\n                        <div class=\"goods-reviews-item__description\">\r\n                          <div class=\"goods-reviews-item__brief-description\">\r\n                            <?php echo Text::cuttext(html_entity_decode(strip_tags($row->answer), ENT_COMPAT | ENT_HTML5, \'UTF-8\'), 256, \'...\')?>\r\n                          </div>\r\n                          <div class=\"goods-reviews-item__full-description\"><?php echo $row->answer?></div>\r\n                        </div>\r\n                        <div class=\"goods-reviews-item__more\">\r\n                          <a href=\"#\" class=\"goods-reviews-item__more-trigger\" data-show=\"<?php echo READ_ANSWER?>\" data-hide=\"<?php echo HIDE?>\">\r\n                            <?php echo READ_ANSWER?>\r\n                          </a>\r\n                        </div>\r\n                      </div>\r\n                  <?php } ?>\r\n                </div>\r\n              </div>\r\n          <?php } ?>\r\n        </div>\r\n      </div>\r\n      <script src=\"/js/goods-reviews.js?v=<?php echo date(\'Y-m-d\', filemtime(\'js/goods-reviews.js\'))?>\"></script>\r\n    </div>\r\n<?php } ?>', 0),
(57, 1, 'hidden_props', 'Скрытые свойства', '<?php\n/**\n * Скрытые свойства\n * @return array<string>\n */\nreturn [\n    \'images\',\n    \'brief\',\n    \'videos\',\n    \'videos_url\',\n    \'files\',\n    \'onmain\',\n    \'article\',\n    \'price\',\n    \'price_old\',\n    \'available\',\n    \'min\',\n    \'step\'\n];\n', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_snippet_folders`
--

DROP TABLE IF EXISTS `cms_snippet_folders`;
CREATE TABLE IF NOT EXISTS `cms_snippet_folders` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `locked` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Locked',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Snippet folders';

--
-- Дамп данных таблицы `cms_snippet_folders`
--

INSERT INTO `cms_snippet_folders` (`id`, `pid`, `name`, `urn`, `locked`) VALUES
(1, 0, 'Интерфейсы', '__raas_interfaces', 1),
(2, 0, 'Представления', '__raas_views', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_snippet_folders_tree_cache`
--

DROP TABLE IF EXISTS `cms_snippet_folders_tree_cache`;
CREATE TABLE IF NOT EXISTS `cms_snippet_folders_tree_cache` (
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent ID#',
  `child_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Child ID#',
  `__level` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Level',
  PRIMARY KEY (`child_id`,`__level`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`),
  KEY `__level` (`__level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Snippet folders tree cache';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_templates`
--

DROP TABLE IF EXISTS `cms_templates`;
CREATE TABLE IF NOT EXISTS `cms_templates` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `description` text COMMENT 'Code',
  `width` int(10) UNSIGNED NOT NULL DEFAULT '640' COMMENT 'Width',
  `height` int(10) UNSIGNED NOT NULL DEFAULT '1024' COMMENT 'Height',
  `visual` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Template is visual',
  `background` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Background attachment ID#',
  `locations_info` text COMMENT 'Locations info',
  PRIMARY KEY (`id`),
  KEY `background` (`background`),
  KEY `urn` (`urn`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Templates';

--
-- Дамп данных таблицы `cms_templates`
--

INSERT INTO `cms_templates` (`id`, `name`, `urn`, `description`, `width`, `height`, `visual`, `background`, `locations_info`) VALUES
(1, 'Главная', 'main', '<?php\r\nnamespace RAAS\\CMS;\r\n\r\n$colspanSM = 4;\r\n$colspanMD = 3;\r\n\r\n$bgPage = $Page;\r\nwhile (!$bgPage->background->id && $bgPage->pid) {\r\n    $bgPage = $bgPage->parent;\r\n}\r\n$bg = $bgPage->background;\r\nunset($bgPage);\r\n\r\n$separateScripts = function($text)\r\n{\r\n    $rx = \'/\\\\<script.*?\\\\>.*?\\\\<\\\\/script\\\\>/umis\';\r\n    $scripts = \'\';\r\n    $result = $text;\r\n    if (preg_match_all($rx, $text, $regs)) {\r\n        foreach ($regs[0] as $i => $script) {\r\n            if (!preg_match(\'/maps.*?yandex.*constructor?/umis\', $script)) {\r\n                $scripts .= $script . \"\\n\";\r\n                $result = str_replace($script, \'\', $result);\r\n            }\r\n        }\r\n    }\r\n    return array($result, $scripts);\r\n};\r\n\r\nob_start();\r\n?>\r\n<!DOCTYPE html>\r\n<?php if ($Page->noindex || $Page->Material->noindex) { ?>\r\n    <!--noindex-->\r\n<?php } ?>\r\n<html lang=\"<?php echo htmlspecialchars($Page->lang)?>\">\r\n  <head>\r\n    <?php echo eval(\'?\' . \'>\' . Snippet::importByURN(\'head\')->description)?>\r\n    <?php echo $Page->location(\'head_counters\')?>\r\n  </head>\r\n  <body class=\"body <?php echo !$Page->pid ? \' body_main\' : \'\'?>\">\r\n    <div id=\"top\" class=\"body__background-holder\"<?php echo $bg->id ? \' style=\"background-image: url(\\\'/\' . htmlspecialchars($bg->fileURL) . \'\\\')\"\' : \'\'?>>\r\n      <header class=\"body__header\">\r\n        <div class=\"container\">\r\n          <div class=\"body__header-inner\">\r\n            <div class=\"row\">\r\n              <div class=\"col-sm-6 body__logo\">\r\n                <?php echo $Page->location(\'logo\')?>\r\n              </div>\r\n              <div class=\"col-sm-6 body__contacts-top\">\r\n                <?php echo $Page->location(\'contacts_top\')?>\r\n              </div>\r\n            </div>\r\n          </div>\r\n          <div class=\"body__menu-top-outer\">\r\n            <div class=\"row\">\r\n              <div class=\"col-sm-9 body__menu-top\">\r\n                <?php echo $Page->location(\'menu_top\')?>\r\n              </div>\r\n              <div class=\"col-sm-3 body__search-form\">\r\n                <?php echo $Page->location(\'search_form\')?>\r\n              </div>\r\n            </div>\r\n          </div>\r\n          <div class=\"body__banners\">\r\n            <?php echo $Page->location(\'banners\')?>\r\n          </div>\r\n        </div>\r\n      </header>\r\n      <main class=\"body__main-container\">\r\n        <div class=\"container\">\r\n          <div class=\"row\">\r\n            <?php\r\n            $leftText = $Page->location(\'left\');\r\n            $rightText = $Page->location(\'right\');\r\n            if ($leftText) { ?>\r\n                <aside class=\"body__left col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>\">\r\n                  <div class=\"body__left-inner\"><?php echo $leftText?></div>\r\n                </aside>\r\n            <?php } ?>\r\n            <?php if (count($Page->locationBlocksText[\'content\'])) {\r\n                $spanSM = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanSM);\r\n                $spanMD = 12 - (((int)(bool)$leftText + (int)(bool)$rightText) * $colspanMD);\r\n                ?>\r\n                <div class=\"body__content col-sm-<?php echo $spanSM?> col-md-<?php echo $spanMD?>\">\r\n                  <div class=\"body__content-inner\">\r\n                    <?php if (!$Page->pid) { ?>\r\n                        <?php echo $Page->location(\'content\')?>\r\n                    <?php } else { ?>\r\n                        <?php if ((count($Page->parents) + (bool)$Page->Material->id + (bool)$Page->Item->id) > 1) { ?>\r\n                            <ol class=\"breadcrumb\">\r\n                              <?php foreach ($Page->parents as $row) { ?>\r\n                                  <li><a href=\"<?php echo htmlspecialchars($row->url)?>\"><?php echo htmlspecialchars($row->getBreadcrumbsName())?></a></li>\r\n                              <?php } ?>\r\n                              <?php if ($Page->Material->id || $Page->Item->id) { ?>\r\n                                  <li><a href=\"<?php echo htmlspecialchars($Page->url)?>\"><?php echo htmlspecialchars($Page->getBreadcrumbsName())?></a></li>\r\n                              <?php } ?>\r\n                            </ol>\r\n                        <?php } ?>\r\n                        <h1 class=\"h1\"><?php echo htmlspecialchars($Page->getH1())?></h1>\r\n                        <?php echo $Page->location(\'content\')?>\r\n                        <?php echo $Page->location(\'share\')?>\r\n                    <?php } ?>\r\n                  </div>\r\n                </div>\r\n            <?php } ?>\r\n            <?php if ($rightText) { ?>\r\n                <aside class=\"body__right col-sm-<?php echo $colspanSM?> col-md-<?php echo $colspanMD?>\">\r\n                  <div class=\"body__right-inner\"><?php echo $rightText?></div>\r\n                </aside>\r\n            <?php } ?>\r\n          </div>\r\n        </div>\r\n        <?php if ($text = $Page->location(\'content2\')) { ?>\r\n            <div class=\"body__content2\"><?php echo $text?></div>\r\n        <?php } ?>\r\n        <?php if ($text = $Page->location(\'content3\')) { ?>\r\n            <div class=\"body__content3\">\r\n              <div class=\"container\">\r\n                <div class=\"body__content3-inner\"><?php echo $text?></div>\r\n              </div>\r\n            </div>\r\n        <?php } ?>\r\n        <?php if ($text = $Page->location(\'content4\')) { ?>\r\n            <div class=\"body__content4\"><?php echo $text?></div>\r\n        <?php } ?>\r\n        <?php if ($text = $Page->location(\'content5\')) { ?>\r\n            <div class=\"body__content5\">\r\n              <div class=\"container\">\r\n                <div class=\"body__content5-inner\"><?php echo $text?></div>\r\n              </div>\r\n            </div>\r\n        <?php } ?>\r\n      </div>\r\n      <footer class=\"body__footer\">\r\n        <div class=\"container\">\r\n          <div class=\"body__footer-inner\">\r\n            <div class=\"row\">\r\n              <div class=\"col-sm-6 body__copyrights\"><?php echo $Page->location(\'copyrights\')?></div>\r\n              <div class=\"col-sm-6 body__menu-bottom\"><?php echo $Page->location(\'menu_bottom\')?></div>\r\n            </div>\r\n          </div>\r\n          <div class=\"body__developer\">Разработка и сопровождение сайта <a href=\"http://volumnet.ru\" target=\"_blank\">Volume&nbsp;Networks</a></div>\r\n        </div>\r\n      </footer>\r\n    </div>\r\n    <?php\r\n    echo $Page->location(\'footer_counters\');\r\n    $content = ob_get_contents();\r\n    ob_end_clean();\r\n    $content = $separateScripts($content);\r\n    echo $content[0] . $content[1];\r\n    ?>\r\n  </body>\r\n</html>\r\n<?php if ($Page->noindex || $Page->Material->noindex) { ?>\r\n    <!--/noindex-->\r\n<?php } ?>\r\n', 660, 1320, 1, 0, '[{\"urn\":\"logo\",\"x\":\"10\",\"y\":\"0\",\"width\":\"150\",\"height\":\"120\"},{\"urn\":\"contacts_top\",\"x\":\"500\",\"y\":\"0\",\"width\":\"150\",\"height\":\"120\"},{\"urn\":\"menu_top\",\"x\":\"10\",\"y\":\"130\",\"width\":\"480\",\"height\":\"60\"},{\"urn\":\"search_form\",\"x\":\"500\",\"y\":\"130\",\"width\":\"150\",\"height\":\"60\"},{\"urn\":\"banners\",\"x\":\"10\",\"y\":\"200\",\"width\":\"640\",\"height\":\"60\"},{\"urn\":\"left\",\"x\":\"10\",\"y\":\"270\",\"width\":\"150\",\"height\":\"220\"},{\"urn\":\"content\",\"x\":\"170\",\"y\":\"270\",\"width\":\"320\",\"height\":\"220\"},{\"urn\":\"right\",\"x\":\"500\",\"y\":\"270\",\"width\":\"150\",\"height\":\"220\"},{\"urn\":\"content2\",\"x\":\"10\",\"y\":\"500\",\"width\":\"640\",\"height\":\"90\"},{\"urn\":\"content3\",\"x\":\"170\",\"y\":\"600\",\"width\":\"320\",\"height\":\"90\"},{\"urn\":\"content4\",\"x\":\"10\",\"y\":\"700\",\"width\":\"640\",\"height\":\"90\"},{\"urn\":\"content5\",\"x\":\"170\",\"y\":\"800\",\"width\":\"320\",\"height\":\"90\"},{\"urn\":\"share\",\"x\":\"170\",\"y\":\"900\",\"width\":\"320\",\"height\":\"60\"},{\"urn\":\"copyrights\",\"x\":\"10\",\"y\":\"960\",\"width\":\"150\",\"height\":\"120\"},{\"urn\":\"menu_bottom\",\"x\":\"500\",\"y\":\"960\",\"width\":\"150\",\"height\":\"120\"},{\"urn\":\"head_counters\",\"x\":\"10\",\"y\":\"1090\",\"width\":\"315\",\"height\":\"220\"},{\"urn\":\"footer_counters\",\"x\":\"335\",\"y\":\"1090\",\"width\":\"315\",\"height\":\"220\"}]');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users`
--

DROP TABLE IF EXISTS `cms_users`;
CREATE TABLE IF NOT EXISTS `cms_users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `login` varchar(255) NOT NULL DEFAULT '' COMMENT 'Login',
  `password_md5` varchar(255) NOT NULL DEFAULT '' COMMENT 'Password MD5',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT 'E-mail',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Registration date',
  `vis` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Active',
  `lang` varchar(255) NOT NULL DEFAULT 'ru' COMMENT 'Language',
  `new` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'New',
  `activated` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Activated',
  PRIMARY KEY (`id`),
  KEY `pid` (`login`),
  KEY `email` (`email`),
  KEY `post_date` (`post_date`),
  KEY `vis` (`vis`),
  KEY `new` (`new`),
  KEY `activated` (`activated`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Users';

--
-- Дамп данных таблицы `cms_users`
--

INSERT INTO `cms_users` (`id`, `login`, `password_md5`, `email`, `post_date`, `vis`, `lang`, `new`, `activated`) VALUES
(1, 'test', '368001d7d16bca9636f07daa3347428d', 'test@test.org', '2018-04-22 09:46:02', 1, 'ru', 0, 0),
(2, 'test2', '368001d7d16bca9636f07daa3347428d', 'test2@test.org', '2018-04-22 09:47:28', 1, 'ru', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users_billing_accounts`
--

DROP TABLE IF EXISTS `cms_users_billing_accounts`;
CREATE TABLE IF NOT EXISTS `cms_users_billing_accounts` (
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `billing_type_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Billing type ID#',
  `balance` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Balance',
  PRIMARY KEY (`uid`,`billing_type_id`),
  KEY `uid` (`uid`),
  KEY `billing_type_id` (`billing_type_id`),
  KEY `balance` (`balance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Billing accounts';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users_billing_transactions`
--

DROP TABLE IF EXISTS `cms_users_billing_transactions`;
CREATE TABLE IF NOT EXISTS `cms_users_billing_transactions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `author_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Author ID#',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `billing_type_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Billing type ID#',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `amount` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Transaction amount',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `billing_type_id` (`billing_type_id`),
  KEY `post_date` (`post_date`),
  KEY `amount` (`amount`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Billing transactions';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users_billing_types`
--

DROP TABLE IF EXISTS `cms_users_billing_types`;
CREATE TABLE IF NOT EXISTS `cms_users_billing_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `urn` varchar(255) NOT NULL DEFAULT '' COMMENT 'URN',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `description` text COMMENT 'Description',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Billing types';

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users_blocks_login`
--

DROP TABLE IF EXISTS `cms_users_blocks_login`;
CREATE TABLE IF NOT EXISTS `cms_users_blocks_login` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `email_as_login` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Show e-mail as login',
  `social_login_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Social networks log-in type: 0 - none, 1 - only registered, 2 - quick register',
  `password_save_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Password save type: 0 - none, 1 - checkbox "save password", 2 - checkbox "foreign computer"',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COMMENT='Log in blocks';

--
-- Дамп данных таблицы `cms_users_blocks_login`
--

INSERT INTO `cms_users_blocks_login` (`id`, `email_as_login`, `social_login_type`, `password_save_type`) VALUES
(46, 1, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users_blocks_recovery`
--

DROP TABLE IF EXISTS `cms_users_blocks_recovery`;
CREATE TABLE IF NOT EXISTS `cms_users_blocks_recovery` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `notification_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Recovery notification ID#',
  PRIMARY KEY (`id`),
  KEY `notification_id` (`notification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COMMENT='Recovery blocks';

--
-- Дамп данных таблицы `cms_users_blocks_recovery`
--

INSERT INTO `cms_users_blocks_recovery` (`id`, `notification_id`) VALUES
(47, 50);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users_blocks_register`
--

DROP TABLE IF EXISTS `cms_users_blocks_register`;
CREATE TABLE IF NOT EXISTS `cms_users_blocks_register` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `form_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Register form ID#',
  `email_as_login` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Use e-mail as login',
  `notify_about_edit` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Notify admin about profile edit',
  `allow_edit_social` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Allow to edit social networks',
  `activation_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Activation type: 0 - by admin, 1 - by user, 2 - already active',
  `allow_to` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Allow block to: -1 - unregistered, 0 - all, 1 - registered',
  `redirect_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'Redirect unallowable users to',
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COMMENT='Register blocks';

--
-- Дамп данных таблицы `cms_users_blocks_register`
--

INSERT INTO `cms_users_blocks_register` (`id`, `form_id`, `email_as_login`, `notify_about_edit`, `allow_edit_social`, `activation_type`, `allow_to`, `redirect_url`) VALUES
(45, 4, 0, 0, 0, 1, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users_groups_assoc`
--

DROP TABLE IF EXISTS `cms_users_groups_assoc`;
CREATE TABLE IF NOT EXISTS `cms_users_groups_assoc` (
  `uid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `gid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Group ID#',
  PRIMARY KEY (`uid`,`gid`),
  KEY `uid` (`uid`),
  KEY `gid` (`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users-groups associations';

--
-- Дамп данных таблицы `cms_users_groups_assoc`
--

INSERT INTO `cms_users_groups_assoc` (`uid`, `gid`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `cms_users_social`
--

DROP TABLE IF EXISTS `cms_users_social`;
CREATE TABLE IF NOT EXISTS `cms_users_social` (
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT 'Social network page URL',
  PRIMARY KEY (`uid`,`url`),
  KEY `uid` (`uid`),
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users social networks associations';

--
-- Дамп данных таблицы `cms_users_social`
--

INSERT INTO `cms_users_social` (`uid`, `url`) VALUES
(1, 'https://facebook.com/test'),
(1, 'https://vk.com/test');

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `pid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Parent group ID#',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `description` text COMMENT 'Description',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Groups of users';

-- --------------------------------------------------------

--
-- Структура таблицы `groups_levels_assoc`
--

DROP TABLE IF EXISTS `groups_levels_assoc`;
CREATE TABLE IF NOT EXISTS `groups_levels_assoc` (
  `gid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Group ID#',
  `m` varchar(32) NOT NULL DEFAULT '' COMMENT 'MID',
  `lid` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Level ID#',
  PRIMARY KEY (`gid`,`m`),
  KEY `gid` (`gid`),
  KEY `lid` (`lid`),
  KEY `m` (`m`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Groups-rights levels associations';

-- --------------------------------------------------------

--
-- Структура таблицы `groups_rights`
--

DROP TABLE IF EXISTS `groups_rights`;
CREATE TABLE IF NOT EXISTS `groups_rights` (
  `gid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Group ID#',
  `m` varchar(32) NOT NULL DEFAULT '' COMMENT 'MID',
  `access` text COMMENT 'Access data',
  PRIMARY KEY (`gid`,`m`),
  KEY `gid` (`gid`),
  KEY `m` (`m`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Groups access rights';

-- --------------------------------------------------------

--
-- Структура таблицы `levels`
--

DROP TABLE IF EXISTS `levels`;
CREATE TABLE IF NOT EXISTS `levels` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `m` varchar(32) NOT NULL DEFAULT '' COMMENT 'MID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name',
  `access` text COMMENT 'Access data',
  `locked` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Locked',
  `priority` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Priority',
  PRIMARY KEY (`id`),
  KEY `m` (`m`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Access levels';

-- --------------------------------------------------------

--
-- Структура таблицы `registry`
--

DROP TABLE IF EXISTS `registry`;
CREATE TABLE IF NOT EXISTS `registry` (
  `m` varchar(32) NOT NULL DEFAULT '' COMMENT 'MID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT 'Key',
  `value` text COMMENT 'Value',
  `locked` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Locked',
  PRIMARY KEY (`m`,`name`),
  KEY `m` (`m`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='System Registry';

--
-- Дамп данных таблицы `registry`
--

INSERT INTO `registry` (`m`, `name`, `value`, `locked`) VALUES
('', 'installDate', '2020-03-30 10:40:26', 1),
('', 'cookieLifetime', '14', 0),
('', 'minPasswordLength', '3', 0),
('', 'rowsPerPage', '20', 0),
('cms', 'tnsize', '300', 0),
('cms', 'maxsize', '1920', 0),
('cms', 'cache', '0', 0),
('cms', 'installDate', '2020-03-30 10:40:27', 1),
('cms', 'isActive', '1', 0),
('cms.meta_checker', 'installDate', '2018-04-05 11:34:16', 1),
('cms.meta_checker', 'isActive', '1', 0),
('cms.shop', 'installDate', '2020-03-30 10:40:27', 1),
('cms.shop', 'isActive', '1', 0),
('cms.users', 'activation_notify', '<?php\r\nnamespace RAAS\\CMS\\Users;\r\n\r\n$recoveryBlocks = Block_Recovery::getSet(array(\r\n    \'where\' => \"block_type = \'RAAS\\\\\\\\CMS\\\\\\\\Users\\\\\\\\Block_Recovery\'\",\r\n    \'orderBy\' => \'id\'\r\n));\r\n$recoveryPages = array();\r\nif ($recoveryBlocks) {\r\n    $recoveryPages = array();\r\n    foreach ($recoveryBlocks as $recoveryBlock) {\r\n        $recoveryPages = array_merge($recoveryPages, $recoveryBlock->pages);\r\n    }\r\n}\r\n$recoveryPage = null;\r\n$langRecoveryPages = array_filter($recoveryPages, function ($x) use ($User) {\r\n    return $x->lang == $User->lang;\r\n});\r\nif ($langRecoveryPages) {\r\n    $recoveryPage = array_shift($langRecoveryPages);\r\n}\r\nif (!$recoveryPage->id && $recoveryPages) {\r\n    $recoveryPage = array_shift($recoveryPages);\r\n}\r\n?>\r\n<p><?php echo GREETINGS?></p>\r\n\r\n<?php if ($active) { ?>\r\n    <p><?php echo YOUR_ACCOUNT_HAS_BEEN_ACTIVATED?></p>\r\n    <p><?php echo NOW_YOU_CAN_LOG_IN_INTO_THE_SYSTEM?></p>\r\n    <p>\r\n      <strong><?php echo YOUR_LOGIN?>:</strong> <?php echo htmlspecialchars($User->login)?><br />\r\n      <?php\r\n      $recoveryUrl = (\'http\' . ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\') . \'://\' . $_SERVER[\'HTTP_HOST\'])\r\n                   . ($recoveryPage->id ? $recoveryPage->url : \'/recovery/\');\r\n      echo sprintf(YOUR_PASSWORD_ISNT_STORED_IN_DATABASE_FOR_SECURITY_REASON, htmlspecialchars($recoveryUrl));\r\n      ?>\r\n    </p>\r\n<?php } else { ?>\r\n    <p><?php echo YOUR_ACCOUNT_HAS_BEEN_BLOCKED?></p>\r\n    <p><?php echo PLEASE_CONTACT_SITE_ADMINISTRATOR_TO_ASK_REASON?></p>\r\n<?php } ?>\r\n\r\n<p>--</p>\r\n<p>\r\n  <?php echo WITH_RESPECT?>,<br />\r\n  <?php echo ADMINISTRATION_OF_SITE?> <a href=\"http<?php echo ($_SERVER[\'HTTPS\'] == \'on\' ? \'s\' : \'\')?>://<?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'])?>\"><?php echo htmlspecialchars($_SERVER[\'HTTP_HOST\'])?></a>\r\n</p>\r\n', 0),
('cms.users', 'automatic_notification', '1', 0),
('cms.users', 'installDate', '2020-03-30 10:40:28', 1),
('cms.users', 'isActive', '1', 0),
('', 'baseVersion', '4.2.27', 0),
('cms', 'baseVersion', '4.2.49', 0),
('cms.shop', 'baseVersion', '4.2.30', 0),
('cms.users', 'baseVersion', '4.2.13', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `uncompleted_forms`
--

DROP TABLE IF EXISTS `uncompleted_forms`;
CREATE TABLE IF NOT EXISTS `uncompleted_forms` (
  `token` varchar(255) NOT NULL DEFAULT '' COMMENT 'Token',
  `field` varchar(255) NOT NULL DEFAULT '' COMMENT 'Field URN',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Post date/time',
  `fii` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Field index',
  `value` varchar(255) NOT NULL DEFAULT '' COMMENT 'Value',
  PRIMARY KEY (`token`,`field`,`fii`),
  KEY `token` (`token`),
  KEY `field` (`field`),
  KEY `fii` (`fii`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Uncompleted forms';

--
-- Дамп данных таблицы `uncompleted_forms`
--

INSERT INTO `uncompleted_forms` (`token`, `field`, `post_date`, `fii`, `value`) VALUES
('https://future-vision.ru/contacts/@feedback@64825d918c5722e52b597bf9301fe2a7@79.173.94.159@2017-09-17 15:24:57', 'email', '2017-09-17 15:29:58', 0, 'stvolovinru@gmail.com'),
('https://future-vision.ru/contacts/@feedback@64825d918c5722e52b597bf9301fe2a7@79.173.94.159@2017-09-17 15:24:57', 'phone', '2017-09-17 15:29:52', 0, '(960)9323379'),
('https://future-vision.ru/contacts/@feedback@64825d918c5722e52b597bf9301fe2a7@79.173.94.159@2017-09-17 15:24:57', '_name', '2017-09-17 15:30:35', 0, 'riben'),
('https://future-vision.ru/calculator/@feedback@3e636ee6cee296172f39986c6a9b5da0@176.215.228.94@2017-09-18 19:59:53', 'phone', '2017-09-18 20:02:43', 0, '+79500698799'),
('https://future-vision.ru/calculator/@feedback@3e636ee6cee296172f39986c6a9b5da0@176.215.228.94@2017-09-18 19:59:53', 'email', '2017-09-18 20:03:00', 0, 'denis-birjukov16@rambler.ru'),
('https://future-vision.ru/calculator/@feedback@3e636ee6cee296172f39986c6a9b5da0@176.215.228.94@2017-09-18 19:59:53', '_description_', '2017-09-18 20:03:27', 0, 'уличный экран'),
('https://future-vision.ru/calculator/@calculator@2fee2a3fbf560b5a3d93a7ffbc776b0e@188.164.138.138@2017-09-19 10:44:02', 'email', '2017-09-19 10:44:44', 0, 'taget@list.ru'),
('https://future-vision.ru/calculator/@calculator@2fee2a3fbf560b5a3d93a7ffbc776b0e@188.164.138.138@2017-09-19 10:44:02', 'phone', '2017-09-19 10:44:50', 0, '+7 (905) 537-90-58'),
('https://future-vision.ru/calculator/@calculator@2fee2a3fbf560b5a3d93a7ffbc776b0e@188.164.138.138@2017-09-19 10:44:02', 'agree', '2017-09-19 10:45:14', 0, '1'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', 'full_name', '2017-09-19 11:32:23', 0, 'У Яньшань'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', 'country', '2017-09-19 11:32:30', 0, 'Китай'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', 'company_name', '2017-09-19 11:32:02', 0, 'YAHAM'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', 'city', '2017-09-19 11:33:49', 0, 'Шэньчжэнь'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', 'phone', '2017-09-19 11:50:31', 0, '+8 (962) 715-95-34'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', 'email', '2017-09-19 11:35:46', 0, 'janey@yaham.com.cn'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', 'url', '2017-09-19 11:38:29', 0, 'Yanshan Wu'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', 'do_sales', '2017-09-19 11:38:58', 0, '1'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', '_description_', '2017-09-19 11:52:40', 0, ''),
('https://future-vision.ru/@feedback@d3211d4b39f7bb19b9ac7d6647543541@109.195.97.168@2017-09-19 11:56:56', 'phone', '2017-09-19 11:57:02', 0, 'Тест'),
('https://future-vision.ru/@feedback@d3211d4b39f7bb19b9ac7d6647543541@109.195.97.168@2017-09-19 11:56:56', 'email', '2017-09-19 11:57:08', 0, 'test@test.ru'),
('https://future-vision.ru/dealers/@dealers@9eeeaf3e81bb53b845a77c33e8582aca@172.111.143.200@2017-09-19 11:31:37', 'fax', '2017-09-19 11:51:30', 0, ''),
('https://future-vision.ru/@feedback@d3211d4b39f7bb19b9ac7d6647543541@109.195.97.168@2017-09-19 11:56:56', '_description_', '2017-09-19 11:57:13', 0, 'Тест'),
('https://future-vision.ru/@feedback@d3211d4b39f7bb19b9ac7d6647543541@109.195.97.168@2017-09-19 11:58:00', 'phone', '2017-09-19 11:58:05', 0, 'test'),
('https://future-vision.ru/@feedback@d3211d4b39f7bb19b9ac7d6647543541@109.195.97.168@2017-09-19 11:58:00', 'email', '2017-09-19 11:58:07', 0, 'test'),
('https://future-vision.ru/@feedback@d3211d4b39f7bb19b9ac7d6647543541@109.195.97.168@2017-09-19 11:58:00', '_description_', '2017-09-19 11:58:13', 0, 'test'),
('https://future-vision.ru/@order_call@dfabe96b9668dfbf1355440edd74a317@178.75.36.24@2017-09-19 12:16:51', 'phone', '2017-09-19 12:16:58', 0, '+7 999 000-00-00'),
('https://future-vision.ru/?utm_campaign=Russia&utm_source=google&utm_medium=cpc&utm_term=%2Bled%20%2B%D1%8D%D0%BA%D1%80%D0%B0%D0%BD&utm_content=desc&utm_source=google&utm_campaign=sverd.obl&utm_medium=cpc&utm_term=poisk&utm_content=VCH&gclid=CjwKCAjwo4jOBR', 'phone', '2017-09-21 07:57:30', 0, '89029902402'),
('https://future-vision.ru/catalog/ulichnyj_ehkrani/?utm_campaign=sverdlovskaya_obl&utm_source=google&utm_medium=cpc&utm_term=%2B%D1%81%D0%B2%D0%B5%D1%82%D0%BE%D0%B4%D0%B8%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9%20%2B%D1%8D%D0%BA%D1%80%D0%B0%D0%BD%20%2B%D0%BA%D1%83%D', 'phone', '2017-09-30 13:03:58', 0, '89122854600'),
('https://future-vision.ru/catalog/ulichnyj_ehkrani/?utm_campaign=sverdlovskaya_obl&utm_source=google&utm_medium=cpc&utm_term=%2B%D1%81%D0%B2%D0%B5%D1%82%D0%BE%D0%B4%D0%B8%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9%20%2B%D1%8D%D0%BA%D1%80%D0%B0%D0%BD%20%2B%D0%BA%D1%83%D', 'email', '2017-09-30 13:03:50', 0, 'Alina.komp@gmail.com'),
('https://future-vision.ru/solutions/@feedback@164ec751cfafb6e9f28cbf0d39441abb@194.169.247.1@2017-09-28 10:56:55', 'agree', '2017-09-28 10:57:01', 0, '1'),
('https://future-vision.ru/calculator/@calculator@23df012cd2e46f6761e46a84ce2bfd2c@185.3.34.160@2017-09-28 22:34:49', 'phone', '2017-09-28 22:36:11', 0, '+7 (920) 204-69-10'),
('https://future-vision.ru/?utm_source=google&utm_medium=remarketing_vsepolizovateki_30_dnei&utm_campaign=allusers&utm_source=google&utm_medium=cpc&utm_campaign=remarketing|804964492&utm_content=tar|aud-328151109010|cid|804964492|aid|213623550426|gid|403972', 'phone', '2017-10-01 09:06:41', 0, '(981)844-82-29');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID#',
  `login` varchar(255) NOT NULL DEFAULT '' COMMENT 'Login',
  `register_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Registration date',
  `password_md5` varchar(255) NOT NULL DEFAULT '' COMMENT 'Password MD5',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT 'E-mail',
  `last_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Last name',
  `first_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'First name',
  `second_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Second name',
  `root` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Global admin',
  `ip_filter` varchar(255) NOT NULL DEFAULT '' COMMENT 'User IP filter',
  `blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User is blocked',
  `cache_rights` text COMMENT 'Rights table serialize',
  `prefs` text COMMENT 'User preferences',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Users';

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `register_date`, `password_md5`, `email`, `last_name`, `first_name`, `second_name`, `root`, `ip_filter`, `blocked`, `cache_rights`, `prefs`) VALUES
(1, 'admin', '2018-04-05 11:33:36', '2f3ea141f3b969b622db16187d81333d', '', '', '', '', 1, '', 0, '{\"cms\":{\"rights\":[],\"mask\":-1},\"\\/\":{\"rights\":[],\"mask\":-1},\"cms.users\":{\"rights\":[],\"mask\":-1}}', '{\"lang\":\"ru\"}');

-- --------------------------------------------------------

--
-- Структура таблицы `users_groups_assoc`
--

DROP TABLE IF EXISTS `users_groups_assoc`;
CREATE TABLE IF NOT EXISTS `users_groups_assoc` (
  `uid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `gid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Group ID#',
  `group_admin` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Group admin',
  PRIMARY KEY (`uid`,`gid`),
  KEY `uid` (`uid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Users-groups associations';

-- --------------------------------------------------------

--
-- Структура таблицы `users_levels_assoc`
--

DROP TABLE IF EXISTS `users_levels_assoc`;
CREATE TABLE IF NOT EXISTS `users_levels_assoc` (
  `uid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `m` varchar(32) NOT NULL DEFAULT '' COMMENT 'MID',
  `lid` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Level ID#',
  PRIMARY KEY (`uid`,`m`),
  KEY `uid` (`uid`),
  KEY `lid` (`lid`),
  KEY `m` (`m`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Users-rights levels associations';

-- --------------------------------------------------------

--
-- Структура таблицы `users_log`
--

DROP TABLE IF EXISTS `users_log`;
CREATE TABLE IF NOT EXISTS `users_log` (
  `uid` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User ID#',
  `last_activity_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last activity date',
  `ip` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP Address',
  `package` varchar(32) NOT NULL DEFAULT '' COMMENT 'Package',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT 'Module',
  `sub` varchar(32) NOT NULL DEFAULT '' COMMENT 'Submodule',
  `action_name` varchar(32) NOT NULL DEFAULT '' COMMENT 'Action',
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Element ID#',
  PRIMARY KEY (`uid`,`last_activity_date`),
  KEY `uid` (`uid`),
  KEY `last_activity_date` (`last_activity_date`),
  KEY `package` (`package`),
  KEY `module` (`module`),
  KEY `sub` (`sub`),
  KEY `action_name` (`action_name`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Users activity log';

-- --------------------------------------------------------

--
-- Структура таблицы `users_rights`
--

DROP TABLE IF EXISTS `users_rights`;
CREATE TABLE IF NOT EXISTS `users_rights` (
  `uid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User ID#',
  `m` varchar(32) NOT NULL DEFAULT '' COMMENT 'MID',
  `access` text COMMENT 'Access data',
  PRIMARY KEY (`uid`,`m`),
  KEY `uid` (`uid`),
  KEY `m` (`m`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Users access rights';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
