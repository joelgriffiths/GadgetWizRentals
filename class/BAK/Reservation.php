<?php

include_once 'config.php';
include_once 'Address.php';
include_once 'Item.php';
include_once "cGeocode.php";
include_once "user.php";
include_once "userinfo.php";

class Reservation extends Address {

    private $conn = null;
    private $newreservation = false;

    private $itemobj = null;

    private $reservationid = null;
    private $buyerid = null;
    private $sellerid = null;
    private $itemid = null;

    private $interval = null;
    private $numintervals = null;

    private $pickupdate = null;
    private $returndate = null;
    private $totalrent = null;
    private $deliveryfee = null;
    private $tax = null;
    private $deposit = null;
    private $deliverychoice = null;
    private $deliveryaddressid = null;
    private $status = null;
    private $depositstatus = null;

    protected $total = 0;

    /*
     * ID = ReservationID or BuyerIDo
     * No overloading (ugh)
     * new Reservation($reservationid);
     * new Reservarion($buyerid, $itemid)
     */
    function __construct($reservation_or_buyerid, $itemid = null) {
        // If itemid is null, the first argument is a revservationid
        if($itemid == null) {
            $this->reservationid = $reservation_or_buyerid;
            //error_log("loadExistingReservation(RESID: $reservationid)");
            $this->loadExistingReservation($reservationid);
        // otherwise, it's the buyerid
        } else {
            $this->buyerid = $reservation_or_buyerid;
            $this->itemid = $itemid;
            //error_log("mkNewReservation(BUYER: $this->buyerid, ITEM: $this->itemid)");
            $this->mkNewReservation($this->buyerid, $this->itemid);
        }
        //error_log(print_r($this,true));
        parent::__construct($this->deliveryaddressid);
    }

