 CREATE TABLE `userprofile` (
  `id_user` int(10) unsigned NOT NULL,
  `field` varchar(20) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id_user`,`field`),
  KEY `field` (`field`),
  CONSTRAINT `userprofile_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8