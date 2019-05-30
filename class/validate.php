<?php

include_once 'config.php';
include_once 'states.php';
include_once 'cGeocode.php';

class Validate {

	public $conn = null;
	public $username = null;

	public function __construct( $username = '' ) {
		$this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array( PDO::ATTR_PERSISTENT => true) );
		$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->username = $username;
	}

	public function __destruct() {
	}

	public function validateUsername($username) {

		$success = false;
		try {

			$username = stripslashes( strip_tags( $username) );

			$sql = "SELECT * FROM users WHERE username = :username";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue("username", $username, PDO::PARAM_STR);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows != 0 ) throw new Exception("Username not available<br />");

			if (strlen($username) > MAX_USERNAME_LEN)
				throw new Exception("Username cannot exceed " . MAX_USERNAME_LEN . " characters<br />");
	

			if (strlen($username) < MIN_USERNAME_LEN)
				throw new Exception("Username must be at least " . MIN_USERNAME_LEN ." characters<br />");
	
			if (!ctype_alnum($username)) 
				throw new Exception("Username may only contain alphnumeric aracters<br />");

			return $success;
		} catch ( PDOException $e) {
			error_log($e->getMessage());
			return("PDO Error");
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	public function validateCode($code, $email, $userid) {

		try {

			$semail = stripslashes( strip_tags( $email) );

			$sql = "SELECT userid FROM users WHERE code = :code and userid = :userid and email = :email";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue("code", $code, PDO::PARAM_STR);
			$stmt->bindvalue("userid", $userid, PDO::PARAM_STR);
			$stmt->bindvalue("email", $semail, PDO::PARAM_STR);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows != 1 ) {
                error_log("ABUSE: Invald code attempt by $email, $userid");
                throw new Exception("Invalid Request. Please request <a href='/forgot.php'>Request Another Email</a><br />");
            }

			return '';
		} catch ( PDOException $e) {
			error_log($e->getMessage());
			throw new Exception("Internal Error");
		}
	}


	# The UID is used to allow somebody to submit their already registered email address
	function validateEmail($email, $uid=0) {
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = false;
			} else if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
				// local part starts or ends with '.'
				$isValid = false;
			} else if (preg_match('/\\.\\./', $local)) {
				// local part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// character not valid in domain part
				$isValid = false;
			} else if (preg_match('/\\.\\./', $domain)) {
				// domain part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
				// character not valid in local part unless 
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/',
				 	str_replace("\\\\","",$local)))
				{
					$isValid = false;
				}
			}
			try {

				// Check for duplicate email addresses
				$con = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true) );
                        	$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				// != userID so I only check conflicts with OTHER users
				$sql = "SELECT email FROM users WHERE email = :email and userid != :userid";
