<?php

include_once 'config.php';

class Item {

	private $conn = null;
	private $aStates = null;

	function __construct() {
		try {
			$this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->read();
        } catch ( Exception $e ) {
            echo $e->getMessage();
            return false;
        }
		return true;
	}

	function close() {
		$this->conn = null;
	}

	function read() {
		try {
			$sql = "SELECT abbr,full FROM states";
	
			if(!isset($this->read_stmt)) {
				$read_stmt = $this->conn->prepare( $sql );
			}
	
			$read_stmt->execute();
	
			$numRows = $read_stmt->rowCount();
			if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve STATE list from Database");
            }

			$this->aStates = $read_stmt->fetchAll(PDO::FETCH_NUM);
        } catch ( Exception $e ) {
              echo $e->getMessage();
              return false;
         }
		return $this->aStates;
	}


	function GetStateRecord($state, $getabbr=true) {
    	try {
			$sql = "SELECT abbr,full FROM states where abbr = :abbr or full = :state";
			$stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":abbr", $state, PDO::PARAM_STR );
            $stmt->bindValue(":state", $state, PDO::PARAM_STR );
	
	
			$stmt->execute();
	
			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                error_log("Go screw you");
                return false;
            }

			$aStates = $stmt->fetch(PDO::FETCH_NUM);
            if($getabbr)
                return $aStates[0];
            else
                return $aStates[1];

        } catch ( Exception $e ) {
              error_log($e->getMessage());
              return false;
         }
	}

    function getStateOptions($selected = '') {
        foreach ( $this->aStates as $state ) {
            $abbr = $state[0];
            $full = $state[1];
            if(strcmp(strtoupper($abbr), strtoupper($selected))) {
                print '<option value="'.$abbr.'">'.$full."</option>\n";
            } else {
                print '<option selected value="'.$abbr.'">'.$full."</option>\n";
            }
        }
    }
}
