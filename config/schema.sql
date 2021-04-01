-- MySQL dump 10.13  Distrib 5.7.33, for Linux (x86_64)
--
-- Host: localhost    Database: rentals
-- ------------------------------------------------------
-- Server version	5.7.33-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `GeoIPCountryWhois`
--

DROP TABLE IF EXISTS `GeoIPCountryWhois`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GeoIPCountryWhois` (
  `cipstart` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cipend` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipstart` int(10) unsigned NOT NULL,
  `ipend` int(10) unsigned NOT NULL,
  `shortname` char(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ipstart`,`ipend`),
  KEY `shortname` (`shortname`),
  KEY `longname` (`longname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoLiteCityLocation`
--

DROP TABLE IF EXISTS `GeoLiteCityLocation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GeoLiteCityLocation` (
  `locid` int(11) NOT NULL DEFAULT '0',
  `country` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `region` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postalCode` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lat` float(8,4) NOT NULL,
  `lon` float(8,4) NOT NULL,
  `metrocode` int(11) DEFAULT NULL,
  `areacode` int(11) DEFAULT NULL,
  PRIMARY KEY (`locid`),
  KEY `postalCode` (`postalCode`),
  KEY `areacode` (`areacode`),
  KEY `metrocode` (`metrocode`),
  KEY `city` (`city`),
  KEY `country` (`country`),
  KEY `region` (`region`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments`
--

DROP TABLE IF EXISTS `Payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Payments` (
  `txn_id` char(20) NOT NULL,
  `item_number` int(10) unsigned NOT NULL,
  `mc_gross` float(9,2) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payer_email` varchar(255) NOT NULL,
  `payment_fee` float(9,2) NOT NULL,
  `receiver_id` varchar(20) NOT NULL,
  `txn_type` varchar(20) DEFAULT NULL,
  `protection_eligibility` smallint(5) DEFAULT NULL,
  `address_status` varchar(50) DEFAULT NULL,
  `payer_id` varchar(255) DEFAULT NULL,
  `tax` float(9,2) DEFAULT NULL,
  `address_street` varchar(255) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `charset` varchar(50) DEFAULT NULL,
  `address_zip` varchar(20) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `mc_fee` float(9,2) NOT NULL,
  `address_country_code` varchar(20) DEFAULT NULL,
  `address_name` varchar(255) DEFAULT NULL,
  `notify_version` varchar(20) DEFAULT NULL,
  `custom` varchar(50) DEFAULT NULL,
  `payer_status` varchar(20) DEFAULT NULL,
  `business` varchar(255) DEFAULT NULL,
  `address_country` varchar(255) DEFAULT NULL,
  `address_city` varchar(255) DEFAULT NULL,
  `quantity` int(5) unsigned DEFAULT NULL,
  `verify_sign` varchar(255) DEFAULT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address_state` varchar(20) DEFAULT NULL,
  `receiver_email` varchar(100) DEFAULT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `mc_currency` varchar(20) DEFAULT NULL,
  `residence_country` varchar(20) DEFAULT NULL,
  `test_ipn` int(10) unsigned DEFAULT NULL,
  `handling_amount` float(9,2) DEFAULT NULL,
  `transaction_subject` varchar(50) DEFAULT NULL,
  `payment_gross` float(9,2) DEFAULT NULL,
  `shipping` float(9,2) DEFAULT NULL,
  `ipn_track_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`txn_id`),
  KEY `item_number` (`item_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `addressid` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(20) DEFAULT 'US',
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(2) DEFAULT NULL,
  `postalcode` varchar(20) DEFAULT NULL,
  `zip4` varchar(20) DEFAULT NULL,
  `metrocode` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`addressid`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `catid` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL DEFAULT '0',
  `urlname` varchar(100) DEFAULT NULL,
  `humanname` varchar(100) NOT NULL,
  PRIMARY KEY (`catid`),
  KEY `urlname` (`urlname`)
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `distance`
--

DROP TABLE IF EXISTS `distance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distance` (
  `zip1` char(5) NOT NULL,
  `zip2` char(5) NOT NULL,
  `distance` float(10,4) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`zip1`,`zip2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `resid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `otherid` int(11) NOT NULL,
  `role` enum('buyer','seller') NOT NULL,
  `fbscore` smallint(5) unsigned NOT NULL,
  `fbtext` varchar(255) NOT NULL DEFAULT '',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`,`resid`),
  KEY `otherid` (`otherid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geocode`
--

DROP TABLE IF EXISTS `geocode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geocode` (
  `Zipcode` char(5) NOT NULL DEFAULT '',
  `ZipCodeType` varchar(12) DEFAULT NULL,
  `City` varchar(24) DEFAULT NULL,
  `State` char(2) DEFAULT NULL,
  `LocationType` varchar(12) DEFAULT NULL,
  `Lat` float(8,4) DEFAULT NULL,
  `Lon` float(8,4) DEFAULT NULL,
  `Location` varchar(20) DEFAULT NULL,
  `Decommisioned` int(1) DEFAULT NULL,
  `TaxReturnsFiled` int(11) DEFAULT NULL,
  `EstimatedPopulation` int(11) DEFAULT NULL,
  `TotalWages` int(11) DEFAULT NULL,
  PRIMARY KEY (`Zipcode`),
  KEY `City` (`City`,`State`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geoip_blocks`
--

DROP TABLE IF EXISTS `geoip_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geoip_blocks` (
  `gbl_block_start` int(10) unsigned NOT NULL,
  `gbl_block_end` int(10) unsigned NOT NULL,
  `gbl_glc_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`gbl_block_start`,`gbl_block_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geoip_locations`
--

DROP TABLE IF EXISTS `geoip_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geoip_locations` (
  `locid` int(11) unsigned NOT NULL,
  `country` char(2) NOT NULL,
  `region` varchar(2) NOT NULL,
  `city` varchar(64) NOT NULL,
  `postalcode` varchar(16) NOT NULL,
  `lat` decimal(7,4) NOT NULL,
  `lon` decimal(7,4) NOT NULL,
  `metrocode` int(11) DEFAULT NULL,
  `areacode` int(11) DEFAULT NULL,
  PRIMARY KEY (`locid`),
  KEY `postalCode` (`postalcode`),
  KEY `areacode` (`areacode`),
  KEY `metrocode` (`metrocode`),
  KEY `city` (`city`),
  KEY `country` (`country`),
  KEY `region` (`region`),
  KEY `location` (`lat`,`lon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geoip_mapping`
--

DROP TABLE IF EXISTS `geoip_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geoip_mapping` (
  `userID` int(11) NOT NULL,
  `locid` int(11) unsigned NOT NULL,
  `postalcode` varchar(16) NOT NULL,
  `city` varchar(64) NOT NULL,
  `distance` decimal(7,2) NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `imageid` int(11) NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) DEFAULT NULL,
  `imagetype` enum('profile','listing','admin') CHARACTER SET latin1 DEFAULT NULL,
  `caption` varchar(255) CHARACTER SET latin1 NOT NULL,
  `path` varchar(255) CHARACTER SET latin1 NOT NULL,
  `basename` varchar(32) CHARACTER SET latin1 NOT NULL,
  `private` int(1) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`imageid`),
  KEY `refid` (`refid`,`imagetype`),
  KEY `userid` (`userid`,`imagetype`)
) ENGINE=MyISAM AUTO_INCREMENT=321 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `category` int(11) NOT NULL,
  `condition` enum('Poor','Fair','Good','Like New') DEFAULT NULL,
  `thumbnailid` varchar(50) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `description` blob,
  `price` float(9,2) NOT NULL,
  `interval` enum('hour','day','week','month','year') NOT NULL,
  `deposit` float(9,2) NOT NULL,
  `tax` float(6,4) DEFAULT NULL,
  `taxstate` char(2) DEFAULT NULL,
  `taxshipping` char(2) DEFAULT NULL,
  `deliveryradius` int(11) NOT NULL,
  `calculatedhourlyrate` float(6,2) NOT NULL,
  `pickup` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fee` float(9,2) NOT NULL DEFAULT '0.00',
  `lat` float(11,8) DEFAULT NULL,
  `lon` float(11,8) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `toschecked` tinyint(1) DEFAULT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `locid` int(11) DEFAULT NULL,
  `deliveryoptions` enum('pickuponly','deliveryavailable','deliveryrequired','delivertofixedlocation') NOT NULL,
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `minnotice` tinyint(3) unsigned NOT NULL DEFAULT '24',
  `pickuptime` tinyint(3) unsigned NOT NULL DEFAULT '9',
  PRIMARY KEY (`itemid`),
  KEY `category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itemsearch`
--

DROP TABLE IF EXISTS `itemsearch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemsearch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `itemtitle` varchar(255) NOT NULL,
  `itemdescription` mediumtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `itemid` (`itemid`),
  FULLTEXT KEY `itemtitle` (`itemtitle`,`itemdescription`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `userID` int(11) NOT NULL,
  `time` varchar(30) CHARACTER SET latin1 NOT NULL,
  `REMOTE_ADDR` varchar(46) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `orderid` int(11) NOT NULL AUTO_INCREMENT,
  `test_ipn` int(5) NOT NULL DEFAULT '0',
  `reservationid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `txn_id` varchar(19) NOT NULL,
  `payer_email` varchar(75) NOT NULL,
  `mc_gross` float(9,2) NOT NULL,
  PRIMARY KEY (`orderid`),
  UNIQUE KEY `txn_id` (`txn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `reservationid` int(11) NOT NULL AUTO_INCREMENT,
  `buyerid` int(11) NOT NULL,
  `sellerid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `interval` enum('hour','day','week','month','year') NOT NULL,
  `numintervals` smallint(5) unsigned NOT NULL,
  `pickupdate` datetime NOT NULL,
  `returndate` datetime NOT NULL,
  `totalrent` float(9,2) NOT NULL,
  `deliveryfee` float(9,2) NOT NULL DEFAULT '0.00',
  `tax` float(9,2) NOT NULL DEFAULT '0.00',
  `resfee` float(9,2) NOT NULL DEFAULT '0.00',
  `deposit` float(9,2) NOT NULL DEFAULT '0.00',
  `deliverychoice` enum('pickup','deliver') DEFAULT NULL,
  `deliveryaddressid` int(11) DEFAULT '0',
  `status` enum('pending','resfeepaid','sellerconfirmed','depositpaid','paidinfull','complete','sellerfeespaid','buyercancelled','sellercancelled','admincancelled') NOT NULL,
  `depositstatus` enum('na','received','returned','senttoseller') DEFAULT NULL,
  PRIMARY KEY (`reservationid`),
  KEY `itemid` (`itemid`,`pickupdate`,`returndate`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `session_id` varbinary(192) NOT NULL,
  `created` int(11) NOT NULL DEFAULT '0',
  `session_data` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions_2020021601`
--

DROP TABLE IF EXISTS `sessions_2020021601`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions_2020021601` (
  `sessionid` char(128) NOT NULL,
  `set_time` char(10) NOT NULL,
  `data` text NOT NULL,
  `session_key` char(128) NOT NULL,
  PRIMARY KEY (`sessionid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions_bak`
--

DROP TABLE IF EXISTS `sessions_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sessionid` char(128) CHARACTER SET latin1 NOT NULL,
  `set_time` char(10) CHARACTER SET latin1 NOT NULL,
  `data` text CHARACTER SET latin1 NOT NULL,
  `session_key` char(128) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessionid` (`sessionid`)
) ENGINE=InnoDB AUTO_INCREMENT=11943 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `abbr` char(2) NOT NULL,
  `full` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`abbr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_items`
--

DROP TABLE IF EXISTS `test_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_items` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `category` int(11) NOT NULL,
  `condition` enum('Poor','Fair','Good','Like New') DEFAULT NULL,
  `thumbnailid` varchar(50) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `description` blob,
  `price` float(9,2) NOT NULL,
  `interval` enum('hour','day','week','month','year') NOT NULL,
  `deposit` float(9,2) NOT NULL,
  `tax` float(6,4) DEFAULT NULL,
  `taxstate` char(2) DEFAULT NULL,
  `taxshipping` char(2) DEFAULT NULL,
  `deliveryradius` int(11) NOT NULL,
  `calculatedhourlyrate` float(6,2) NOT NULL,
  `pickup` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fee` float(9,2) NOT NULL DEFAULT '0.00',
  `lat` float(11,8) DEFAULT NULL,
  `lon` float(11,8) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `toschecked` tinyint(1) DEFAULT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `deliveryoptions` enum('pickuponly','deliveryavailable','deliveryrequired','delivertofixedlocation') NOT NULL,
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `minnotice` tinyint(3) unsigned NOT NULL DEFAULT '24',
  `pickuptime` tinyint(3) unsigned NOT NULL DEFAULT '9',
  PRIMARY KEY (`itemid`),
  KEY `category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_reservations`
--

DROP TABLE IF EXISTS `test_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_reservations` (
  `reservationid` int(11) NOT NULL AUTO_INCREMENT,
  `buyerid` int(11) NOT NULL,
  `sellerid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `interval` enum('hour','day','week','month','year') NOT NULL,
  `numintervals` smallint(5) unsigned NOT NULL,
  `pickupdate` datetime NOT NULL,
  `returndate` datetime NOT NULL,
  `totalrent` float(9,2) NOT NULL,
  `deliveryfee` float(9,2) NOT NULL DEFAULT '0.00',
  `tax` float(9,2) NOT NULL DEFAULT '0.00',
  `deposit` float(9,2) NOT NULL DEFAULT '0.00',
  `deliverychoice` enum('pickup','deliver') DEFAULT NULL,
  `deliveryaddressid` int(11) DEFAULT '0',
  `status` enum('pending','depositpaid','paidinfull','complete','sellerfeespaid','buyercancelled','sellercancelled','admincancelled') NOT NULL,
  `depositstatus` enum('na','received','returned','senttoseller') DEFAULT NULL,
  PRIMARY KEY (`reservationid`),
  KEY `itemid` (`itemid`,`pickupdate`,`returndate`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userinfo`
--

DROP TABLE IF EXISTS `userinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userinfo` (
  `userID` int(11) NOT NULL,
  `pphone` varchar(10) DEFAULT NULL,
  `aphone` varchar(10) DEFAULT NULL,
  `birthday` varchar(10) DEFAULT NULL,
  `address1` varchar(200) DEFAULT NULL,
  `address2` varchar(200) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `zip` char(5) DEFAULT NULL,
  `zip4` char(4) DEFAULT NULL,
  `country` varchar(20) DEFAULT 'US',
  `lat` float(10,6) DEFAULT NULL,
  `lon` float(10,6) DEFAULT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET latin1 NOT NULL,
  `first` varchar(50) CHARACTER SET latin1 NOT NULL,
  `last` varchar(50) CHARACTER SET latin1 NOT NULL,
  `email` varchar(512) CHARACTER SET latin1 NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password` varbinary(250) NOT NULL,
  `code` varchar(25) CHARACTER SET latin1 NOT NULL,
  `activated` tinyint(1) DEFAULT '0',
  `salt` char(128) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`userID`,`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xxxGeoIPCountryWhois`
--

DROP TABLE IF EXISTS `xxxGeoIPCountryWhois`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xxxGeoIPCountryWhois` (
  `cipstart` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `cipend` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `ipstart` int(10) unsigned NOT NULL,
  `ipend` int(10) unsigned NOT NULL,
  `shortname` char(2) CHARACTER SET utf8 DEFAULT NULL,
  `longname` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ipstart`,`ipend`),
  KEY `shortname` (`shortname`),
  KEY `longname` (`longname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xxxGeoLiteCityLocation`
--

DROP TABLE IF EXISTS `xxxGeoLiteCityLocation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xxxGeoLiteCityLocation` (
  `locid` int(11) NOT NULL DEFAULT '0',
  `country` varchar(20) DEFAULT NULL,
  `region` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postalCode` varchar(20) DEFAULT NULL,
  `lat` float(8,4) NOT NULL,
  `lon` float(8,4) NOT NULL,
  `metrocode` int(11) DEFAULT NULL,
  `areacode` int(11) DEFAULT NULL,
  PRIMARY KEY (`locid`),
  KEY `postalCode` (`postalCode`),
  KEY `areacode` (`areacode`),
  KEY `metrocode` (`metrocode`),
  KEY `city` (`city`),
  KEY `country` (`country`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-31 23:34:21