error_log($sql.' '.$email.' '.$uid);

				$stmt = $con->prepare( $sql );
				$stmt->bindvalue("email", $email, PDO::PARAM_STR);
				$stmt->bindvalue("userid", $uid, PDO::PARAM_INT);

				$stmt->execute();

				$numRows = $stmt->rowCount();
				if( $numRows != 0 ) throw new Exception("Email address is not available<br />");
			} catch ( PDOException $e) {
				error_log($e->getMessage());
				return("validateEmail PDO Error");
			} catch ( Exception $e ) {
				error_log($e->getMessage());
				throw($e);
			}
			


			if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
			{
				// domain not found in DNS
				$isValid = false;
			}
		}
		if(! $isValid )
			return("Invalid Email Address<br />");

	}


	public function validateCountry($country) {

		try {

			$sql = "SELECT metrocode FROM geoip_locations WHERE country = :country limit 1";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue("country", $country, PDO::PARAM_STR);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows != 0 ) throw new Exception("Invalid Country<br />");

            $metrocode = $stmt->fetchColumn();
			return $metrocode;
		} catch ( PDOException $e) {
			error_log($e->getMessage());
			return("PDO Error");
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}




	public function validateFirstName($first) {

		if (strlen($first) == 0)
			return("First Name is required<br />");
		
		if (strlen($first) > MAX_NAME_LEN)
			return("First name cannot exceed " . MAX_NAME_LEN . " characters<br />");
	

		if (strlen($first) < MIN_NAME_LEN)
			return("First name must be at least " . MIN_NAME_LEN ." characters<br />");
	
		if (!preg_match("#^[a-zA-Z]+$#", $first))
			return("First Name: Can only contain letters<br />");
		
	}


	public function validateLastName($last) {


		if (strlen($last) == 0)
			return("Last Name is required<br />");
		
		if (strlen($last) > MAX_NAME_LEN)
			return("Last name cannot exceed " . MAX_NAME_LEN . " characters<br />");
	

		if (strlen($last) < MIN_NAME_LEN)
			return("Last name must be at least " . MIN_NAME_LEN ." characters<br />");
	
		if (!preg_match("#^[a-zA-Z]+$#", $last))
			return("Last Name: Can only contain letters<br />");
		
	}

	public function validateState($state) {
        // Placeholder
        if(strcmp($state, 'Not Selected') == 0) return '';

        $states = new States();
        $stateAbbr =  $states->GetStateRecord($state);
        //error_log($stateAbbr);
        if($stateAbbr == false)
			throw new Exception("Invalid State Code $state<br />");
        else
            return $stateAbbr;
        
    }

	public function validateBirthday($bday) {
        if($unixTimeStamp = strtotime($bday) == null)
			throw new Exception("Date is invalid. Try MM/DD/YYYY<br />");

        return date("Y-m-d H:i:s", $unixTimestamp);
    }


	public function validateCity($city) {
        // Placeholder
		if (strlen($city) > 50)
			throw new Exception("City is too long<br />");
        return $city;
    }

	public function validateAddress($address) {
        // Placeholder
		if (strlen($address) > 60)
			throw new Exception("Address is too long<br />");

        return $address;
    }

	public function validatePassword($password, $conpassword) {

		if( $password !== $conpassword )
			return("Passwords do not match<br />");

		if( trim($password) !== $password )
			return("Trailing spaces not allowed<br />");

		if (strlen($password) > 15)
			return("Seriously? A bit long don't you think?<br />");

		if (strlen($password) < 6)
			return("Password must be at least 6 characters<br />");
		
		if (!preg_match("#[0-9]+#", $password))
			return("Password must include at least one number!<br />");
		
		if (!preg_match("#[a-zA-Z]+#", $password))
			return("Password must include at least one letter!<br />");

	}

	public function validatePhone($phone, $type='Primary') {
		$phone = preg_replace('/\D+/', '', $phone);
		$numbersOnly = preg_replace("/^1/", '',$phone);
		$numberOfDigits = strlen($numbersOnly);
		if ($numberOfDigits != 10) {
			throw new Exception('Invalid '.$type.' Phone Number<br />');
		}
		return $numbersOnly;
	}

    // Not used atm
	public function validateDate($date) {
            throw new Exception("Try validateBirthday dude");
     		if(strlen($date) == 10) {
        		$pattern = '/\.|\/|-/i';    // . or / or -
        		preg_match($pattern, $date, $char);
       		
        		$array = preg_split($pattern, $date, -1, PREG_SPLIT_NO_EMPTY);
       		
        		if(strlen($array[2]) == 4) {
            		// dd.mm.yyyy || dd-mm-yyyy
            		if($char[0] == "."|| $char[0] == "-") {
                		$month = $array[1];
                		$day = $array[0];
                		$year = $array[2];
            		}
            		// mm/dd/yyyy    # Common U.S. writing
            		if($char[0] == "/") {
                		$month = $array[0];
                		$day = $array[1];
                		$year = $array[2];
            		}
        		}
        		// yyyy-mm-dd    # iso 8601
        		if(strlen($array[0]) == 4 && $char[0] == "-") {
            			$month = $array[1];
            			$day = $array[2];
            			$year = $array[0];
        		}
        		if(checkdate($month, $day, $year)) {    //Validate Gregorian date
            			return TRUE;
       		
        		} else {
            			return("Invalid Date");
        		}
    		} else {
        		return('Please provide date as MM/DD/YYYY<br />');    // more or less 10 chars
    		}
	}

	public function validateZip($zip) {
		if(!preg_match("/^[0-9]{5}$/", $zip)) {
			throw new Exception ('The ZIP code must be a 5-digit number.<br />');
		} 
        return $zip;
	}

	public function validateZip4($zip) {
		if(!preg_match("/^[0-9]{4}$/", $zip)) {
			throw new Exception ('The ZIP4 code must be a 4-digit number.<br />');
		} 
        return $zip;
	}

	// get all the zipcodes within the specified radius - default 20
	function zipcodeRadius($lat, $lon, $radius) {
    		$radius = $radius ? $radius : 20;
    		$sql = 'SELECT distinct(ZipCode) FROM zipcode  WHERE (3958*3.1415926*sqrt((Latitude-'.$lat.')*(Latitude-'.$lat.') + cos(Latitude/57.29578)*cos('.$lat.'/57.29578)*(Longitude-'.$lon.')*(Longitude-'.$lon.'))/180) <= '.$radius.';';
    		$result = $this->db->query($sql);
    		// get each result
    		$zipcodeList = array();
    		while($row = $this->db->fetch_array($result)) {
        		array_push($zipcodeList, $row['ZipCode']);
    		}
    		return $zipcodeList;
	}

	public function validateCategory($category) {

		$success = false;
		try {

			    $sql = "SELECT catid FROM category WHERE catid = :catid";

			    $stmt = $this->conn->prepare( $sql );
			    $stmt->bindvalue(":catid", $category, PDO::PARAM_STR);

			    $stmt->execute();

			    $numRows = $stmt->rowCount();
			    if( $numRows == 0 ) throw new Exception("Invalid Category");
		} catch ( PDOException $e) {
			error_log($e->getMessage());
            throw new Exception("DB Error");
		}
	}


	public function validateTitle($title) {

        $stitle = strip_tags($title);
        if($stitle != $title)
            throw new Exception("Invalid Characters in Title");

        if(strlen($stitle) < 5 ) {
            throw new Exception("Title is too short");
        }
	}

	public function validateDescription($desc) {

        $sdesc = strip_tags($desc);
        if($sdesc != $desc)
            throw new Exception("Invalid Characters in Description");

        # Check for email addresses
        $toopersonal = false;
        $numbers = array('/ one /i','/ two /i','/ three /i', '/ four /i', '/ five /i', '/ six /i', '/ seven /i' , '/ eight /i', '/ nine /i', '/ zero /i', '/\D\d{4}\D/', '/\D\d{3}\D/', '/ phone /i');
        $strike = 0;
        foreach($numbers as $number) {
            //error_log($number);
            if(preg_match($number, $desc)) 
                $strike++;
        }
        if($strike > 0 )
            error_log("ABUSE: $strike Strikes $this->username");


        if(preg_match("/d0t/i", $desc) || preg_match("/@/i", $desc) || preg_match("/dot com/i", $desc) || preg_match("/at.*com /i", $desc)) {
            $toopersonal = true;
        }

        if($toopersonal || $strike > 3)
            throw new Exception("Please do not include direct contact information in the Description. It's unsafe and violates our terms of service.");

        error_log($desc.$sdesc);
        if(strlen($sdesc) < 20 ) {
            throw new Exception("Consider making your description longer. Longer descriptions receive more responses.");
        }
	}


	public function validateRentalFee($fee, $interval) {

        $validperiods = array("hour","day","weekend","week","month","year");
        $match = false;
        foreach($validperiods as $period) {
            error_log($period.':'. $interval);
            if($period === $interval) {
                $match = true;
                error_log("Matched $period");
                break;
            }
        }
        if(!$match)
            throw new Exception("Invalid Rental Period");

        $fee = strip_tags($fee);
        $fee = preg_replace('/\$/', '', $fee);
        if(!is_numeric($fee))
            throw new Exception("Invalid Fee");

        if($fee < 2.50)
            error_log("ABUSE: $this->username is charging a low fee");

        return $fee;
	}


    public function money($money) {
        return(round($money,2));
    }

	public function validateDeposit($deposit) {

        $deposit = preg_replace('/\$/', '', $deposit);
        $deposit = preg_replace('/ /', '', $deposit);
        if($deposit != $this->money($deposit))
            throw new Exception("Invalid Deposit Amount");

        if($deposit < 2.50 && $deposit )
            error_log("ABUSE: $this->username is charging a low deposit");

        return $deposit;
	}

	public function validateDeliveryOptions($deliveryoption, $locid, $radius, $deliveryfee) {

        $deliveryoption = preg_replace('/\$/', '', $deliveryoption);
        $deliveryoption = preg_replace('/ /', '', $deliveryoption);

        $locid = preg_replace('/\$/', '', $locid);
        $locid = preg_replace('/ /', '', $locid);

        $radius = preg_replace('/\$/', '', $radius);
        $radius = preg_replace('/ /', '', $radius);

        $deliveryfee = preg_replace('/\$/', '', $deliveryfee);
        $deliveryfee = preg_replace('/ /', '', $deliveryfee);

        $userzip = new Geocode('','','','',$locid);
        if($userzip->getLon() === 0)
            throw new Exception("A Valid Zip Code is Required");

        if($deliveryoption === 'pickuponly') {
            return array($deliveryoption, $locid, 0, 0);
        }


        if($deliveryoption != "deliveryrequired" && $deliveryoption != "deliveryavailable")
            throw new Exception('Please provide a valid delivery option');

        if($deliveryfee != $this->money($deliveryfee))
            throw new Exception("Invalid Delivery Fee");

        if($deliveryfee > 102.50)
            error_log("ABUSE: $this->username is charging a high delivery fee");

        return array($deliveryoption, $locid, $radius, $deliveryfee);
	}


	public function validateItemTax($tax, $taxstate) {

        $tax = preg_replace('/\%/', '', $tax);
        $tax = preg_replace('/ /', '', $tax);
        if($tax == '' && $taxstate == 'No Tax')
            $tax = 0;

        error_log("TAX: $taxstate");
        //if($taxstate == 'No Tax' && $tax == 0)
        if($tax == 0)
            return "0";

        if($taxstate == 'No Tax' && $tax != 0)
            throw new Exception("State must be defined when a Sales Tax is specified");

        $states = new States();
        if($states->GetStateRecord($taxstate, true) != false && $tax != 0 && is_numeric($tax))
            return($tax);


        throw new Exception("State must be defined when a Sales Tax is specified or not defined at all");
	}




}

?>
