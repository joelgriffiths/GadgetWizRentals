<?php

include_once 'config.php';

class Users {
	public $username = null;
	public $first = null;
	public $last = null;
	public $email = null;
	public $password = null;
	public $conpassword = null;
	public $salt = "zO4Ru5z1yYkjaasy0pt6eUg7BBYdlEhPaNLuXaWu8lQU1ElzHv0Ri7EM6IRpx5W";

	public function __construct( $data = array() ) {
		//error_log(print_r($data['username'],true));
		if( isset( $data['username'] ) )
			$this->username = stripslashes( strip_tags( $data['username'] ) );

		if( isset( $data['first'] ) )
			$this->first = stripslashes( strip_tags( $data['first'] ) );

		if( isset( $data['last'] ) )
			$this->last = stripslashes( strip_tags( $data['last'] ) );

		if( isset( $data['email'] ) )
			$this->email = stripslashes( strip_tags( $data['email'] ) );

		if( isset( $data['password'] ) )
			$this->password = stripslashes( strip_tags( $data['password'] ) );

		if( isset( $data['conpassword'] ) )
			$this->conpassword = stripslashes( strip_tags( $data['conpassword'] ) );

		//error_log(print_r($this,true));
	}

	public function storeFormValues( $params ) {
		$this->__construct( $params );
	}

	public function userLogin() {
		$success = false;
		try {
			// Create our PDO object
			$con = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
			
			// How will PDO handle errors?
			$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Da query
			$sql = "SELECT * FROM users WHERE username = :username and password = :password";

			$stmt = $con->prepare( $sql );
			$stmt->bindvalue("username", $this->username, PDO::PARAM_STR);
			$stmt->bindvalue("password", hash("SHA256", $this->password . $this->salt), PDO::PARAM_STR);

			$stmt->execute();

			//$numRows = $stmt->columnCount();
			//if( $numRows > 1 ) throw new Exception('CRITICAL: Corrupt User Table. '.$numRows.' rows');

			$valid = $stmt->fetchColumn();
			if ( $valid ) $success = true;

			$con = null;
			return $success;
		} catch ( PDOException $e) {
			echo $e->getMessage();
			return $success;
		} catch ( Exception $e ) {
			echo $e->getMessage();
			return $success;
		}

	}

