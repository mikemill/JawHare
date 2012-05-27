CREATE TABLE `users` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `passwd` varchar(128) NOT NULL,
  `email` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `salt` char(31) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8