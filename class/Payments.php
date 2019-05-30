<?php

include_once 'config.php';

class Payments {

    private $conn = null;
    private $ppvars = new Array(
        'item_number' => null,
        'mc_gross' => null,
        'txn_id' => null,
        'txn_type' => null,
        'item_name' => null,
        'payer_id' => null,
        'protection_eligibility' => null,
        'address_status' => null,
        'tax' => null,
        'address_street' => null,
        'payment_date' => null,
        'payment_status' => null,
        'charset' => null,
        'address_zip' => null,
        'first_name' => null,
        'mc_fee' => null,
        'address_country_code' => null,
        'address_name' => null,
        'notify_version' => null,
        'custom' => null,
        'payer_status' => null,
        'business' => null,
        'address_country' => null,
        'address_city' => null,
        'quantity' => null,
        'verify_sign' => null,
        'payer_email' => null,
        'payment_type' => null,
        'last_name' => null,
        'address_state' => null,
        'receiver_email' => null,
        'payment_fee' => null,
        'receiver_id' => null,
        'mc_currency' => null,
        'residence_country' => null,
        'test_ipn' => null,
        'handling_amount' => null,
        'transaction_subject' => null,
        'payment_gross' => null,
        'shipping' => null,
        'ipn_track_id' => null,
    );


    function __construct($vars) {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $keys = "(";
            $values = "(";
            $comma = ''; //Sorry. Nasty
            foreach($this->ppvars as $k => $v) {
                if(isset($vars[$k]) {
                    $this->populated_ppvars[$k] = $vars[$k];
                    $keys .= $comma.$k;
                    $vals .= $comma.':'.$k;
                }
            }
            $keys .= ")";
            $values .= ")";

            $sql = "INSERT INTO Payments $keys VALUES $vals";
            foreach($this->populated_ppvars as $k => $v) {
                $stmt->bindValue(":$k", $v, PDO::PARAM_STR );
            }


            Holy Crap Screw This


        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Cannot retrieve geo-coordinates from the Database");
        }
        return true;
    }

    function close() {
        $this->conn = null;
    }

    function read() {
        try {
            $sql = "SELECT city, region, country, metrocode, lon, lat FROM geoip_locations where postalCode=:zipcode and country=:country";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":zipcode", $this->zip, PDO::PARAM_STR );
            $read_stmt->bindValue(":country", $this->country, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve geo-coordinates from the Database");
            }

            list($this->city, $this->state, $this->country, $this->metrocode,
                 $this->lon, $this->lat) =
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

    function getMetroCode() {
        return $this->netrocode;
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
