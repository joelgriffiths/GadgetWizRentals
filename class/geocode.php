<?php

include_once 'config.php';

DO NOT USE
DO NOT USE
DO NOT USE
DO NOT USE
DO NOT USE
DO NOT USE
DO NOT USE
class Geocode {

    private $conn = null;
    private $locid = null;
    private $city = null;
    private $state = null;
    private $lon = null;
    private $lat = null;
    private $zip = null;
    private $country = null;

    // Sorry adding locid as an afterthought an don't want to fix EVERYTHING
    function __construct($zip='', $country='', $state='', $city, $locid = '') {
        error_log("++++++++$zip='', $country='', $state='', $city, $locid = ''++++++++++++++");
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if($locid !== '') {
                $this->locid = $locid;
                $this->read();
            } elseif($country !== '' and $city !== '' and $state !== '') {
                $this->read($country, $state, $city);
            } elseif($zip !== '') {
                $this->country = $country;
                $this->state = $state;
                $this->city = $city;
                $this->read();
            } elseif($zip !== '' and $country !== '') {
                $this->country = $country;
                $this->zip = $zip;
                $this->read();
            } else {
                throw new Exception("Zip code, State, or locationID are required");
            }


            /*$this->zip = $zip ? $zip : null;
            $this->locid = $locid ? $locid : null;
            $this->country = $country;
            $this->read();*/
        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Cannot Construct Geocode Class");
        }
        return true;
    }

    function close() {
        $this->conn = null;
    }

    function read() {
        try {
            error_log("++++++++++++++++++++++".print_r($this,true));
            if($this->locid) {
                $sql = "SELECT locid, city, region, country, metrocode, zip, lon, lat FROM geoip_locations where locid=:locid";
                $read_stmt = $this->conn->prepare( $sql );
                $read_stmt->bindValue(":locid", $this->locid, PDO::PARAM_STR );
            } elseif($this->state) {
                $sql = "SELECT locid,city,region,country,metrocode,zip,lon,lat FROM geoip_locations where city=:city and region=:state and country=:country order by postalcode limit 1";
                $read_stmt = $this->conn->prepare( $sql );
                $read_stmt->bindValue(":city", $this->city, PDO::PARAM_STR );
                $read_stmt->bindValue(":state", $this->state, PDO::PARAM_STR );
                $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );
            } else {
                $sql = "SELECT locid, city, region, country, metrocode, zip,lon, lat FROM geoip_locations where postalCode=:zipcode and country=:country limit 1";
                $read_stmt = $this->conn->prepare( $sql );
                $read_stmt->bindValue(":zipcode", $this->zip, PDO::PARAM_STR );
                $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );
            }

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("$sql Cannot retrieve geo-coordinates from the Database");
            }

            list($this->locid, $this->city, $this->state, $this->country, $this->metrocode,
                 $this->zip, $this->lon, $this->lat) =
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


    function getLocId() {
        return $this->locid;
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

    function getMetroCode() {
        return $this->netrocode;
    }


    function getCloseLocations($range, $unit='M') {

        # Kilometers or Miles (earth's radius)
        if($unit == 'K') {
            $earth = 6371;
        } else {
            $earth = 3959;
        }

        try {
            //$sql = "SELECT locid, postalcode, city, ( $earth * acos( cos( radians(@centerLat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(@centerLon) ) + sin( radians(@centerLat) ) * sin( radians( lat ) ) ) ) AS distance FROM geoip_locations HAVING distance < 25 ORDER BY distance";
            $sql = "SELECT locid, ( $earth * acos( cos( radians(@centerLat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(@centerLon) ) + sin( radians(@centerLat) ) * sin( radians( lat ) ) ) ) AS distance FROM geoip_locations HAVING distance < :range ORDER BY distance";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":range", $range, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $alocs = $read->stmt->fetchAll(PDO::FETCH_COLUMN,0);
            return $alocs;

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
