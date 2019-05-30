<?php

include_once 'config.php';

class Session {

	private $conn = null;
	private $read_stmt = null;
	private $write_stmt = null;
	private $gc_stmt = null;
	private $delete_stmt = null;
	private $key_stmt = null;
	private $salt = SALT;

	function __construct() {
		session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));
		register_shutdown_function('session_write_close');


	}

	function start_session($session_name, $secure) {
	   	// Make sure the session cookie is not accessable via javascript.
		$httponly = true;
		
		// Hash algorithm to use for the sessionid. (use hash_algos() to get a list of available hashes.)
		$session_hash = 'sha512';
		
		// Check if hash is available
		if (in_array($session_hash, hash_algos())) {
			// Set the has function.
			ini_set('session.hash_function', $session_hash);
		}
		// How many bits per character of the hash.
		// The possible values are '4' (0-9, a-f), '5' (0-9, a-v), and '6' (0-9, a-z, A-Z, "-", ",").
		ini_set('session.hash_bits_per_character', 5);
		
		// Force the session to only use cookies, not URL variables.
		ini_set('session.use_only_cookies', 1);
		
		// Get session cookie parameters 
		$cookieParams = session_get_cookie_params(); 
		// Set the parameters
		session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
		// Change the session name 
		session_name($session_name);
		// Now we cat start the session
		session_start();
		// This line regenerates the session and delete the old one. 
		// It also generates a new encryption key in the database. 
		session_regenerate_id(true);    
	}

	function open() {
		try {
			$this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch ( Exception $e ) {
                        error_log('SESSSION EXCEPTION: '.$e->getMessage());
                        return false;
                }
		return true;
	}

	function close() {
		$this->conn = null;
	}

	function read($sessionid) {
		try {
			$sql = "SELECT data FROM sessions WHERE sessionid = :sessionid";
	
			if(!isset($this->read_stmt)) {
				$read_stmt = $this->conn->prepare( $sql );
			}
	
			$read_stmt->bindvalue(":sessionid", $sessionid, PDO::PARAM_STR);
	
			$read_stmt->execute();
	
			$numRows = $read_stmt->rowCount();
			if( $numRows != 1 ) return false;

			$data = $read_stmt->fetchColumn();
			$key = $this->getkey($sessionid);
			$data = $this->decrypt($data, $key);
                } catch ( Exception $e ) {
                        error_log('SESSSION EXCEPTION: '.$e->getMessage());
                        return false;
                }
		return $data;
	}

	function write($sessionid, $data) {
		//error_log("Write: ($data");
		try {
			$session_key = $this->getkey($sessionid);
			$data = $this->encrypt($data, $session_key);
	
			$set_time = time();
	
			$sql = "REPLACE INTO sessions (sessionid, set_time, data, session_key) VALUES (:sessionid, :set_time, :data, :session_key)";

			if(!isset($this->write_stmt)) {
				$write_stmt = $this->conn->prepare( $sql );
			}

			$write_stmt->bindvalue(":sessionid", $sessionid, PDO::PARAM_STR);
			$write_stmt->bindvalue(":set_time", $set_time, PDO::PARAM_STR);
			$write_stmt->bindvalue(":data", $data, PDO::PARAM_STR);
			$write_stmt->bindvalue(":session_key", $session_key, PDO::PARAM_STR);

			$write_stmt->execute();
                } catch ( Exception $e ) {
                        error_log('SESSSION EXCEPTION: '.$e->getMessage());
                        return false;
                }

		return true;
	}

	function destroy($sessionid) {
		try {
			$sql = "DELETE FROM sessions WHERE sessionid = :sessionid";

			if(!isset($this->delete_stmt)) {
				$delete_stmt = $this->conn->prepare( $sql );
			}

			$delete_stmt->bindvalue(":sessionid", $sessionid, PDO::PARAM_STR);

			$delete_stmt->execute();
        } catch ( Exception $e ) {
                error_log('SESSSION EXCEPTION: '.$e->getMessage());
                        return false;
        }

        error_log('SESSSION DESTROYED: '.$sessionid);
		return true;
	}

	function gc($max) {
		try {
			$sql = "DELETE FROM sessions WHERE set_time < :old";

			if(!isset($this->gc_stmt)) {
				$this->gc_stmt = $this->conn->prepare( $sql );
			}

			$old = time() - $max;
			$this->gc_stmt->bindvalue(":old", $old, PDO::PARAM_STR);

			$this->gc_stmt->execute();
                } catch ( Exception $e ) {
                        error_log('SESSSION EXCEPTION: '.$e->getMessage());
                        return $success;
                }


		return true;
	}


	private function getkey($sessionid) {
		try {
			$sql = "SELECT session_key FROM sessions WHERE sessionid = :sessionid";

			if(!isset($this->key_stmt)) {
				$key_stmt = $this->conn->prepare( $sql );
			}

			$key_stmt->bindvalue(":sessionid", $sessionid, PDO::PARAM_STR);

			$key_stmt->execute();

			$numRows = $key_stmt->rowCount();
			if( $numRows == 1 ) {
				$key = $key_stmt->fetchColumn();
			} else {
				$random_key = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
				return $random_key;
			}
                } catch ( Exception $e ) {
                        error_log('SESSSION EXCEPTION: '.$e->getMessage());
                        return $success;
                }
	}

	private function encrypt($data, $key) {
		return $data; // Fro debugging
		$key = substr(hash('sha256', SESSION_SALT.$key.SESSION_SALT), 0, 32);
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv));
		return $encrypted;
	}
	private function decrypt($data, $key) {
		return $data; // Fro debugging
		$key = substr(hash('sha256', SESSION_SALT.$key.SESSION_SALT), 0, 32);
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB, $iv);
		return $decrypted;
	}

}
	
