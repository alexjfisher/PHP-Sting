-- simple test comment
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `test_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day` varchar(45) DEFAULT NULL,
  `month` varchar(45) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `flag_1` tinyint(1) unsigned DEFAULT NULL,
  `flag_2` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;


/*
-- Query: SELECT * FROM test.test_table
LIMIT 0, 1000

-- Date: 2018-05-12 19:10
*/
INSERT IGNORE INTO `test_table` (`id`,`day`,`month`,`date`,`flag_1`,`flag_2`) VALUES (1,'Monday','January','2018-05-12 00:00:00',1,1);
INSERT IGNORE INTO `test_table` (`id`,`day`,`month`,`date`,`flag_1`,`flag_2`) VALUES (2,'Tuesday','February','2018-05-12 00:00:00',0,1);
INSERT IGNORE INTO `test_table` (`id`,`day`,`month`,`date`,`flag_1`,`flag_2`) VALUES (3,'Wednesday','March','2018-05-12 00:00:00',0,0);
INSERT IGNORE INTO `test_table` (`id`,`day`,`month`,`date`,`flag_1`,`flag_2`) VALUES (4,'Thursday','April','2018-05-12 00:00:00',1,1);
INSERT IGNORE INTO `test_table` (`id`,`day`,`month`,`date`,`flag_1`,`flag_2`) VALUES (5,'Friday','May','2018-05-12 00:00:00',1,0);
