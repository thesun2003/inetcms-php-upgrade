CREATE TABLE IF NOT EXISTS `[table_prefix]catalog` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `site_id` int(11) NOT NULL DEFAULT '0',
 `name` varchar(255) NOT NULL,
 `gallery1_id` int(11) NOT NULL DEFAULT '-1',
 `description` varchar(255) NOT NULL,
 `visible` tinyint(1) NOT NULL DEFAULT '0',
 `title` varchar(255) NOT NULL,
 `descr` varchar(255) NOT NULL,
 `keyw` varchar(255) NOT NULL,
 `parent_id` int(11) NOT NULL DEFAULT '0',
 `position` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE IF NOT EXISTS `[table_prefix]catalog_items` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `site_id` int(11) NOT NULL DEFAULT '0',
 `articul` varchar(255) NOT NULL,
 `name` varchar(255) NOT NULL,
 `gallery1_id` int(11) NOT NULL DEFAULT '-1',
 `description` varchar(255) NOT NULL,
 `cost` float NOT NULL DEFAULT '0',
 `content` text NOT NULL,
 `catalog_id` int(11) NOT NULL,
 `date_price_updated` datetime NOT NULL default '0000-00-00 00:00:00',
 `date_auto_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 KEY `articul` (`articul`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;