<?php

include_once 'config.php';
include_once 'Item.php';
include_once "cGeocode.php";
include_once "user.php";
include_once "userinfo.php";
include_once "timezone.php";


class Feedback {

    private $conn = null;

    private $resid = null;
    private $userid = null;
    private $role = null;

    private $newfb = null;

    private $otherid = null;
    private $fbscore = null;
    private $fbtext = null;

    /*
     * Reservation ID: dug
     * userid: ID of buyer or seller
     * role: enum 'buyer' or 'seller'
     * Primary Key - (`resid`, `userid`, `role`)
     */
    public function __construct($resid, $userid, $role) {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->resid = $resid;
            $this->userid = $userid;
            $this->role = $role;
            $this->read($resid, $userid, $role);
        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Internal Database Error");
        }
 
    }

    public function __destruct() {
        $this->conn = null;
    }

    public function save2DB() {
        try {
            if($this->otherid == null || $this->fbtext == null ||  $this->fbscore == null) {
                throw new Exception("Not enough information for the DB");
            }

            // I've only tested the INSERT so far
            if($this->newfb === true) {
                $sql = 'INSERT INTO feedback (`resid`, `userid`, `role`, `otherid`, `fbscore`, `fbtext`) VALUES (:resid, :userid, :role, :otherid, :fbscore, :fbtext)';
            } else  {
                $sql = 'UPDATE feedback set `fbscore`=:fbscore, `fbtext`=:fbtext, `otherid`=:otherid WHERE `resid`=:resid and `userid`=:userid and `role`=:role';
            }

            error_log($sql);
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":resid", $this->resid, PDO::PARAM_INT );
            $stmt->bindValue(":userid", $this->userid, PDO::PARAM_INT );
            $stmt->bindValue(":role", $this->role, PDO::PARAM_STR );

            $stmt->bindValue(":otherid", $this->otherid, PDO::PARAM_INT );
            $stmt->bindValue(":fbtext", $this->fbtext, PDO::PARAM_STR );
            $stmt->bindValue(":fbscore", $this->fbscore, PDO::PARAM_INT );

            $stmt->execute();
        } catch (PDOException $e) {
            error_log("DB Exception for feedback write".$e->getMessage());
            throw new Exception("DB Exception");
        } catch (Exception $e) {
            error_log("Cannot write feedback to database".print_r($this,true));
            throw new Exception("Cannot write feedback to database:".$e->getMessage());
        }
    }

    function read() {
        try {

            $sql = "SELECT `fbscore`, `fbtext`, `otherid` FROM feedback where resid=:resid and userid=:userid and role=:role";
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":resid", $this->resid, PDO::PARAM_INT );
            $stmt->bindValue(":userid", $this->userid, PDO::PARAM_INT );
            $stmt->bindValue(":role", $this->role, PDO::PARAM_STR );

            $stmt->execute();
    
            $numRows = $stmt->rowCount();
            if( $numRows == 0 ) {
                // New Feedback
                $this->newfb = true;
                return;
            } else {
                $this->newfb = false;
            }

            list($this->fbscore, $this->fbtext, $this->otherid)
                  = $stmt->fetch(PDO::FETCH_NUM);

        } catch ( PDOException $e ) {
            error_log("Reservation read DB error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('RESERVATION: '.$this->resid .':'.$e->getMessage());
            throw($e);
         }
        return true;
    }

    public function setFeedback($fbtext=null) {
        if($fbtext != null) {
            $this->fbtext = $fbtext;
        }
    }

    public function setFeedbackScore($fbscore=null) {
        if($fbscore != null) {
            $this->fbscore = $fbscore;
        }
    }

    public function setOtherID($otherid=null) {
        if($otherid != null) {
            $this->otherid = $otherid;
        }
    }
 
    public function getOtherID($otherid=null) {
        if($otherid != null) {
            $this->otherid = $otherid;
        }
        return $this->otherid;
    }
 
    public function getFeedback($fbtext=null) {
        if($fbtext != null) {
            $this->fbtext = $fbtext;
        }
        return $this->fbtext;
    }

    public function getFeedbackScore($score=null) {
        if($score != null) {
            $this->fbscore = $score;
        }
        return $this->fbscore;
    }

    public function getRole() {
        return $this->role;
    }


    public function checkExists() {
        $existing = $this->newfb == true ? false : true;
        return $existing;
    }

}

?>
