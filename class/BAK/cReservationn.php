<?php

include_once 'config.php';
/*
CREATE TABLE `reservations` (
  `reservationid` int(11) NOT NULL,
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
*/

class Reservation {

    private $conn = null;
    private $startdate = null;
    private $returndate = null;
    private $itemobj = null;
    private $total = null;

    function __construct($userid, $checkoutid=null) {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if($checkoutid && $userid) {
                $this->checkoutid = $checkoutid;
                $this->userid = $userid;
                $this->read();
            }
        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Internal Database Error");
        }
        return true;
    }

    function close() {
        $this->conn = null;
    }

    function read() {
        try {
            $sql = "SELECT city, region, country, lon, lat FROM GeoLiteCityLocation where postalCode=:zipcode and country=:country";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":zipcode", $this->zip, PDO::PARAM_STR );
            $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve geo-coordinates from the Database");
            }

            list($this->city, $this->state, $this->country, $this->lon, $this->lat) =
                    $read_stmt->fetch(PDO::FETCH_NUM);

        } catch ( PDOException $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('ZIP: '.$this->zip .':'.$e->getMessage());
            throw($e);
         }
        return true;
    }


    function getLon() {
        return $this->lon;
    }

    function getLat() {
        return $this->lat;
    }

    function getCity() {
        return $this->city;
    }

    function getState() {
        return $this->state;
    }

    function getCountry() {
        return $this->country;
    }

    function getDistance($otherzip, $unit='M') {

        # Check to see if I've done the calculation before
        try {
            $sql = "SELECT distance FROM distance where zip1=:zip1 && zip2=:zip2";
            $read_stmt = $this->conn->prepare( $sql );
            if($this->zip >  $otherzip) {
                $read_stmt->bindValue(":zip1", $this->zip, PDO::PARAM_STR );
                $read_stmt->bindValue(":zip2", $otherzip, PDO::PARAM_STR );
            } else {
                $read_stmt->bindValue(":zip1", $otherzip, PDO::PARAM_STR );
                $read_stmt->bindValue(":zip2", $this->zip, PDO::PARAM_STR );
            }

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 1 ) {
                $distance = $read_stmt->fetch(PDO::FETCH_NUM);
                return $distance;
            }

        } catch ( Exception $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
         }
 
        # Apparently I like recursion.
        $othergeo = new Geocode($otherzip);
        $lon = $othergeo->getLon();
        $lat = $othergeo->getLat();

        $theta = $lon - $this->lon;
        $dist = sin(deg2rad($lat)) * sin(deg2rad($this->lat)) +  cos(deg2rad($lat)) * cos(deg2rad($this->lat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
    
        if ($unit == "K") {
            return round(($miles * 1.609344));
        } else if ($unit == "N") {
            return round(($miles * 0.8684));
        } else {
            return round($miles);
        }
    }
}
