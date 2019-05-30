<?php

include_once 'config.php';

class ShortIPN {

    private $conn = null;
    private $orderid = null;
    private $test_ipn = null;
    private $reservationid = null;
    private $userid = null;
    private $txn_id = null;
    private $payer_email = null;
    private $mc_gross = null;

    function __construct($orderid = 0, $txn_id = 0) {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->orderid = $orderid;
            $this->txn_id = $txn_id;
            if($orderid || $txn_id)
                $this->read();

        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Cannot retrieve order Database");
        }
        return true;
    }

    function close() {
        $this->conn = null;
    }

    function read() {
        try {
            $sql = "SELECT orderid,test_ipn,reservationid,userid,txn_id,payer_email,mc_gross FROM orders where orderid=:orderid OR txn_id=:txn_id";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":orderid", $this->orderid, PDO::PARAM_INT );
            $read_stmt->bindValue(":txn_id", $this->txn_id, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve transaction from the Database");
            }

            list($this->orderid,
                 $this->test_ipn,
                 $this->reservationid,
                 $this->userid,
                 $this->txn_id,
                 $this->payer_email,
                 $this->mc_gross) =
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

    function checkDuplicateTXN($txn_id) {
        try {
            $sql = "SELECT txn_id FROM orders where txn_id=:txn_id";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":txn_id", $this->txn_id, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows >= 1 ) {
                return true;
            }

        } catch ( PDOException $e ) {
            error_log("checkDuplicateTXN DB read error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('checkDuplicateTXN: '.$e->getMessage());
            throw($e);
         }
        return false;
    }

    public function recordPayment($reservationid, $userid, $txn_id, $payer_email, $mc_gross, $test_ipn) {
        try {
            $sql = "INSERT INTO orders (test_ipn,reservationid,userid,txn_id,payer_email,mc_gross) VALUES (:test_ipn, :reservationid, :userid,:txn_id,:payer_email,:mc_gross)";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":reservationid", $reservationid, PDO::PARAM_INT );
            $read_stmt->bindValue(":test_ipn", $test_ipn, PDO::PARAM_INT );
            $read_stmt->bindValue(":userid", $userid, PDO::PARAM_INT );
            $read_stmt->bindValue(":txn_id", $txn_id, PDO::PARAM_STR );
            $read_stmt->bindValue(":payer_email", $payer_email, PDO::PARAM_STR );
            $read_stmt->bindValue(":mc_gross", $mc_gross, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $this->orderid = $this->conn->lastInsertID('orderid');

            // Extra work to load variables but less trouble prone.
            $this->read();

        } catch ( PDOException $e ) {
            error_log("aGeocode read error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('ZIP: '.$this->zip .':'.$e->getMessage());
            throw($e);
         }
        return $this->orderid;
    }

    public function getPaymentAmount() {
        //error_log(print_r($this, true));
        return $this->mc_gross;
    }
        
}
