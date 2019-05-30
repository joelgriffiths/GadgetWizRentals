<?php

include_once 'config.php';
include_once 'validate.php';
include_once 'session.php';

// This is the fix for the old Users class which was the definition i
// of fubar.
class User {
	public $username = null;
	public $first = null;
	public $last = null;
	public $email = null;
	public $password = null;
	public $conpassword = null;
	public $code = null;
	public $userID = 99;
	public $created = null;

	// Default to false to protect against bugs
	public $activated = false;

	private $con = null;
	private $salt = SALT;

	// Try not to use this to import information. This could cause security issues.
	public function __construct( $userid = null, $username = null, $email = null ) {
		try {
			$this->con = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array( PDO::ATTR_PERSISTENT => true) );
			$this->con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if(is_numeric($userid) || $username !== null || $email !== null) {
			    $this->loadFromDB($userid, $username, $email);
            }
		} catch (Exception $e) {
			$this->con = null;
			error_log("User::Construct: ".print_r($e, true));
			return null;
		}

	}

	// Cannot update Username or UserID (Probably need to disable Name too)
	public function UpdateRecords( $data ) {
		$this->loadFromDB($this->userID);

		$val = new Validate();
		$this->first = isset($data['first']) && !empty($data['first']) && $val->validateFirstName($data['first']) == '' ? $data['first'] : $this->first;
		$this->last  = isset($data['last'])  && !empty($data['last']) && $val->validateLastName($data['last']) == '' ? $data['last'] : $this->last;
		$this->email = isset($data['email']) && !empty($data['email']) && $val->validateEmail($data['email'],$this->userID) == '' ? $data['email'] : $this->email;

		// Only check the password if new info provided
		if(isset($data['password']) || isset($data['conpassword'])) {
			if($this->checkPassword($data['curpassword'])) {
				$error = $val->validatePassword($data['password'],$data['conpassword']);
				$this->ChangePassword($data['password'], $this->userID);
			} else {
				throw new Exception("I'm sorry, your existing password was not correct");
			}
		}


		try {
			$sql = "UPDATE users SET `first`=:first, `last`=:last, `email`=:email WHERE userID=:userID";

			$stmt = $this->con->prepare( $sql );
			$stmt->bindvalue(":userID", $this->userID, PDO::PARAM_INT);
			$stmt->bindvalue(":first", $this->first, PDO::PARAM_STR);
			$stmt->bindvalue(":last", $this->last, PDO::PARAM_STR);
			$stmt->bindvalue(":email", $this->email, PDO::PARAM_STR);

			$stmt->execute();

		} catch (PDOException $e) {
			error_log($e->getMessage());
			return null;
		} catch (Exception $e) {
			$this->con = null;
			error_log("UpdateRecords: ".print_r($e, true));
			return null;
		}

	}

	public function __destruct() {
		$this->con = null;
	}

	public function storeFormValues( $params ) {
		$this->__construct( $params );
	}

	public function getCreated() {
        error_log("Created: " . $this->created);
		return $this->created;
	}

	public function getFirstName() {
		return $this->first;
	}

	public function getLastName() {
		return $this->last;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getUserID() {
		return $this->userID;
	}

	public function userIDKey() {
		try {
			$sql = "SELECT * FROM users WHERE userID = :userID and code = :code";

			$stmt = $this->con->prepare( $sql );
			$stmt->bindvalue(":code", $this->code, PDO::PARAM_STR);
			$stmt->bindvalue(":userID", $this->userID, PDO::PARAM_INT);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows == 1 ) $success = true;

			
		} catch (Exception $e) {
			error_log(print_r($e, true));
			$success = false;
		}

		return $success;
	}

	public function loadFromDB($userID = 0, $username = '',$email = '') {
		$success = false;
		try {

			if ($email) {
				$sql =  "SELECT username,first,last,email,password,code,userID,created ".
					"FROM users WHERE email = :email";
				$stmt = $this->con->prepare( $sql );
				$stmt->bindvalue(":email", $email, PDO::PARAM_STR);
			} elseif ($userID && $username) {
				$sql =  "SELECT username,first,last,email,password,code,userID,created ".
					"FROM users WHERE userID = :userID and username = :username";
				$stmt = $this->con->prepare( $sql );
				$stmt->bindvalue(":username", $username, PDO::PARAM_STR);
				$stmt->bindvalue(":userID", $userID, PDO::PARAM_INT);
			} elseif ($userID) {
				$sql =  "SELECT username,first,last,email,password,code,userID,created ".
					"FROM users WHERE userID = :userID";
				$stmt = $this->con->prepare( $sql );
				$stmt->bindvalue(":userID", $userID, PDO::PARAM_INT);
			} elseif ($username) {
				$sql =  "SELECT username,first,last,email,password,code,userID,created ".
					"FROM users WHERE username = :username";
				$stmt = $this->con->prepare( $sql );
				$stmt->bindvalue(":username", $username, PDO::PARAM_STR);
			} else {
				throw new Exception("Nothing to see here. Move along");
			}

			$eres = $stmt->execute();
			$aResult =  $stmt->fetch();

			$numRows = $stmt->rowCount();
			if( $numRows != 1 ) return false;

			list($this->username, $this->first, $this->last, $this->email,
                 $this->password, $this->code, $this->userID, $this->created) = $aResult;

			return true;
		} catch ( Exception $e) {
			error_log("loadFromDB(): ".$e->getMessage());
			throw($e);
		}
		// Should never reach this line
		return $success;
	}

	public function checkuid($userID) {
		try {
			// Da query
			$sql = "SELECT activated FROM users WHERE userID = :userID";

			$stmt = $this->con->prepare( $sql );
			$stmt->bindvalue(":userID", $userID, PDO::PARAM_INT);

			$stmt->execute();

			// Probably redundant - JLG
			$numRows = $stmt->rowCount();
			if( $numRows == 1 ) {
				$this->userID = $userID;
				return true;
			}

			return false;
		} catch ( PDOException $e) {
			error_log($e->getMessage());
			return $success;
		} catch ( Exception $e ) {
			error_log($e->getMessage());
			return $success;
		}
		// Should never reach this line
		return false;
	}


	// Is userid valid and active
	public function checkstate() {
		$success = false;
		try {
			// Da query
			$sql = "SELECT activated FROM users WHERE userID = :userID";

			$stmt = $this->con->prepare( $sql );
			$stmt->bindvalue(":userID", $this->userID, PDO::PARAM_INT);

			$stmt->execute();

			// Probably redundant - JLG
			$numRows = $stmt->rowCount();
			if( $numRows == 1 ) {
				$valid = $stmt->fetchColumn();
				if ( $valid ) $success = true;
			}
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		// Should never reach this line
		return $success;
	}

	public function activate($flag = true) {
		$success = false;
		try {
			// Da query
			$this->activated = $flag;
			$sql = "UPDATE users set activated=$flag WHERE userID = :userID and code = :code";

			$stmt = $this->con->prepare( $sql );
			$stmt->bindvalue(":code", $this->code, PDO::PARAM_STR);
			$stmt->bindvalue(":userID", $this->userID, PDO::PARAM_INT);

			$result = $stmt->execute();
			error_log('REESULT: '.$result);

			$numRows = $stmt->rowCount();
			if( $numRows == 1 ) $success = true;

			return $success;
		} catch ( PDOException $e) {
			echo $e->getMessage();
			throw($e);
			return $success;
		} catch ( Exception $e ) {
			echo $e->getMessage();
			throw($e);
			return $success;
		}
		// Should never reach this line
		return false;
	}

	public function deactivate() {
		activate(false);
	}
	
	public function checkbrute($userID) {
		$now = time();
		$valid_attempts = $now - (2*60*60); // 2 hours

		$sql = "SELECT time from login_attempts WHERE userID = :userID";
		$stmt = $this->con->prepare($sql);
		$stmt->bindValue(":userID", $userID);
		$stmt->execute();

		if($stmt->rowCount() > 500) {
			return true;
		} else {
			return false;
		}
	}

	public function login_check() {
	 // Check if all session variables are set
	 if(isset($_SESSION['userID'], $_SESSION['username'], $_SESSION['login_string'])) {
		$userID = $_SESSION['userID'];
		$login_string = $_SESSION['login_string'];
		$username = $_SESSION['username'];
 
		$user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.

		if ($stmt = $this->con->prepare("SELECT password FROM users WHERE activated = true and userID = :userID LIMIT 1")) { 
				$stmt->bindValue(":userID", $userID);
				$stmt->execute();
 
				if($stmt->rowCount() == 1) { // If the user exists
					$stmt->bindValue(":userID", $userID);
					$stmt->execute();
					$password = $stmt->fetchColumn();
					$login_check = hash('sha512', $password.$user_browser);
					 if($login_check == $login_string) {
							$this->loadFromDB($userID);
							//error_log(print_r($this,true));
							return true;
					 } else {
							return false;
					 }
				} else {
						// Not logged in
						return false;
				}
		} else {
			// Not logged in
			return false;
		}
	} else {
		 // Not logged in
		//error_log("Not logged in");
		return false;
	}
	}

	private function checkPassword($password) {
		try {
			// Da query
			$sql = "SELECT userID, username, password, salt FROM users WHERE userID = :userID";

			$stmt = $this->con->prepare( $sql );
			$stmt->bindvalue(":userID", $this->userID, PDO::PARAM_STR);

			$stmt->execute();

			$aResult =  $stmt->fetch();
			list($dbuserID, $dbusername, $dbpassword, $dbsalt) = $aResult;

			$this->salt  = ($dbsalt != null) ? $dbsalt : SALT;
			$hash_pw = hash("SHA256", $password . $this->salt);
error_log($hash_pw.' : '. $this->salt.' : '.$password);
			$numRows = $stmt->rowCount();
			if( $numRows == 1 ) {
				if($password != '' and $dbpassword === $hash_pw) {
					return true;
				} 
			} else return false;
		} catch ( PDOException $e) {
			error_log($e->getMessage());
			throw new Exception("Internal Database Error");
		} catch ( Exception $e ) {
			error_log($e->getMessage());
			throw($e);
		}
		return false;
	}


	public function userLogin($username, $password) {
		try {
			// Da query
			$sql = "SELECT userID, username, password, salt, activated FROM users WHERE username = :username OR email = :email";

			$stmt = $this->con->prepare( $sql );
			$stmt->bindvalue(":username", $username, PDO::PARAM_STR);
			$stmt->bindvalue(":email", $username, PDO::PARAM_STR);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows > 1 ) throw new Exception('CRITICAL: Corrupt User Table. '.$numRows.' rows');


			$aResult =  $stmt->fetch();
			list($dbuserID, $dbusername, $dbpassword, $dbsalt, $activated) = $aResult;

			$this->salt  = ($dbsalt != null) ? $dbsalt : SALT;
			$hash_pw = hash("SHA256", $password . $this->salt);
			if( $numRows == 1 ) {
				if($this->checkbrute($this->userID) === true) {
					// Account is locked
					throw new Exception("Your Account has been locked. Call 530-388-5635 to restore your account.");
				} elseif($activated == 0) {
					throw new Exception("Your Account has not been activated yet. Please check your email.");
				} elseif($password != '' and $dbpassword === $hash_pw) {
					$user_browser = $_SERVER['HTTP_USER_AGENT'];

					$_SESSION['userID'] = preg_replace("/[^0-9]+/", "", $dbuserID);
					$_SESSION['username'] = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);
					$_SESSION['login_string'] = hash('sha512', $dbpassword.$user_browser);
					//error_log($_SESSION['username']." $username is LOGGED IN");
				} elseif($password == '') {
					// Don't count empty passwords
					throw new Exception("Ooops. Somebody forgot to enter a password.");
				} else {
					$now = time();
					$sql = "INSERT INTO login_attempts (userID, time, REMOTE_ADDR) VALUES (:userID, :now, :remote_addr)";
					$stmt = $this->con->prepare( $sql );
					$stmt->bindvalue(":userID", $dbuserID, PDO::PARAM_INT);
					$stmt->bindvalue(":now", $now, PDO::PARAM_STR);
					$stmt->bindvalue(":remote_addr", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
					$stmt->execute();
					// Hacking attempt?
					throw new Exception("Please Provide a valid Email/Password");
				}
			} else throw new Exception("Please Provide a valid E-mail/Password");
		} catch ( PDOException $e) {
			error_log($e->getMessage());
			throw new Exception("Internal Database Error");
		}
		return true;
	}

	public function validateReg() {
		$error = '';
		$val = new Validate();
		try {
			$error .= $val->validateUsername($this->username);
			$error .= $val->validateEmail($this->email);	
			$error .= isset($data['first']) && !empty($data['first']) && $val->validateFirstName($this->first);	
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

	public function register() {
		try {
			
			$this->validateReg();

			$this->con->beginTransaction();
			$sql = "INSERT INTO users(username, first, last, email, password, activated, code) VALUES( :username, :first, :last, :email, :password, :activated, :code )";

			$stmt = $this->con->prepare( $sql );
			$stmt->bindValue( ":username", $this->username, PDO::PARAM_STR );
			$stmt->bindValue( ":first", $this->first, PDO::PARAM_STR );
			$stmt->bindValue( ":last", $this->last, PDO::PARAM_STR );
			$stmt->bindValue( ":email", $this->email, PDO::PARAM_STR );
			$stmt->bindValue( ":password", hash("SHA256", $this->password . $this->salt), PDO::PARAM_STR );
			$stmt->bindValue( ":activated", false, PDO::PARAM_BOOL );

			$this->code = substr(md5(rand()), 5, 25);
			$stmt->bindValue( ":code", $this->code, PDO::PARAM_STR );

			if ($stmt->execute() === true) {
				$this->userID = $this->con->lastInsertID("userID");
				if($this->userID > 99) {
					$this->con->commit();
				}
			} else {
				error_log("rollback User: $this->userID");
				$this->con->rollBack();
				throw new Exception("Rolling Back user  $this->userID");
			}
		} catch ( PDOException $e ) {
			error_log('register(): '.print_r($e->getMessage(), true));
			$this->con->rollback();
			throw($e);
		} catch ( Exception $e ) {
			error_log('register(): '.print_r($e->getMessage(), true));
			$this->con->rollback();
			throw($e);
		}
	}


	public function resetCode($email) {
		try {
			
			//$this->validateReg();

			$sql = "UPDATE users set code = :code where email = :email";

			$stmt = $this->con->prepare( $sql );

			$this->code = substr(md5(rand()), 5, 25);
			$stmt->bindValue( ":code", $this->code, PDO::PARAM_STR );
			$stmt->bindValue( ":email", $email, PDO::PARAM_STR );

			$stmt->execute();
		} catch ( PDOException $e ) {
			error_log('register(): '.print_r($e->getMessage(), true));
			$this->con->rollback();
			throw($e);
		} catch ( Exception $e ) {
			error_log('register(): '.print_r($e->getMessage(), true));
			$this->con->rollback();
			throw($e);
		}
	}

	private function ChangeRegKey($userID) {
		try {

			$sql = "UPDATE users set code=:code where userID=:userID";
			$stmt = $this->con->prepare( $sql );
			$this->code = substr(md5(rand()), 5, 25);
			$stmt->bindValue( ":code", $this->code, PDO::PARAM_STR );
			$stmt->bindValue( ":userID", $userID, PDO::PARAM_INT );

			$stmt->execute();

			if($stmt->rowCount() === 1)
				return $this->code;
			else
				throw new Exception("Cannot create Registration Key");

		} catch(Exception $e) {
			error_log("createRegKey(): " . $e->getMessage());
			throw($e);
		}


	}

    
    // Do not call this function without validation
    public function ChangePassword($password, $userID, $email = '') {
        try {

            $error = '';
            $val = new Validate();
            if($val->validatePassword($password,$password) != '') {
                throw new Exception("Invalid Password. Would be a nicer message if you weren't a hacker.");
            }
            $sql = "UPDATE users set password=:password where userID=:userID";
            $stmt = $this->con->prepare( $sql );


            $this->salt  = ($this->salt != null) ? $this->salt : SALT;
            $this->password = hash("SHA256", $password . $this->salt);

            $stmt->bindValue( ":password", $this->password, PDO::PARAM_STR );
            $stmt->bindValue( ":userID", $userID, PDO::PARAM_INT );

            $stmt->execute();

            if($email) {
                $this->userLogin($email, $password);
            } else {
                $this->userLogin($this->username, $password);
            }
    
        } catch(Exception $e) {
           error_log("ChangePassword: " . $e->getMessage());
           throw($e);
        }


    }



	public function sendRegEmail() {
		//$my_email = '"' . SHORTDOM . '" <'.SUPPORT_EMAIL.'>';
		$my_email = SUPPORT_EMAIL;
		$my_email = '"'.SUPPORT_EMAIL.'"';
		$domain = $_SERVER["HTTP_HOST"];
		$activation = 'http://'.$domain.'/activation.php?userID='.$this->userID.'&code='.$this->code;
		$shortdom = SHORTDOM;
		$boundary = uniqid('np');

		$subject = "Welcome to ".trim(LONGDOM)."! One Step Left";

		$headers = '';
		$headers .= 'MIME-Version: 1.0' . "\n";
		$headers .= 'From: ' . SUPPORT_EMAIL . "\n";
		$headers .= 'Content-Type: multipart/alternative;boundary=' . $boundary . "\n";

		// MIME
		$message = 'This is a MIME encoded message.'."\n";;

		// Text
		$message .= "\n\n--" . $boundary . "\n";
		$message .= 'Content-type: text/plain;charset=utf-8'."\n\n";
		$message .= 'Hi ' . $this->first .":\n";
		$message .= 'Welcome to ' . $shortdom . "!\n";
		$message .= 'You\'re almost done.' . "\n";
		$message .= 'Go to '.$activation.' to activate your account.'."\n";
		$message .= "\n\n--" . $boundary . "\n";
		$message .= "Content-type: text/html;charset=utf-8\n\n";
		$message .= "<html><body><img src='http://$domain/images/welcometo.png' alt='Welcome to $shortdom' /> <table rules='all' style='border-color: #666;' cellpadding='10'> <tr style='background: #eee;'><td><strong>You're almost done $this->first.</strong></td><td style='font-size: 120%;font-weight:bold;'><strong><a href='$activation'>Activate Account</a></strong></td></tr><tr><td colspan=2>$activation</td></tr></table> </body></html>\n";
		$message .= "\n\n--" . $boundary . "--";

		$foo = mail($this->email, $subject, $message, $headers, "-f".$my_email);
		error_log("mail($this->email, $subject, $message, $headers, \"-f\".$my_email);");
		mail('newaccount@zalaxy.com', $subject, $message, $headers, "-f".$my_email);
		if( ! $foo )
		    throw new Exception("Account Created, but email could not be sent");
	}

	public function sendPasswordResetEmail($email) {
        $exists = $this->loadFromDB(0,'',$email);
        if(!$exists) return;

        $this->resetCode($email);
		$my_email = '"' . SHORTDOM . '" <'.SUPPORT_EMAIL.'>';
		$my_email = '"'.SUPPORT_EMAIL.'"';
		$domain = $_SERVER["HTTP_HOST"];
		$resetlink = 'http://'.$domain.'/resetpassword.php?userID='.$this->userID.'&code='.$this->code.'&email='.$this->email;
		$shortdom = SHORTDOM;
		$boundary = uniqid('np');

		$subject = "Password Reset Requested for ".trim(LONGDOM)."! One Step Left";

		$headers = '';
		$headers .= 'MIME-Version: 1.0' . "\n";
		$headers .= 'From: ' . SUPPORT_EMAIL . "\n";
		$headers .= 'Content-Type: multipart/alternative;boundary=' . $boundary . "\n";

		// MIME
		$message = 'This is a MIME encoded message.'."\n";;

		// Text
		$message .= "\n\n--" . $boundary . "\n";
		$message .= 'Content-type: text/plain;charset=utf-8'."\n\n";
		$message .= 'Hi ' . $this->first .":\n";
		$message .= 'Welcome to ' . $shortdom . "!\n";
		$message .= 'You\'re almost done.' . "\n";
		$message .= 'Go to '.$resetlink.' to reset your password.'."\n";
		$message .= "\n\n--" . $boundary . "\n";
		$message .= "Content-type: text/html;charset=utf-8\n\n";
		$message .= "<html><body><img src='http://$domain/images/welcometo.png' alt='Welcome to $shortdom' /> <table rules='all' style='border-color: #666;' cellpadding='10'> <tr style='background: #eee;'><td><strong>You're almost done $this->first.</strong></td><td style='font-size: 120%;font-weight:bold;'><strong><a href='$resetlink'>Reset Password</a></strong></td></tr><tr><td colspan=2>$resetlink</td></tr></table> </body></html>\n";
		$message .= "\n\n--" . $boundary . "--";

		$foo = mail($this->email, $subject, $message, $headers, "-f".$my_email);
		error_log("mail($this->email, $subject, message, $headers, \"-f\".$my_email);");
		//mail('passwordreset@zalaxy.com', $subject, $message, $headers, "-f".$my_email);
		if( ! $foo )
		    throw new Exception("Email could not be sent");
	}



}

?>
