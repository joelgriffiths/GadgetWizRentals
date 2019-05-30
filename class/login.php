<?php

include "user.php";
include_once "session.php";


class login {

        function __construct() {
                try {
                        $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
                        $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch ( Exception $e ) {
                        echo $e->getMessage();
                        return false;
                }
                return true;
        }

	public function login($username, $password) {
		$sql = "SELECT userID, username, password, salt FROM users username id = :username";
		
		if(!isset($this->read_stmt)) {
		        $read_stmt = $this->conn->prepare( $sql );
		}
		
		$read_stmt->bindvalue(":id", $id, PDO::PARAM_STR);
		
		$read_stmt->execute();
		
		$numRows = $read_stmt->rowCount();
		if( $numRows != 1 ) return false;
	

   // Using prepared Statements means that SQL injection is not possible. 
   if ($stmt = $mysqli->prepare("SELECT id, username, password, salt FROM members WHERE email = ? LIMIT 1")) { 
      $stmt->bind_param('s', $email); // Bind "$email" to parameter.
      $stmt->execute(); // Execute the prepared query.
      $stmt->store_result();
      $stmt->bind_result($user_id, $username, $db_password, $salt); // get variables from result.
      $stmt->fetch();
      $password = hash('sha512', $password.$salt); // hash the password with the unique salt.
 
      if($stmt->num_rows == 1) { // If the user exists
         // We check if the account is locked from too many login attempts
         if(checkbrute($user_id, $mysqli) == true) { 
            // Account is locked
            // Send an email to user saying their account is locked
            return false;
         } else {
         if($db_password == $password) { // Check if the password in the database matches the password the user submitted. 
            // Password is correct!
 
 
               $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
 
               $user_id = preg_replace("/[^0-9]+/", "", $user_id); // XSS protection as we might print this value
               $_SESSION['user_id'] = $user_id; 
               $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); // XSS protection as we might print this value
               $_SESSION['username'] = $username;
               $_SESSION['login_string'] = hash('sha512', $password.$user_browser);
               // Login successful.
               return true;    
         } else {
            // Password is not correct
            // We record this attempt in the database
            $now = time();
            $mysqli->query("INSERT INTO login_attempts (user_id, time) VALUES ('$user_id', '$now')");
            return false;
         }
      }
      } else {
         // No user exists. 
         return false;
      }
   }
}

	}
}