	public function checkUsername() {

		$success = false;
		try {
			// Create our PDO object
			$con = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
			
			// How will PDO handle errors?
			$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Da query
			$sql = "SELECT * FROM users WHERE username = :username";

			$stmt = $con->prepare( $sql );
			$stmt->bindvalue("username", $this->username, PDO::PARAM_STR);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows != 0 ) throw new Exception("Username not available<br />");

			if (strlen($this->username) > MAX_USERNAME_LEN)
				throw new Exception("Username cannot exceed " . MAX_USERNAME_LEN . " characters<br />");
	

			if (strlen($this->username) < MIN_USERNAME_LEN)
				throw new Exception("Username must be at least " . MIN_USERNAME_LEN ." characters<br />");
	
			if (!ctype_alnum($this->username)) 
				throw new Exception("Username may only contain alphnumeric aracters<br />");

			$con = null;
			return $success;
		} catch ( PDOException $e) {
			error_log($e->getMessage());
			return("PDO Error");
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	function checkEmail() {
		$isValid = true;
		$atIndex = strrpos($this->email, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else {
			$domain = substr($this->email, $atIndex+1);
			$local = substr($this->email, 0, $atIndex);
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
				// Create our PDO object
				$con = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
				
				// How will PDO handle errors?
				$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				// Da query
				$sql = "SELECT * FROM users WHERE email = :email";

				$stmt = $con->prepare( $sql );
				$stmt->bindvalue("email", $this->email, PDO::PARAM_STR);

				$stmt->execute();

				$numRows = $stmt->rowCount();
				if( $numRows != 0 ) throw new Exception("Email address is not available<br />");
			} catch ( PDOException $e) {
				error_log($e->getMessage());
				return("PDO Error");
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
	public function checkFirstName() {

		if (strlen($this->first) == 0)
			return("First Name is required<br />");
		
		if (strlen($this->first) > MAX_NAME_LEN)
			return("First name cannot exceed " . MAX_NAME_LEN . " characters<br />");
	

		if (strlen($this->first) < MIN_NAME_LEN)
			return("First name must be at least " . MIN_NAME_LEN ." characters<br />");
	
		if (!preg_match("#^[a-zA-Z]+$#", $this->first))
			return("First Name: Can only contain letters<br />");
		
	}


	public function checkLastName() {


		if (strlen($this->last) == 0)
			return("Last Name is required<br />");
		
		if (strlen($this->last) > MAX_NAME_LEN)
			return("Last name cannot exceed " . MAX_NAME_LEN . " characters<br />");
	

		if (strlen($this->last) < MIN_NAME_LEN)
			return("Last name must be at least " . MIN_NAME_LEN ." characters<br />");
	
		if (!preg_match("#^[a-zA-Z]+$#", $this->last))
			return("Last Name: Can only contain letters<br />");
		
	}


	public function checkPassword() {

		if( $this->password !== $this->conpassword )
			return("Passwords do not match<br />");

		if( trim($this->password) !== $this->password )
			return("Trailing spaces not allowed<br />");

		if (strlen($this->password) < 6)
			return("Password must be at least 6 characters<br />");
		
		if (!preg_match("#[0-9]+#", $this->password))
			return("Password must include at least one number!<br />");
		
		if (!preg_match("#[a-zA-Z]+#", $this->password))
			return("Password must include at least one letter!<br />");

	}

	public function validateReg() {
		$error = '';
		try {
			$error .= $this->checkUsername();
			$error .= $this->checkEmail();	
			$error .= $this->checkFirstName();	
			$error .= $this->checkLastName();	
			$error .= $this->checkPassword();	
                } catch ( PDOException $e) {
                        error_log($e->getMessage());
                        return "Database Error";
                } catch ( Exception $e ) {
                        error_log($e->getMessage());
			throw($e);
                }

		return $error;
	}

	public function register() {
		$correct = false;
		try {
			$con = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
			$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

			$sql = "INSERT INTO users(username, first, last, email, password) VALUES( :username, :first, :last, :email, :password )";

			$stmt = $con->prepare( $sql );
			$stmt->bindValue( "username", $this->username, PDO::PARAM_STR );
			$stmt->bindValue( "first", $this->first, PDO::PARAM_STR );
			$stmt->bindValue( "last", $this->last, PDO::PARAM_STR );
			$stmt->bindValue( "email", $this->email, PDO::PARAM_STR );
			$stmt->bindValue( "password", hash("SHA256", $this->password . $this->salt), PDO::PARAM_STR );
			if ($stmt->execute() === true)
			 	return "Registration Successful <br /> <a href='/'>Login Now</a>";
			else
				throw new Exception("Database Error<br />");
		} catch ( PDOException $e ) {
			error_log(print_r($e->getMessage(), true));
			throw new Exception("PDO Error<br />");
		}
	}

	function check_username($username) {
		$username = trim($username); // strip any white space
		$response = array(); // our response
	
		// if the username is blank
		if (!$username) {
			$response = array(
				'ok' => false,
				'msg' => "Please specify a username");

		// if the username does not match a-z or '.', '-', '_' then it's not valid
		} else if (!preg_match('/^[a-z0-9.-_]+$/', $username)) {
			$response = array(
				'ok' => false,
				'msg' => "Your username can only contain alphanumerics and period, dash and underscore (.-_)");
	
		// this would live in an external library just to check if the username is taken
		} else if (username_taken($username)) {
			$response = array(
				'ok' => false,
				'msg' => "The selected username is not available");
	
		// it's all good
		} else {
			$response = array(
				'ok' => true,
				'msg' => "This username is free");
		}
	
		return $response;
	}

}

?>
