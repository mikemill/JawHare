CREATE TABLE `groups` (
  `id_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(80) NOT NULL,
  PRIMARY KEY (`id_group`),
  UNIQUE KEY `groupname` (`groupname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
