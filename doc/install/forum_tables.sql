DROP TABLE IF EXISTS `xrowforum_notification`;
CREATE TABLE `xrowforum_notification` (
  `user_id` int(10) unsigned NOT NULL,
  `path_string` varchar(255) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  KEY `user_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `xrowforum_viewcount`;
CREATE TABLE `xrowforum_viewcount` (
  `contentobject_id` int(10) unsigned NOT NULL,
  `viewcount` int(10) unsigned NOT NULL,
  PRIMARY KEY (`contentobject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `xrowforum_rank`;
CREATE TABLE `xrowforum_rank` (
  `rank_index` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rank_name` varchar(45) NOT NULL,
  `rank_category` varchar(45) NOT NULL,
  `rank_condition` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`rank_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;