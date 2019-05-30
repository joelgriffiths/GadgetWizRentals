CREATE TABLE `reservations` (
  `reservationid` int(11) NOT NULL AUTO_INCREMENT,
  `buyerid` int(11) NOT NULL,
  `sellerid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `pickupdate` datetime NOT NULL,
  `returndate` datetime NOT NULL,
  `price` float(9,2) NOT NULL,
  `interval` enum('hour','day','week','month','year') NOT NULL,
  `numintervals` SMALLINT UNSIGNED NOT NULL,
  `deposit` float(9,2) NOT NULL DEFAULT 0,
  `total` float(9,2) NOT NULL DEFAULT 0,
  `prepaid` float(9,2) NOT NULL DEFAULT 0,
  `deliveryoption` enum('pickuponly','deliveryavailable','deliveryrequired') NOT NULL,
  `deliveryaddress` int(11) DEFAULT 0,
  PRIMARY KEY (`reservationid`),
  KEY (`itemid`, `pickupdate`, `returndate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `address` (
  `addressid` int(11) NOT NULL,
  `country` varchar(20) DEFAULT 'US',
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(2) DEFAULT NULL,
  `postalCode` varchar(20) DEFAULT NULL,
  `zip4` varchar(20) DEFAULT NULL,
  `metrocode` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`addressid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