    // Existing Reservation
    private function loadExistingReservation($reservationid) {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if(is_numeric($reservationid)) {
                $this->reservationid = $reservationid;
                $this->read();
            } else {
                error_log("Invalid reservation ID");
                throw new Exception("Invalid reservation ID");
            }
            parent::__construct($this->itemid);
        } catch ( PDOException $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Internal Database Error");
        }
        return true;
    }


    // New reservation
    private function mkNewReservation($buyerid, $itemid) {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if(is_numeric($buyerid) && is_numeric($itemid) && $itemid > 0 && $buyerid > 0) {
                $this->buyerid = $buyerid;
                $this->itemid = $itemid;
            } else {
                error_log("Cannot create reservation for $buyerid:$itemid");
                throw new Exception("Cannot create reservcation for $buyerid:$itemid");
            }
        } catch ( PDOException $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Internal Database Error");
        }
        return true;
    }

    function close() {
        $this->conn = null;
    }

    function read() {
        try {

            $sql = "SELECT `reservationid`, `buyerid`, `sellerid`, `itemid`, `pickupdate`, `returndate`, `totalrent`,  `interval`, `numintervals`, `deposit`, `total`, `deliverychoice`, `deliveryaddress`, `status`, `depositstatus` FROM reservations where reservationid=:reservationid and (buyerid=:buyerid or sellerid=:sellerid)";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":reservationid", $this->reservationid, PDO::PARAM_STR );

            // Only buyer and seller can access this information
            $read_stmt->bindValue(":buyerid", $this->buyerid, PDO::PARAM_STR );
            $read_stmt->bindValue(":sellerid", $this->sellerid, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve reservation from the Database for itemid $itemid");
            }

            list($this->reservationid, $this->buyerid, $this->sellerid,
                 $this->itemid, $this->pickupdate, $this->returndate,
                 $this->totalrent,  $this->interval, $this->numintervals, $this->deposit,
                 $this->total, $this->deliverychoice,
                 $this->deliveryaddress, $this->status, $this->depositstatus) 
                  = $read_stmt->fetch(PDO::FETCH_NUM);

        } catch ( PDOException $e ) {
            error_log("Reservation read DB error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            error_log('RESERVATION: '.$this->reservationid .':'.$e->getMessage());
            throw($e);
         }
        return true;
    }

    function saveReservation() {
        try {

            $this->conn->beginTransaction();

            if($this->deliverychoice == 'deliver') {
                $this->deliveryaddressid = $this->saveAddressToDB();
            }

            $sql = 'INSERT INTO reservations (`buyerid`, `sellerid`, `itemid`, `interval`, `numintervals`, `pickupdate`, `returndate`, `totalrent`, `deliveryfee`, `tax`, `deposit`, `deliverychoice`, `deliveryaddressid`, `status`, `depositstatus`) VALUES (:buyerid, :sellerid, :itemid, :interval, :numintervals, :pickupdate, :returndate, :totalrent,  :deliveryfee, :tax,  :deposit, :deliverychoice, :deliveryaddressid, :status, :depositstatus)';
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":buyerid", $this->buyerid, PDO::PARAM_INT );
            $read_stmt->bindValue(":sellerid", $this->sellerid, PDO::PARAM_INT );
            $read_stmt->bindValue(":itemid", $this->itemid, PDO::PARAM_INT );
            $read_stmt->bindValue(":interval", $this->interval, PDO::PARAM_STR );
            $read_stmt->bindValue(":numintervals", $this->numintervals, PDO::PARAM_INT );
            $read_stmt->bindValue(":pickupdate", $this->pickupdate, PDO::PARAM_STR );
            $read_stmt->bindValue(":returndate", $this->returndate, PDO::PARAM_STR );
            $read_stmt->bindValue(":totalrent", $this->totalrent, PDO::PARAM_STR );
            $read_stmt->bindValue(":deliveryfee", $this->deliveryfee, PDO::PARAM_STR );
            $read_stmt->bindValue(":tax", $this->tax, PDO::PARAM_STR );
            $read_stmt->bindValue(":deposit", $this->deposit, PDO::PARAM_STR );
            $read_stmt->bindValue(":deliverychoice", $this->deliverychoice, PDO::PARAM_STR );
            $read_stmt->bindValue(":deliveryaddressid", $this->deliveryaddressid, PDO::PARAM_INT );
            $read_stmt->bindValue(":status", $this->status, PDO::PARAM_STR );
            $read_stmt->bindValue(":depositstatus", $this->depositstatus, PDO::PARAM_STR );

            $read_stmt->execute();

            $this->reservationid = $this->conn->lastInsertId('reservationid');
    
            $this->conn->commit();

        } catch ( PDOException $e ) {
            $this->conn->rollback();
            error_log("Reservation read DB error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            $this->conn->rollback();
            error_log('RESERVATION: '.$this->reservationid .':'.$e->getMessage());
            throw($e);
         }
        return $this->reservationid;
    }

    function updateReservation() {
        try {


            $this->conn->beginTransaction();

            if($this->deliverychoice == 'deliver') {
                $this->deliveryaddressid = $this->saveAddressToDB();
            }
            $sql = "UPDATE INTO reservations (`buyerid`, `sellerid`, `itemid`, `interval`, `numintervals`, `pickupdate`, `returndate`, `totalrent`, `deliveryfee`, `tax`, `deposit`, `deliverychoice`, `deliveryaddressid`, `status`, `depositstatus`) VALUES (:buyerid, :sellerid, :itemid, :interval, :numintervals, :pickupdate, :returndate, :totalrent, :deliveryfee, :tax, :deposit, :deliverychoice, :deliveryaddressid, :status, :depositstatus)";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":buyerid", $this->buyerid, PDO::PARAM_INT );
            $read_stmt->bindValue(":sellerid", $this->sellerid, PDO::PARAM_INT );
            $read_stmt->bindValue(":itemid", $this->itemid, PDO::PARAM_INT );
            $read_stmt->bindValue(":interval", $this->interval, PDO::PARAM_STR );
            $read_stmt->bindValue(":numintervals", $this->numintervals, PDO::PARAM_INT );
            $read_stmt->bindValue(":pickupdate", $this->pickupdate, PDO::PARAM_STR );
            $read_stmt->bindValue(":returndate", $this->returndate, PDO::PARAM_STR );
            $read_stmt->bindValue(":totalrent", $this->totalrent, PDO::PARAM_STR );
            $read_stmt->bindValue(":deliveryfee", $this->deliveryfee, PDO::PARAM_STR );
            $read_stmt->bindValue(":tax", $this->tax, PDO::PARAM_STR );
            $read_stmt->bindValue(":deposit", $this->deposit, PDO::PARAM_STR );
            $read_stmt->bindValue(":deliverychoice", $this->deliverychoice, PDO::PARAM_STR );
            $read_stmt->bindValue(":deliveryaddressid", $this->deliveryaddress, PDO::PARAM_INT );
            $read_stmt->bindValue(":status", $this->status, PDO::PARAM_STR );
            $read_stmt->bindValue(":depositstatus", $this->depositstatus, PDO::PARAM_STR );

            $read_stmt->execute();

            $this->conn->commit();
    
        } catch ( PDOException $e ) {
            $this->conn->rollback();
            error_log("Reservation read DB error ".$e->getMessage());
            throw new Exception("DB Error");
        } catch ( Exception $e ) {
            $this->conn->rollback();
            error_log('RESERVATION: '.$this->reservationid .':'.$e->getMessage());
            throw($e);
         }
        return true;
    }



    private function validate() {

        $this->validPosNumber($this->buyerid);
        $this->validPosNumber($this->sellerid);
        $this->validPosNumber($this->itemid);

        $this->notNull($this->deliverychoice);

        if($this->deliverychoice === 'deliver')
            $this->notNull($this->deliveryaddress);
    }

    private function validPosNumber($num) {
         if(is_numeric($num) and $num > 0)
            return $num;
         else
            throw new Exception("Invalid Postive Number");
    }
    private function validNumber($num) {
         if(is_numeric($num) and $num >= 0)
            return $num;
         else
            throw new Exception("Invalid Postive Number");
    }
    private function notNull($string) {
        if($string == null)
            throw new Exception("Undefined Parameter for Reservation");
    }



    function getReservationID() {
        return($this->reservationid);
    }


    // ID's that must be > 0
    function getBuyerID() {
         return($this->buyerid);
    }
    function setBuyerID($id) {
         $this->buyerid = $this->validPosNumber($id);
    }

    function getSellerID() {
         return($this->sellerid);
    }
    function setSellerID($id) {
         $this->sellerid = $this->validPosNumber($id);
    }

    function getItemid() {
        return($this->itemid);
    }
    function setItemID($id) {
         $this->itemid = $this->validPosNumber($id);
    }

    function getInterval() {
        return($this->interval);
    }
    function setInterval($interval) {
         $this->interval = $interval;
    }

    function getNumIntervals() {
        return($this->numintervals);
    }
    function setNumIntervals($num) {
         $this->numintervals = $this->validPosNumber($num);
    }

    // Dates that must be defined
    function getPickupDate() {
         return($this->pickupdate);
    }
    function setPickupDate($date) {
         $this->pickupdate = $date;
    }

    function getReturnDate() {
         return($this->returndate);
    }
    function setReturnDate($date) {
         $this->returndate = $date;
    }

    // Floating Point Numbers
    function getTotalRent() {
         return($this->totalrent);
    }
    function setTotalRent($rent) {
        $this->totalrent = $this->validNumber($rent);
        $this->getTotal();
    }

    function getDeliveryFee() {
         return($this->deliveryfee);
    }
    function setDeliveryFee($fee) {
        $this->deliveryfee = $this->validNumber($fee);
        $this->getTotal();
    }

    function getTax() {
         return($this->tax);
    }
    function setTax($tax) {
        $this->tax = $this->validNumber($tax);
        $this->getTotal();
    }

    function getDeposit() {
         return($this->deposit);
    }
    function setDeposit($deposit) {
        $this->deposit = $this->validNumber($deposit);
        $this->getTotal();
    }

    function getTotal() {
        $this->total = $this->totalrent + $this->deliveryfee + $this->tax;
        return $this->total;
    }

    function getDeliveryChoice() {
         return($this->deliverychoice);
    }
    function setDeliveryChoice($choice) {
        if(strtolower($choice) != 'pickup' && strtolower($choice) != 'deliver')
            throw new Exception("Invalid Delivery Choice");
        $this->deliverychoice = $choice;
    }

    /* 
     * 'pending' = Unconfirmed Order
     * 'depositpaid'
     * 'complete' = Transaction is Complete
     * 'buyercancelled'
     * 'sellercancelled'
     * 'admincancelled'
     */
    function getStatus() {
         return($this->status);
    }
    function setStatus($choice) {
        $this->status = $choice;
    }

    /*
     * 'na' = Unconfirmed Order
     * 'received' = Unconfirmed Order
     * 'returned' = Deposit Refunded
     * 'senettoseller' = Transaction is Complete
     */
    function getDepositStatus() {
         return($this->depositstatus);
    }
    function setDepositStatus($choice) {
        $this->depositstatus = $choice;
    }



}
