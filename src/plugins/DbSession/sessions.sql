
CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(200) NOT NULL DEFAULT '',
  `session_data` varchar(255) DEFAULT NULL,
  `session_expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`session_id`)
);
