<?php

include_once 'config.php';

class Regions {

    private $conn = null;
    private $city = null;
    private $state = null;
    private $country = 'US';
    private $metrocode = null;

    function __construct() {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_PERSISTENT => true));
            //$this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Cannot construct Regions");
        }
        return true;
    }

    function close() {
        $this->conn = null;
    }

    function printSelectedCountries($selectedCountry = null) {
        $countries = $this->getCountries();

        $options = '';
        //overloading doesn't work when I pass in a null
        if($selectedCountry === null) $selectedCountry = 'US';

        $options .= "<option value='US'>United States</options>\n";
        foreach($countries as $abbrev => $country) {
            if($abbrev == $selectedCountry) {
                $options .= "<option selected value='$abbrev'>$country</options>\n";
            } else {
                $options .= "<option value='$abbrev'>$country</options>\n";
            }
        }
        $this->country = $selectedCountry;
        return($options);
    }

    function getCountry() { return $this->country; }
    function setCountry($country) { $this->country = $country; }
    function getCountries() {
        try {
            //$sql = "SELECT distinct(shortname), longname FROM GeoIPCountryWhois A,geoip_locations B WHERE shortname not IN ('A1', 'A2') and metrocode != 0 and A.shortname = B.country order by longname";
            $sql = "SELECT distinct(shortname), longname FROM GeoIPCountryWhois WHERE shortname not IN ('A1', 'A2') order by longname";
            $read_stmt = $this->conn->prepare( $sql );
            //$read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Regions::getCountries Cannot retrieve geo-coordinates from the Database");
            }

            $countries = $read_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            //#error_log(print_r($countries,true));
            return $countries;

        } catch ( PDOException $e ) {
            error_log("Regions countries".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('Regions: '.$e->getMessage());
            throw($e);
         }
    }


    function printSelectedStates($selectedState = '', $country = null) {
        $allstates = $this->getStates($country);

        // Sorry Not going to translate this bigger
        $options = '';
        foreach($allstates as $state) {

            // Use the first state that pops up if one is not select for the return variable
            $selectedState = $selectedState == null && $state != '' ? $state : $selectedState;

            if($state == '' || $state == null)
                continue;

            if($state == $selectedState) {
                $options .= "<option selected value='$state'>$state</options>\n";
            } else {
                $options .= "<option value='$state'>$state</options>\n";
            }
        }
        $this->state = $selectedState;
        //error_log($options);
        return($options);
    }


    function getState() { return $this->state; }
    function setState($state) { $this->state = $state; }
    function getStates($passedcountry = null) {
        try {
            if($passedcountry) {
                $this->country = $passedcountry;
            }

            //$sql = "SELECT distinct(region) FROM geoip_locations where country=:country and region != '' and region != '00' order by region desc";
            //$sql = "SELECT distinct(region) FROM geoip_locations where country=:country and region != '' order by region desc";
            $sql = "SELECT distinct(region) FROM geoip_locations where country=:country order by region asc";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                return array();
                //throw new Exception("Cannot retrieve states from the Database");
            }

            
            $states = $read_stmt->fetchAll(PDO::FETCH_COLUMN);

            //if( $numRows == 1 and $states[0] == '00' ) {
            //    return array();
            //}

            return $states;

        } catch ( PDOException $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('State: '.$e->getMessage());
            throw($e);
         }
    }


    function getCity() { return $this->city; }
    function setCity($city) { $this->city = $city; }
    // Should probably return the locid for this one
    function printSelectedCities($selectedCity = null) {
        $cities = $this->getCities();

        // Sorry Not goijng to translate this bigger
        $options = '';
        foreach($cities as $city) {

            //error_log($city);

            // Use the first state that pops up if one is not select for the return variable
            $selectedCity = $selectedCity == null ? $city : $selectedCity;

            if($city == $selectedCity) {
                //$options .= "$city";
                $options .= "<option selected value='$city'>$city</options>\n";
            } else {
                //$options .= "$city";
                $options .= "<option value='$city'>$city</options>\n";
            }
        }
        $this->city = $selectedCity;
        return($options);
    }


    function getCities() {
        try {

            //error_log(print_r($this,true));
            //if($this->state === null || $this->country === null)
            if($this->country === null)
                throw new Exception("Region and Country Required to retrieve cities");

            $sql = "SELECT distinct(city) FROM geoip_locations where country=:country and region=:region order by city";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );
            //error_log("WTF IS THIS:  $this->country - ".print_r($this->state,true));
            $read_stmt->bindValue(":region", $this->state, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve city from the Database");
            }

            $cities = $read_stmt->fetchAll(PDO::FETCH_COLUMN);
            return $cities;

        } catch ( PDOException $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('getCities: '.$e->getMessage());
            throw($e);
         }
    }

    function getMetroCode($country = null, $state = null, $city = null) {
        try {

            //error_log("-------------- MC ------------");
            //error_log(print_r($this, true));
            //error_log("------------------------------");

            if($this->state === null || $this->country === null || $this->city == null)
                throw new Exception("Region, City, and Country Required to retrieve metrocode");

            $sql = "SELECT distinct(metrocode) FROM geoip_locations where country=:country and region=:state and city=:city";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );
            $read_stmt->bindValue(":state", $this->state, PDO::PARAM_STR );
            $read_stmt->bindValue(":city", $this->city, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve metrocode from the Database");
            }

            $this->metrocode = $read_stmt->fetch(PDO::FETCH_COLUMN);
            return $this->metrocode;

        } catch ( PDOException $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('MeroCode: '.$e->getMessage());
            throw($e);
         }
    }

    function getLocID() {
        if($this->locid == null) {
            $this->getCoords();
        }
        return $this->locid;
    }

    function getCoords() {
        try {

            //error_log("-------------- Coords ------------");

            if($this->state === null || $this->country === null || $this->city == null)
                throw new Exception("Region, City, and Country Required to retrieve coordinates");

            $sql = "SELECT locid, lat,lon FROM geoip_locations where country=:country and region=:state and city=:city order by postalcode limit 1";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );
            $read_stmt->bindValue(":state", $this->state, PDO::PARAM_STR );
            $read_stmt->bindValue(":city", $this->city, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve coordinates from the Database");
            }

            list($this->locid, $this->lat, $this->lon)  = $read_stmt->fetch(PDO::FETCH_NUM);
            //error_log("$this->lat, $this->lon");
            return array($this->lat, $this->lon);

        } catch ( PDOException $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('MeroCode: '.$e->getMessage());
            throw($e);
         }
    }

    function getCloseLocations($range, $unit='M') {
        $this->getCoords();

        //error_log("COORDS:  $this->lat  $this->lon");

        # Kilometers or Miles (earth's radius)
        if($unit == 'K') {
            $earth = 6371;
        } else {
            $earth = 3959;
        }

        try {
            //$sql = "SELECT locid, postalcode, city, ( $earth * acos( cos( radians(@centerLat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(@centerLon) ) + sin( radians(@centerLat) ) * sin( radians( lat ) ) ) ) AS distance FROM geoip_locations HAVING distance < 25 ORDER BY distance";
            $sql = "SELECT locid, ( $earth * acos( cos( radians(:centerLat) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(:centerLon) ) + sin( radians(:centerLat) ) * sin( radians( lat ) ) ) ) AS distance FROM geoip_locations HAVING distance < :range ORDER BY distance";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":range", $range, PDO::PARAM_STR );
            $read_stmt->bindValue(":centerLat", $this->lat, PDO::PARAM_STR );
            $read_stmt->bindValue(":centerLon", $this->lon, PDO::PARAM_STR );

            $read_stmt->execute();

            $alocs = $read_stmt->fetchAll(PDO::FETCH_COLUMN);
//error_log("--------------------".print_r($alocs, true));
            return $alocs;

        } catch ( Exception $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
         }
    }

    function getCloseCities($mc = null) {
        try {

            if($mc == null)
                $mc = $this->metrocode;

            if($mc == 0) {
                $closecities = $this->city;
                return "<pre>".$this->city."</pre>";

            }

            $sql = "SELECT distinct(city) FROM geoip_locations where metrocode=:mc order by city";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":mc", $mc, PDO::PARAM_INT );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve metrocode from the Database");
            }

            $closecities = $read_stmt->fetchAll(PDO::FETCH_COLUMN);

            return "<pre>".print_r($closecities,true)."</pre>";

        } catch ( PDOException $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('Close Cities: '.$e->getMessage());
            throw($e);
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
