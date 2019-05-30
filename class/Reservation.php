<?php

include_once 'config.php';
include_once 'Address.php';
include_once 'Item.php';
include_once "cGeocode.php";
include_once "user.php";
include_once "userinfo.php";
include_once "timezone.php";


class Reservation extends Address {

    private $conn = null;
    private $newreservation = false;

    private $itemobj = null;
    private $buyerobj = null;
    private $sellerobj = null;

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
    private $timezone = null;
    private $tax = null;
    private $deposit = null;
    private $resfee = null;
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
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Internal Database Error");
        }
 
        // If itemid is null, the first argument is a revservationid
        if($itemid == null) {
            $this->reservationid = $reservation_or_buyerid;
            //error_log("loadExistingReservation(RESID: $reservation_or_buyerid)");
            $this->loadExistingReservation($reservation_or_buyerid);
        // otherwise, it's the buyerid
        } else {
            $this->buyerid = $reservation_or_buyerid;
            $this->itemid = $itemid;
            //error_log("mkNewReservation(BUYER: $this->buyerid, ITEM: $this->itemid)");
            $this->mkNewReservation($this->buyerid, $this->itemid);
        }
        parent::__construct($this->deliveryaddressid);
        //error_log(print_r($this,true));
    }

    // Existing Reservation
    private function loadExistingReservation($reservationid) {
        try {
            if(is_numeric($reservationid)) {
                $this->reservationid = $reservationid;
                $this->read();
            } else {
                error_log("Invalid reservation ID");
                throw new Exception("Invalid reservation ID");
            }
        } catch ( PDEException $e ) {
            error_log( $e->getMessage() );
            throw new Exception("Internal Database Error");
        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw $e;
        }
        return true;
    }


    // New reservation
    private function mkNewReservation($buyerid, $itemid) {
        try {
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
        } catch ( Exception $e ) {
            error_log( $e->getMessage() );
            throw $e;
        }
        return true;
    }

    function close() {
        $this->conn = null;
    }

    function read() {
        try {

            $sql = "SELECT `reservationid`, `buyerid`, `sellerid`, `itemid`, `interval`, `numintervals`,`pickupdate`, `returndate`, `totalrent`,  `deliveryfee`, `tax`, `resfee`, `deposit`, `deliverychoice`, `deliveryaddressid`, `status`, `depositstatus` FROM reservations where reservationid=:reservationid";
            $read_stmt = $this->conn->prepare( $sql );
            $read_stmt->bindValue(":reservationid", $this->reservationid, PDO::PARAM_STR );

            // Only buyer and seller can access this information
            // Need this, but will checked in the calling function for now
            //$read_stmt->bindValue(":buyerid", $this->buyerid, PDO::PARAM_STR );
            //$read_stmt->bindValue(":sellerid", $this->sellerid, PDO::PARAM_STR );

            $read_stmt->execute();
    
            $numRows = $read_stmt->rowCount();
            if( $numRows == 0 ) {
                throw new Exception("Cannot retrieve reservation from the Database for reservationid:".$this->reservationid);
            }

            list($this->reservationid, $this->buyerid, $this->sellerid,
                 $this->itemid, $this->interval, $this->numintervals,
                 $this->pickupdate, $this->returndate,
                 $this->totalrent,  $this->deliveryfee, $this->tax, $this->resfee, $this->deposit,
                 $this->deliverychoice,
                 $this->deliveryaddressid, $this->status, $this->depositstatus) 
                  = $read_stmt->fetch(PDO::FETCH_NUM);

            $this->buyerobj = new Users();
            $this->buyerobj->loadFromDB($this->buyerid);
            $this->buyerinfo = new Userinfo($this->buyerid);

            $this->sellerobj = new Users();
            $this->sellerobj->loadFromDB($this->sellerid);
//  error_log("SELLER: ".print_r($this->sellerobj,true));
            $this->sellerinfo = new Userinfo($this->sellerid);
            $this->timezone = get_time_zone($this->sellerinfo->getCountry(), $this->sellerinfo->getState());

            $this->itemobj = new Item($this->itemid);
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

            if($this->reservationid) {
                $sql = "UPDATE reservations SET `buyerid`=:buyerid, `sellerid`=:sellerid, `itemid`=:itemid, `interval`=:interval, `numintervals`=:numintervals, `pickupdate`=:pickupdate, `returndate`=:returndate, `totalrent`=:totalrent, `deliveryfee`=:deliveryfee, `tax`=:tax, `resfee`=:resfee, `deposit`=:deposit, `deliverychoice`=:deliverychoice, `deliveryaddressid`=:deliveryaddressid, `status`=:status, `depositstatus`=:depositstatus WHERE `reservationid`=:reservationid";
                $read_stmt = $this->conn->prepare( $sql );
                $read_stmt->bindValue(":reservationid", $this->reservationid, PDO::PARAM_INT );
            } else {
                $sql = 'INSERT INTO reservations (`buyerid`, `sellerid`, `itemid`, `interval`, `numintervals`, `pickupdate`, `returndate`, `totalrent`, `deliveryfee`, `tax`, `resfee`, `deposit`, `deliverychoice`, `deliveryaddressid`, `status`, `depositstatus`) VALUES (:buyerid, :sellerid, :itemid, :interval, :numintervals, :pickupdate, :returndate, :totalrent,  :deliveryfee, :tax, :resfee, :deposit, :deliverychoice, :deliveryaddressid, :status, :depositstatus)';
                $read_stmt = $this->conn->prepare( $sql );
            }
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
            $read_stmt->bindValue(":resfee", $this->resfee, PDO::PARAM_STR );
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

    function deleteReservation() {
        try {
            if($this->deliverychoice == 'deliver') {
                $this->deliveryaddressid = $this->saveAddressToDB();
            }

            if($this->reservationid) {
                $sql = "DELETE FROM reservations WHERE `reservationid`=:reservationid and status = 'pending'";
                $read_stmt = $this->conn->prepare( $sql );
                $read_stmt->bindValue(":reservationid", $this->reservationid, PDO::PARAM_INT );
            } else {
                error_log("Tried to delete a reservation without a reservation ID");
                throw new Exception("Tried to delete a reservation without a reservation ID");
            }

            $read_stmt->execute();

        } catch ( PDOException $e ) {
            $this->conn->rollback();
            error_log("Reservation DELETE DB error ".$e->getMessage());
            return false;
        } catch ( Exception $e ) {
            error_log('RESERVATION: '.$this->reservationid .':'.$e->getMessage());
            return false;
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
    function getEnPickupDate() {
         $endate = date('l, M-d-Y', strtotime($this->pickupdate));
         return($endate);
    }
    function getShortEnPickupDate() {
         $endate = date('D, M-d-Y', strtotime($this->pickupdate));
         return($endate);
    }
    function getEnPickupTime() {
         $entime = date('g:i:00 A', strtotime($this->pickupdate));
         return($entime);
    }
    function getShortEnPickupTime() {
         $entime = date('g A', strtotime($this->pickupdate));
         return($entime);
    }
    function getPickupDate() {
         return($this->pickupdate);
    }
    function setPickupDate($date) {
         $this->pickupdate = $date;
    }

    // Function to determine if it's too late to pick up
    // Timezone is a little loose, but should work 99% of the time
    function timeBeforeLastPickup() {

        // Get current time in correct time zone
        $tz = new DateTimeZone($this->timezone);
        $putime = new DateTime($this->pickupdate, $tz);
        $now = new DateTime('now', $tz);
        
        // Pickup Time MINUS Notice = Last Pickuptime
        $minnotice = $this->itemobj->getMinNotice();
        $minnoticeinterval = new DateInterval('PT'.$minnotice.'H');
        $lastpickuptime = $putime->sub($minnoticeinterval);

        $nowepoc = (int)$now->format('U');
        $lpuepoc = (int)$lastpickuptime->format('U');
        $difference = ($lpuepoc - $nowepoc)/3600;

        return($difference);
    }

    // Function to determine if it's too late to pick up
    // Timezone is a little loose, but should work 99% of the time
    function timeBeforeReturn() {

        // Get current time in correct time zone
        $tz = new DateTimeZone($this->timezone);
        $putime = new DateTime($this->returndate, $tz);
        $now = new DateTime('now', $tz);
        
        // Pickup Time MINUS Notice = Last Pickuptime
        $minnotice = $this->itemobj->getMinNotice();
        $minnoticeinterval = new DateInterval('PT'.$minnotice.'H');
        $lastreturndate = $putime->sub($minnoticeinterval);

        $nowepoc = (int)$now->format('U');
        $lpuepoc = (int)$lastreturndate->format('U');
        $difference = ($lpuepoc - $nowepoc)/3600;

        return($difference);
    }

    function getEnReturnDate() {
         $endate = date('l, M-d-Y', strtotime($this->returndate));
         return($endate);
    }
    function getShortEnReturnDate() {
         $endate = date('D, M-d-Y', strtotime($this->returndate));
         return($endate);
    }
    function getEnReturnTime() {
         $entime = date('g:i:00 A', strtotime($this->returndate));
         return($entime);
    }
    function getShortEnReturnTime() {
         $entime = date('g A', strtotime($this->returndate));
         return($entime);
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

    function getResFee() {
         return($this->resfee);
    }
    function setResFee($resfee) {
        $this->resfee = $this->validNumber($resfee);
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
        $this->total = $this->totalrent + $this->deliveryfee + $this->tax + $this->deposit;
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


    function getNextSellerStep() {
        switch ($this->status) {
            // Waiting on seller to pay. Not reservers
            case 'pending':
                return '';
                break;
            case 'resfeepaid':
                return 'resfeepaid';
                break;
            case 'sellerconfirmed':
                return 'sellerconfirmed';
                break;
        }


        if($this->getStatus() == 'resfeepaid') return true;
        return false;
    }


    function readyToRent() {
        if($this->getStatus() == 'resfeepaid') return true;
        return false;
    }

    /* 
     * 'pending' = Unconfirmed Order
     * 'resfeepaid'
     * 'depositpaid'
     * 'paidinfull'
     * 'complete' = Transaction is Complete
     * 'sellerfeespaid' = Transaction is Complete
     * 'buyercancelled'
     * 'sellercancelled'
     * 'sellerverified'
     * 'admincancelled'
     */
    function getStatus() {
         return($this->status);
    }
    function setStatus($choice) {
        error_log("PAYMENT STATUS CHANGED FROM  $this->status to $choice");
        if($this->status == 'pending' && $choice == 'resfeepaid')
            $this->sendConfirmationEmailToSeller();
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

    public function sendConfirmationEmailToSeller() {
        $my_email = '"' . SHORTDOM . '" <'.SUPPORT_EMAIL.'>';
        $my_email = '"Zalaxy.com" <noreply@zalaxy.com>';
        $domain = $_SERVER["HTTP_HOST"];
        $shortdom = CAPSHORTDOM;
        $boundary = uniqid('np');
    
        $subject = $shortdom." Rental Notification";
    
        $headers = '';
        $headers .= 'MIME-Version: 1.0' . "\n";
        $headers .= 'From: ' . SUPPORT_EMAIL . "\n";
        /*
        $headers .= 'Content-Type: multipart/alternative;boundary=' . $boundary . "\n";
    
        // MIME
        $message = 'This is a MIME encoded message.'."\n";;
    
        // Text
        $message .= "\n\n--" . $boundary . "\n";
        $message .= 'Content-type: text/plain;charset=utf-8'."\n\n";
        */
        $message = 'Hi ' . $this->sellerobj->getFirstName() .":\n\n";
        $message .= 'Your "'.$this->itemobj->getTitle(). " has been rented!\n\n";
        $message .= "Rental Starts: ".$this->getShortEnPickupTime()." ".$this->getShortEnPickupDate()."\n";
        $message .= "Rental Ends: ".$this->getShortEnReturnTime()." ".$this->getShortEnReturnDate()."\n";
        if($this->deliverychoice == 'deliver') {
            $message .= "\n\nBuyer has requested that you deliver the item to the following address:\n";
            $message .= $this->getAddress1()."\n";
            $message .= $this->getAddress2()."\n";
            $message .= $this->getCity().','. $this->getState()." ".$this->getZip()."\n";
        } else {
            $message .= "\n\nThe Buyer will pick up the item at your registered location:\n";
            $message .= $this->sellerinfo->getAddress1()."\n";
            $message .= $this->sellerinfo->getAddress1()."\n";
            $message .= $this->sellerinfo->getCity().','. $this->sellerinfo->getState()." ".$this->sellerinfo->getZip()."\n";
        }
        $message .= "\nMore details are available at http://".MAINSITEURL."/reservationstatus.php?resid=".$this->reservationid."\n\n";
        /*
        $message .= "\n\n--" . $boundary . "\n";
        $message .= "Content-type: text/html;charset=utf-8\n\n";
        $message .= "<html><body><img src='http://$domain/images/http://www.zalaxy.com/images/zalaxy-slash-logo-small.png' alt='$shortdom' /> <table rules='all' style='border-color: #666;' cellpadding='10'> <tr style='background: #eee;'><td><strong>You're almost done $this->first.</strong></td><td style='font-size: 120%;font-weight:bold;'><strong><a href='$activation'>Activate Account</a></strong></td></tr><tr><td colspan=2>$activation</td></tr></table> </body></html>\n";
        $message .= "\n\n--" . $boundary . "--";
        */
    
        $foo = mail($this->sellerobj->getEmail(), $subject, $message, $headers, "-f".$my_email);
        //$foo = mail('joelg@localhost', $subject, $message, $headers, "-f".$my_email);
        if( ! $foo )
            throw new Exception("Reservation Created, but email could not be sent");
    }
    


}
