CREATE TABLE `settings` (
  `variable` varchar(30) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`variable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8