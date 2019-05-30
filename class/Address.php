<?php

include_once 'config.php';
include_once 'config.php';

// Only for reading categories now. Perhaps an admin interface later
// will force updates.
class Address {

	private $conn = null;

    // These define the base category type
	private $addressid = null;
	private $country = null;
	private $address1 = null;
	private $address2 = null;
	private $city = null;
	private $region = null;
	private $postalcode = null;
	private $zip4 = null;
	private $metrocode = null;

	function __construct( $addressid = '') {
		try {
			$this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if( is_numeric($addressid) ) {
                $this->loadAddressFromDB($addressid);
            } else {
                // Accept the defaults - Root Dir
            }
        } catch ( PDOException $e ) {
            error_log($e->getMessage());
            return false;
        } catch ( Exception $e ) {
            error_log($e->getMessage());
            throw($e);
        }
		return $this;
	}

	function close() {
		$this->conn = null;
	}

	function loadAddressFromDB($addressid) {
		try {

            if(is_numeric($addressid)) {
			    $sql = "SELECT `country`,`address1`,`address2`,`city`,`region`,`postalcode`,`zip4`,`metrocode` FROM address where addressid = :addressid";
			    $stmt = $this->conn->prepare( $sql );
                $stmt->bindValue(":addressid", $addressid, PDO::PARAM_INT );
            } 
			$stmt->execute();
	
			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                throw new Exception("Invalid Address ID $addressid");
            }

            $this->addressid = $addressid;
			list($this->country, $this->address1,
                 $this->address2, $this->city, $this->region,
                 $this->postalcode, $this->zip4, 
                 $this->metrocode) = $stmt->fetch(PDO::FETCH_NUM);

        } catch ( PDOException $e ) {
              error_log("Address constuctor failure db");
              throw new Exception("Address constuctor failure db");
        } catch ( Exception $e ) {
              error_log("Address constuctor failure" . $e->getMessage());
              throw $e;
        }
        return true;
	}

	function saveAddressToDB() {
		try {

            if(is_numeric($this->addressid)) {
			    $sql = "UPDATE `address` SET `country`=:country,`address1`=:address1,`address2`=:address2,`city`=:city,`region`=:region,`postalcode`=:postalcode,`zip4`=:zip4,`metrocode`=:metrocode WHERE addressid = :addressid";
			    $stmt = $this->conn->prepare( $sql );
                $stmt->bindValue(":addressid", $this->addressid, PDO::PARAM_INT );
            } else {
			    $sql = "INSERT INTO `address` (`country`,`address1`,`address2`,`city`,`region`,`postalcode`,`zip4`,`metrocode`) VALUES (:country,:address1,:address2,:city,:region,:postalcode,:zip4,:metrocode)";
			    $stmt = $this->conn->prepare( $sql );
            } 
            $stmt->bindValue(":country", $this->country, PDO::PARAM_STR );
            $stmt->bindValue(":address1", $this->address1, PDO::PARAM_STR );
            $stmt->bindValue(":address2", $this->address2, PDO::PARAM_STR );
            $stmt->bindValue(":city", $this->city, PDO::PARAM_STR );
            $stmt->bindValue(":region", $this->region, PDO::PARAM_STR );
            $stmt->bindValue(":postalcode", $this->postalcode, PDO::PARAM_INT );
            $stmt->bindValue(":zip4", $this->zip4, PDO::PARAM_INT );
            $stmt->bindValue(":metrocode", $this->metrocode, PDO::PARAM_INT );
			$stmt->execute();
	
            if(!is_numeric($this->addressid)) {
                $this->addressid = $this->conn->lastInsertId('addressid');
            }

        } catch ( PDOException $e ) {
              error_log("Address constuctor failure db:".$e->getMessage());
              throw new Exception("Address constuctor failure db");
        } catch ( Exception $e ) {
              error_log("Address constuctor failure" . $e->getMessage());
              throw $e;
        }
        return $this->addressid;;
	}



    /* Sorry no error checking on this.
     * Just Trying to get the damned site live
     */

	function setCountry($country) {
        $this->country = $country;
	}
	function getCountry() {
        return $this->country;
	}

	function setAddress1($addr) {
        $this->address1 = $addr;
	}
	function getAddress1() {
        return $this->address1;
	}

	function setAddress2($addr) {
        $this->address2 = $addr;
	}
	function getAddress2() {
        return $this->address2;
	}


	function setCity($city) {
        $this->city = $city;
	}
	function getCity() {
        return $this->city;
	}


    // Region === State in the US
	function setState($region) {
        $this->region = $region;
	}
	function getState() {
        return $this->region;
	}
	function setRegion($region) {
        $this->region = $region;
	}
	function getRegion() {
        return $this->region;
	}

	function setZip($postalcode) {
        $this->postalcode = $postalcode;
	}
	function getZip() {
        return $this->postalcode;
	}
	function setPostalCode($postalcode) {
        $this->postalcode = $postalcode;
	}
	function getPostalCode() {
        return $this->postalcode;
	}

	function setZip4($zip4) {
        $this->zip4 = $zip4;
	}
	function getZip4() {
        return $this->zip4;
	}

	function setMetroCode($metrocode) {
        $this->metrocode = $metrocode;
	}
	function getMetroCode() {
        return $this->metrocode;
	}
}
