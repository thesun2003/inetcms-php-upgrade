--
-- База данных: `kolesa`
--

-- --------------------------------------------------------

--
-- Структура таблицы `kolesa_admins`
--

CREATE TABLE IF NOT EXISTS `kolesa_admins` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `passw` varchar(255) NOT NULL,
  `privileges` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ;

-- --------------------------------------------------------

--
-- Структура таблицы `kolesa_gallery`
--

CREATE TABLE IF NOT EXISTS `kolesa_gallery` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `col_num` smallint(6) NOT NULL DEFAULT '4',
  `limit` smallint(6) NOT NULL DEFAULT '-1',
  `width` smallint(6) NOT NULL DEFAULT '200',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ;

-- --------------------------------------------------------

--
-- Структура таблицы `kolesa_images`
--

CREATE TABLE IF NOT EXISTS `kolesa_images` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `gallery_id` smallint(6) NOT NULL DEFAULT '-1',
  `descr` varchar(500) NOT NULL,
  `position` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ;

-- --------------------------------------------------------

--
-- Структура таблицы `kolesa_installed_modules`
--

CREATE TABLE IF NOT EXISTS `kolesa_installed_modules` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `module_id` varchar(255) NOT NULL,
  `menu_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ;

-- --------------------------------------------------------

--
-- Структура таблицы `kolesa_menu`
--

CREATE TABLE IF NOT EXISTS `kolesa_menu` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('menu','page') NOT NULL DEFAULT 'menu',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `descr` varchar(255) NOT NULL,
  `keyw` varchar(255) NOT NULL,
  `parent_id` smallint(6) NOT NULL DEFAULT '0',
  `position` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ;

-- --------------------------------------------------------

--
-- Структура таблицы `kolesa_page`
--

CREATE TABLE IF NOT EXISTS `kolesa_page` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `content` longtext NOT NULL,
  `menu_id` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ;

-- --------------------------------------------------------

--
-- Структура таблицы `kolesa_rewrite_301`
--

CREATE TABLE IF NOT EXISTS `kolesa_rewrite_301` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_url` varchar(255) NOT NULL,
  `serialized_template_url` text NOT NULL,
  `rewrite_url` varchar(255) NOT NULL,
  `date_last_requested` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ;

-- --------------------------------------------------------

--
-- Структура таблицы `kolesa_search`
--

CREATE TABLE IF NOT EXISTS `kolesa_search` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `query_data` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ;

INSERT INTO `kolesa_admins` (`id`, `login`, `passw`, `privileges`) VALUES
(1, 'admin', 'fbadad03363b288894dbad0f1046d61f', 1),
(2, 'thesun2003', '39babc59304df111cf56ad24e418e8de', 1);
