<?php

// Was
CREATE TABLE `catmap` (
`catid` int(11) NOT NULL AUTO_INCREMENT,
`memberof` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`catid`,`memberof`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8

// now
drop table catmap;

drop table `feedback`;
CREATE TABLE `feedback` (
`resid` int(11) NOT NULL,
`userid` int(11) NOT NULL,
`otherid` int(11) NOT NULL,
`type` enum('buyer','seller') NOT NULL,
`fbscore` smallint(5) unsigned NOT NULL,
`fbtext` varchar(255) NOT NULL DEFAULT '',
PRIMARY KEY (`userid`, `resid`),
KEY (`otherid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `feedback` (
  `resid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `otherid` int(11) NOT NULL,
  `role` enum('buyer','seller') NOT NULL,
  `fbscore` smallint(5) unsigned NOT NULL,
  `fbtext` varchar(255) NOT NULL DEFAULT '',
  `ts` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
Qwerty1
  PRIMARY KEY (`userid`,`resid`),
  KEY `otherid` (`otherid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

?>
