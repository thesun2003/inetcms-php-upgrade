CREATE TABLE IF NOT EXISTS `[table_prefix]clients` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `passw` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

CREATE TABLE IF NOT EXISTS `[table_prefix]messages` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `type` enum('ask','answer') NOT NULL DEFAULT 'ask',
  `client_id` smallint(6) NOT NULL,
  `message` LONGTEXT NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
