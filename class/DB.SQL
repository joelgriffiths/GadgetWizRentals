create table `userinfo` (
`userID` int(11) NOT NULL,
`pphone` varchar(10),
`aphone` varchar(10),
`birthday` varchar(10),
`address1` varchar(200),
`address2` varchar(200),
`city` varchar(200),
`state` char(2),
`zip` char(5),
`zip4` char(4),
PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

# `userID` `pphone` `aphone` `birthday` `address1` `address2` `city` `state` `zip` `zip4`
