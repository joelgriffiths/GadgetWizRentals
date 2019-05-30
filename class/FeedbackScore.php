<?php

include_once 'config.php';

class FeedbackScore {

    private $conn = null;

    private $userid = null;
    private $buyerscore = null;
    private $sellerscore = null;
    private $totalscore = null;
    private $buyercount = null;
    private $sellercount = null;
    private $totalcount = null;

    /*
     * Reservation ID: dug
     * userid: ID of buyer or seller
     * role: enum 'buyer' or 'seller'
     * Primary Key - (`resid`, `userid`, `role`)
     */
    public function __construct($userid) {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->userid = $userid;
            $this->getCount();
            $this->getScores();
        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Internal Database Error");
        }
 
    }

    public function __destruct() {
    }

    private function getCount() {
        try {
            if($this->buyercount === null || $this->sellercount === null)
                return;

            $sql = "SELECT count(*) FROM feedback WHERE userid = :userid and role=:role";
            $stmt = $this->conn->prepare( $sql );
            if($this->buyercount === null) {
                $stmt->bindValue(":userid", $this->userid, PDO::PARAM_INT );
                $stmt->bindValue(":role", 'buyer', PDO::PARAM_INT );
                $stmt->execute();
                $this->buyercount  = $read_stmt->fetch();
                //error_log("BUYER COUNT:".$this->buyercount);
            }
            
            if($this->sellercount === null) {
                $stmt->bindValue(":userid", $this->userid, PDO::PARAM_INT );
                $stmt->bindValue(":role", 'seller', PDO::PARAM_INT );
                $stmt->execute();
                $this->sellercount  = $read_stmt->fetch();
                //error_log("SELLER COUNT:".$this->sellercount);
            }

            $this->totalCount = $this->buyercount + $this->sellercount;

        } catch (Exception $e) {
            error_log("Cannot get feedback count");
            throw new Exception("Cannot get feedback count");
        }
    }

    private function getScores() {
        try {

            $sql = "SELECT avg(fbscore) as score FROM feedback WHERE userid = :userid and role=:role";
            $stmt = $this->conn->prepare( $sql );

            $stmt->bindValue(":userid", $this->userid, PDO::PARAM_INT );
            $stmt->bindValue(":role", 'buyer', PDO::PARAM_STR );
            $stmt->execute();
            $avg  = $stmt->fetch();
            $this->buyerscore  = $avg[0] ? $avg[0] : 0;
            //error_log("BUYER SCORE:".$this->buyerscore);
            
            $stmt->bindValue(":userid", $this->userid, PDO::PARAM_INT );
            $stmt->bindValue(":role", 'seller', PDO::PARAM_STR );
            $stmt->execute();
            $avg  = $stmt->fetch();
            $this->sellerscore  = $avg[0] ? $avg[0] : 0;
            //error_log("SELLER SCORE:".print_r($this->sellerscore,true));

            $sql = "SELECT avg(fbscore) FROM feedback WHERE userid = :userid";
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":userid", $this->userid, PDO::PARAM_INT );
            $stmt->execute();
            $avg  = $stmt->fetch();
            $this->totalscore  = $avg[0] ? $avg[0] : 0;
            //error_log("TOTAL SCORE:".$this->totalscore);
 
        } catch (Exception $e) {
            error_log("Cannot get avg feedback scores");
            throw new Exception("Cannot get avg feedback scores");
        }
    }

    public function getBuyerScore() {
        return($this->buyerscore);
    }

    public function getSellerScore() {
        return($this->sellerscore);
    }

    public function getTotalScore() {
        return($this->totalscore);
    }


    public function getBuyerCount() {
        return($this->buyercount);
    }

    public function getSellerCount() {
        return($this->sellercount);
    }

    public function getTotalCount() {
        return($this->totalcount);
    }

}

?>
