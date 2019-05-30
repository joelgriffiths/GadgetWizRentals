<?php

include_once 'config.php';
include_once 'validate.php';
include_once 'session.php';
include_once 'user.php';
include_once 'states.php';

//class UserInfo extends User {
class UserInfo {
	private $userID;
	private $pphone = null;
	private $aphone = null;
	private $birthday = null;
	private $city = null;
	private $state = null;
	private $zip = null;
	private $zip4 = null;
	private $country = null;
	private $address1 = null;
	private $address2 = null;
	private $newuser = false; // Probably not useful, but wtf - JLG

	// Default to false to protect against bugs
	public $activated = false;

	private $con = null;
	private $salt = SALT;
	private $cStates = null;

	public function __construct( $uid ) {
		$this->userID = $uid;
		try {
			$this->con = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true) );
			$this->con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->loadFromDB();
            $this->cStates = new States();
                } catch ( PDOException $e) {
                        error_log($e->getMessage());
                        throw new Exception("Database Error");
		} catch (Exception $e) {
			$this->con = null;
			error_log("User::Construct: ".print_r($e, true));
			return null;
		}

	}

	public function __destruct() {
        //$this->saveToDB($this->userID);
		$this->con = null;
	}

	public function UpdateRecords($data) {

		$error = '';
		$val = new Validate();
        try {
		    $this->pphone = !empty($data['pphone']) ? $val->validatePhone($data['pphone']) : '';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }
        try {
		    $this->aphone = !empty($data['aphone']) ? $val->validatePhone($data['aphone']) : '';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }
        try {
		    $this->address1 = !empty($data['address1']) ? $val->validateAddress($data['address1']) : '';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }
        try {
		    $this->address2 = !empty($data['address2']) ? $val->validateAddress($data['address2']) : '';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }
        try {
		    $this->city = !empty($data['city']) ? $val->validateCity($data['city']) : '';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }
        try {
		    $this->state = !empty($data['state']) ? $val->validateState($data['state']) : '';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }
        try {
		    $this->zip = !empty($data['zip']) ? $val->validateZip($data['zip']) : '';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }
        try {
		    $this->zip4 = !empty($data['zip4']) ? $val->validateZip4($data['zip4']) : '';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }
        try {
		    $this->country = !empty($data['country']) ? $val->validateCountry($data['country']) : 'US';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }
         try {
		    $this->birthday = !empty($data['birthday']) ? $val->validateBirthday($data['birthday']) : '';
        } catch (Exception $e) {
            $error .= $e->getMessage();
        }


        // Gotta be a better way but wtf
        if($error != '') {
            throw new Exception($e);
        }

		$this->updateAllRecords();
	}

    // Do I have enough information available to be a seller.
	public function ready2Sell() {
        $ready = true;
        $ready = ($this->zip == null || $this->zip == '') ? false : $ready;
        $ready = $this->pphone == null || $this->pphone == '' ? false : $ready;
        $ready = $this->address1 == null || $this->address1 == '' ? false : $ready;
        $ready = $this->state == null || $this->state == '' ? false : $ready;
        $ready = ($this->city == null || $this->city == '') ? false : $ready;
        $ready = $this->country == null || $this->country == '' ? false : $ready;
        return $ready;
    }

	private function updateAllRecords() {
                try {

                        if ($this->userID) {
                                $sql =  "REPLACE INTO `userinfo` (`userID`, `pphone`, `aphone`, `birthday`, `country`, `address1`, `address2`, `city`, `state`, `zip`, `zip4`) VALUES (:userID, :pphone, :aphone, :birthday, :country, :address1, :address2, :city, :state, :zip, :zip4)";
                                error_log($sql);
                                $stmt = $this->con->prepare( $sql );
                                $stmt->bindvalue(":userID", $this->userID, PDO::PARAM_INT);
                                $stmt->bindvalue(":pphone", $this->pphone, PDO::PARAM_STR);
                                $stmt->bindvalue(":aphone", $this->aphone, PDO::PARAM_STR);
                                $stmt->bindvalue(":birthday", $this->birthday, PDO::PARAM_STR);
                                $stmt->bindvalue(":country", $this->country, PDO::PARAM_STR);
                                $stmt->bindvalue(":address1", $this->address1, PDO::PARAM_STR);
                                $stmt->bindvalue(":address2", $this->address2, PDO::PARAM_STR);
                                $stmt->bindvalue(":city", $this->city, PDO::PARAM_STR);
                                $stmt->bindvalue(":state", $this->state, PDO::PARAM_STR);
                                $stmt->bindvalue(":zip", $this->zip, PDO::PARAM_STR);
                                $stmt->bindvalue(":zip4", $this->zip4, PDO::PARAM_STR);
                        } else {
                                throw new Exception("God damned hackers");
                        }

                        $eres = $stmt->execute();

                        return true;

                } catch ( PDOException $e) {
                        error_log('updateAllRecords:'.$e->getMessage());
                        throw new Exception('Database Error');
                } catch ( Exception $e) {
                        error_log("updateAllRecords loadFromDB(): ".$e->getMessage());
                        throw($e);
                }
	}

    public function xxxsaveToDB() {
        $success = false;
        try {
        
                if ($this->userID) {
                        $sql =  "REPLACE INTO `userinfo` (`userID`,`pphone`, `aphone`, ".
                                "`birthday`, `country`, `address1`, `address2`, `city`, ".
                                "`state`, `zip`, `zip4`) VALUES (:userID, :pphone, ".
                                ":aphone, :birthday, :country, :address1, :address2, ".
                                ":city, :state, :zip, :zip4)";
                        $stmt = $this->con->prepare( $sql );
                        $stmt->bindvalue(":userID", $this->userID, PDO::PARAM_INT);
                        $stmt->bindvalue(":pphone", $this->pphone, PDO::PARAM_STR);
                        $stmt->bindvalue(":aphone", $this->aphone, PDO::PARAM_STR);
                        $stmt->bindvalue(":birthday", $this->birthday, PDO::PARAM_STR);
                        $stmt->bindvalue(":country", $this->country, PDO::PARAM_STR);
                        $stmt->bindvalue(":address1", $this->address1, PDO::PARAM_STR);
                        $stmt->bindvalue(":address2", $this->address2, PDO::PARAM_STR);
                        $stmt->bindvalue(":city", $this->city, PDO::PARAM_STR);
                        $stmt->bindvalue(":state", $this->state, PDO::PARAM_STR);
                        $stmt->bindvalue(":zip", $this->zip, PDO::PARAM_STR);
                        $stmt->bindvalue(":zip4", $this->zip4, PDO::PARAM_STR);
                } else {
                        throw new Exception("Nothing to see here. Move along");
                }
        
                $eres = $stmt->execute();
        
                return true;
        } catch ( PDOException $e) {
                error_log($e->getMessage());
                throw new Exception("Database Error");
        } catch ( Exception $e) {
                error_log("USERINFO save2DB(): ".$e->getMessage());
                throw($e);
        }
    }


        public function loadFromDB() {
                $success = false;
                try {

                        if ($this->userID) {
                                $sql =  "SELECT `pphone`, `aphone`, `birthday`, `country`, `address1`, `address2`, `city`, `state`, `zip`, `zip4` ".
                                        "FROM userinfo WHERE userID = :userID";
                                $stmt = $this->con->prepare( $sql );
                                $stmt->bindvalue(":userID", $this->userID, PDO::PARAM_INT);
                        } else {
                                throw new Exception("Nothing to see here. Move along");
                        }

                        $eres = $stmt->execute();
                        $aResult =  $stmt->fetch();

                        $numRows = $stmt->rowCount();
                        if( $numRows != 1 ) $this->newuser = true;

                        list($this->pphone, $this->aphone, $this->birthday, $this->country, $this->address1, $this->address2, $this->city, $this->state, $this->zip, $this->zip4) =$aResult;
                        $success = true;


                        return $success;
                } catch ( PDOException $e) {
                        error_log($e->getMessage());
                        throw new Exception("Database Error");
                } catch ( Exception $e) {
                        error_log("USERINFO loadFromDB(): ".$e->getMessage());
                        throw($e);
                }
                // Should never reach this line
                return $success;
        }

	public function storeFormValues( $params ) {
		$this->__construct( $params );
	}

	public function formatPhone($string) {
		if (strlen($string) != 10) return;
		//preg_match( '/^\+\d(\d{3})(\d{3})(\d{4})$/', $string,  $matches );
		preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $string,  $matches );
		$result = '(' . $matches[1] . ') ' . $matches[2] . '-' . $matches[3];
		if (strlen($result) === 14)
			return($result);
	}

	public function setPrimaryPhone() {
		return $this->pphone;
	}

	public function getPrimaryPhone() {
                try {
                        $sql = "SELECT pphone FROM userinfo WHERE userID = :userID";
                        $stmt = $this->con->prepare( $sql );
                        $stmt->bindvalue(":userID", $this->userID, PDO::PARAM_INT);

                        $stmt->execute();

                        $numRows = $stmt->rowCount();
                        if( $numRows == 1 ) $success = true;


                } catch ( PDOException $e) {
                        error_log($e->getMessage());
                        throw new Exception("Database Error");
                } catch (Exception $e) {
                        error_log(print_r($e, true));
                        return false;
                }

		return($this->formatPhone($this->pphone));
	}

	public function setAltPhone() {
		return $this->aphone;
	}

	public function getAltPhone() {
		return($this->formatPhone($this->aphone));
	}

    public function getCountry() {
        return($this->country);
    }

    public function setCountry($country) {
        return($this->country = $country);
    }


    public function getAddress1() {
        return($this->address1);
    }

    public function setAddress1($address) {
        return($this->address1 = $address);
    }


    public function getAddress2() {
        return($this->address2);
    }

    public function setAddress2($address) {
        return($this->address2 = $address);
    }

    public function getLocId() {
        $geoobj = new Geocode($this->zip, $this->country, $this->state, $this->city);
        return($geoobj->getLocId());
    }

    public function getZip() {
        return($this->zip);
    }

    public function setZip($zip) {
        return($this->zip = $zip);
    }

    public function getZip4() {
        return($this->zip4);
    }

    public function setZip4($zip4) {
        return($this->zip4 = $zip4);
    }


    // Needs its own class? Yes. Ugh
    public function getAllStates() {
    }

    public function getStateOptions() {
        return($this->cStates->getStateOptions($this->state));
    }

	public function getState() {
        return($this->state);
	}

	public function setState($state) {
        // CHECK FOR VALIDITY
        $this->state = $state;
        return($this->state);
	}

	public function getCity() {
        return($this->city);
	}

	public function setCity($city) {
        // CHECK FOR VALIDITY
        $this->city = $city;
        return($this->city);
	}





	public function xxxxxuiuserIDKey() {
		try {
			$sql = "SELECT * FROM users WHERE userID = :userID and code = :code";

			$stmt = $this->con->prepare( $sql );
			$stmt->bindvalue(":code", $this->code, PDO::PARAM_STR);
			$stmt->bindvalue(":userID", $this->userID, PDO::PARAM_INT);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows == 1 ) $success = true;

			
                } catch ( PDOException $e) {
                        error_log($e->getMessage());
                        throw new Exception("Database Error");
		} catch (Exception $e) {
			error_log(print_r($e, true));
			$success = false;
		}

		return $success;
	}

	public function xxxxxvalidateReg() {
		$error = '';
		$val = new Validate();
		try {
			$error .= $val->validateUsername($this->username);
			$error .= $val->validateEmail($this->email);	
			$error .= $val->validateFirstName($this->first);	
			$error .= $val->validateLastName($this->last);	
			$error .= $val->validatePassword($this->password,$this->conpassword);	
                } catch ( PDOException $e) {
                        error_log($e->getMessage());
                        throw new Exception("Database Error");
                } catch ( Exception $e ) {
                        error_log($e->getMessage());
			throw($e);
                }

		$var = 'null';
		if($error === '')
			return true;

		return false;
	}

}

?>
