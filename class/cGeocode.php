<?php

include_once 'config.php';

class Geocode {

    private $conn = null;
    private $city = null;
    private $state = null;
    private $lon = null;
    private $lat = null;
    private $zip = null;
    private $country = null;
    private $locid = null;

    function __construct($zip='', $country='', $state='', $city='', $locid = '') {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //error_log("HERE AGAIN: $zip='', $country='', $state='', $city='', $locid = ''");
//$trace = debug_backtrace();
//error_log(print_r($trace,true));

            if($locid !== '') {
                $this->locid = $locid;
                $this->read();
            } elseif($zip !== '' and $country !== '') {
                $this->country = $country;
                $this->zip = $zip;
                $this->read();
            } elseif($country !== '' and $city !== '' and $state !== '') {
                $this->country = $country;
                $this->state = $state;
                $this->city = $city;
                $this->read();
            } else {
                throw new Exception("Zip code, State, or locationID are required");
            }


            /*$this->zip = $zip;
            $this->country = $country;
            $this->read();*/
        } catch ( Exception $e ) {
            $trace = debug_backtrace();
            error_log(print_r($trace,true));
            error_log( $e->getMessage() );
            throw new Exception("Supplied address is invalid");
        }
        return true;
    }

    function close() {
        $this->conn = null;
    }

    function read() {
        try {
            if($this->locid) {
                $sql = "SELECT locid, city, region, country, metrocode, postalCode, lon, lat FROM geoip_locations where locid=:locid";
                $read_stmt = $this->conn->prepare( $sql );
                $read_stmt->bindValue(":locid", $this->locid, PDO::PARAM_STR );
            } elseif($this->state) {
                $sql = "SELECT locid,city,region,country,metrocode,postalCode,lon,lat FROM geoip_locations where city=:city and region=:state and country=:country order by postalcode limit 1";
                $read_stmt = $this->conn->prepare( $sql );
                $read_stmt->bindValue(":city", $this->city, PDO::PARAM_STR );
                $read_stmt->bindValue(":state", $this->state, PDO::PARAM_STR );
                $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );
            } else {
                $sql = "SELECT locid, city, region, country, metrocode, postalCode,lon, lat FROM geoip_locations where postalCode=:zipcode and country=:country limit 1";
                $read_stmt = $this->conn->prepare( $sql );
                $read_stmt->bindValue(":zipcode", $this->zip, PDO::PARAM_STR );
                $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );
            }

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Geocode - Cannot retrieve geo-coordinates from the Database");
            }

            list($this->locid, $this->city, $this->state, $this->country, $this->metrocode,
                 $this->zip, $this->lon, $this->lat) =
                    $read_stmt->fetch(PDO::FETCH_NUM);

        } catch ( PDOException $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('LOCID: '.$this->locid .':'.$e->getMessage());
            throw($e);
         }
        return true;
    }


    function getLocId() {
        return $this->locid;
    }


    function getLon() {
        return $this->lon;
    }

    function getLat() {
        return $this->lat;
    }

    function getZip() {
        return $this->zip;
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

    function getMetroCode() {
        return $this->netrocode;
    }


    function getDistanceByLocID($otherlocid, $unit='M') {

        # Check to see if I've done the calculation before
        # Not actually doing anything atm
        try {
            $sql = "SELECT distance FROM distance where zip1=:zip1 && zip2=:zip2";
            $read_stmt = $this->conn->prepare( $sql );
            //if($this->zip !=  $otherzip) {
            //    $read_stmt->bindValue(":zip1", $this->locid, PDO::PARAM_STR );
            //    $read_stmt->bindValue(":zip2", $otherlocid, PDO::PARAM_STR );
            //} else {
            $read_stmt->bindValue(":zip1", $otherlocid, PDO::PARAM_STR );
            $read_stmt->bindValue(":zip2", $this->locid, PDO::PARAM_STR );
            //}

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
        $othergeo = new Geocode('','','','',$otherlocid);
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

    function getDistance($otherzip, $country, $unit='M') {

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
        $othergeo = new Geocode($otherzip, $country);
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
