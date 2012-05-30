CREATE TABLE `usergroups` (
  `id_user` int(10) unsigned NOT NULL,
  `id_group` int(10) unsigned NOT NULL,
  `primary` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_group`),
  CONSTRAINT `usergroups_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usergroups_ibfk_2` FOREIGN KEY (`id_group`) REFERENCES `groups` (`id_group`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
