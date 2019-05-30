<?php
header('Content-type: application/json');

include_once("config.php"); //include the config
include_once("CatSelector.php");
include_once("Item.php");
include_once("user.php");
include_once("userinfo.php");
include_once("cGeocode.php");
include_once("Reservation.php");

$sess = new Session();
$sess->start_session('_s', false);

$user = new Users();
$lic = $user->login_check();
$userid = $user->getUserID();
$userinfo = new Userinfo($userid);

if($lic !== true) {
    error_log("ABUSE: Trying reserve while logged out");
    $result = array("success" => false,"error" => 'Sorry. You have been logged out. Please <a href="/login.php">login</a> again.');
    echo json_encode($result);
    exit;
}

$action = isset($_POST['action']) ? strtolower($_POST['action']) : null;
$itemid = isset($_POST['itemid']) && $_POST['itemid'] > 0 && is_numeric($_POST['itemid']) ? $_POST['itemid'] : 0;
$reservationid = isset($_POST['reservationid']) && $_POST['reservationid'] > 0 && is_numeric($_POST['reservationid']) ? $_POST['reservationid'] : 0;

if(!$action) {
    ajaxFailure("Invalid Action");
    exit; // For readability
}

try {
    # Existing Reservation
    if($reservationid) {
        $resobj = new Reservation($reservationid);
        if($userid !== $regobj->getSellerID() && $userid != $resobj->getBuyerID()) {
            error_log("ABUSE: $userid is a bad boy.");
            $result = array("success" => false,"error" => 'You cannot edit an item that\'s not yours');
            echo json_encode($result);
            exit;
        }
    } else {
        if($itemid == 0 || $userid == 0)
            throw new Exception("Cannot Reserve non-existent item ($itemid) by user ($userid)");
        $resobj = new Reservation($userid, $itemid);
    }

    $itemobj = new Item($resobj->getItemID());

    switch($action) {
        case 'nextpage':
        case 'getsubtotal':
            setlocale(LC_MONETARY, 'en_US');

            $pickuptime = sprintf("%02s", $_POST['starttime']);
            $startdate = strtotime($_POST['startdate']." ".$pickuptime.":00:01");
            $resobj->setPickupDate($startdate);

            // Convert the hours to seconds and add it to the startdate
            $minnotice = $itemobj->getMinNotice()*60*60;

            // Need to make this work for each user
            $minstart = strtotime("now") + $minnotice;

            if($startdate < $minstart) {
                ajaxFailure('Your selected delivery time is too early for this provider. Please provide at least '.$itemobj->getMinNotice().' hours notice for this rental.');
            }

            $numintervals = $_POST['numintervals'];
            $resobj->setNumIntervals($numintervals);
            $resobj->setInterval($itemobj->getInterval());
            if($resobj->getInterval() == 'hour')
                $rentaltime = $numintervals*60*60;
            elseif($resobj->getInterval() == 'day')
                $rentaltime = $numintervals*60*60*24;
            elseif($resobj->getInterval() == 'week')
                $rentaltime = $numintervals*60*60*24*7;
            elseif($resobj->getInterval() == 'month')
                $rentaltime = $numintervals*60*60*24*7*30;
            elseif($resobj->getInterval() == 'year')
                $rentaltime = $numintervals*60*60*24*365;

            $returndate = $startdate + $rentaltime;
            $enreturndate = date('n/d/Y g:i:00 A', $returndate);

            $sqlreturndate = date('Y-n-d H:i:00', $returndate);
            $sqlstartdate = date('Y-n-d H:i:00', $startdate);

            $totalrent = (int)$numintervals * $itemobj->getPrice();

            $resobj->setDeliveryChoice('pickup');
            if($itemobj->getDeliveryOption() == 'delivertofixedlocation' ||
                    $itemobj->getDeliveryOption() == 'deliveryrequired' ||
                    $_POST['deliveryoptions'] == "deliveryrequired") {

                $resobj->setDeliveryChoice('deliver');
                $radius = $itemobj->getRadius();
                //$distance = $itemobj->checkBuyerDistance($_POST['cozip']);
                $distance = $itemobj->checkBuyerDistance($_POST['cozip'],$_POST['cocountry']);
                if($distance > $radius) {
                    ajaxFailure("Delivery is limited to $radius miles.<br />You are $distance away.");
                }
               $deliveryfee = $itemobj->getOneTimeFee();

            } else {
               $deliveryfee = 0;
            }

            // This is now the reservation Fee
            $deposit = $itemobj->getDeposit();
            $deposit = round($deposit, 2);
            error_log($deposit."<<<<<<<<<<<<<<<<<<<<<<<");

            $resfee = ($totalrent+$deliveryfee)*(RESFEE/100);
            if($resfee < 1) $resfee = MINFEE;
            if($resfee > MAXFEE) $resfee = MAXFEE;
            $resfee = round($resfee, 2);

            $tax = 0;
            // Confusing - Delivery chack the delivery location
            //             Pickup always has taxes (probably a bug here)
            if(
                  ($itemobj->getTaxState() == $_POST['costate'] &&
                  $resobj->getDeliveryChoice() == 'deliver') 
                  ||
                  ($itemobj->getTaxState() != '' &&
                  $resobj->getDeliveryChoice() == 'pickup')
               ) {

                if($itemobj->getTaxShipping()) {
                    $tax = $itemobj->getTaxRate()/100 *($deliveryfee + $totalrent);
                } else {
                    $tax = $itemobj->getTaxRate()/100 *($totalrent);
                }
            }

            $totalcost = $deliveryfee + $totalrent + $tax + $deposit;
            error_log("$totalcost = $deliveryfee + $totalrent + $tax + $deposit ");
            $due = $totalcost - $resfee;

            $resobj->setBuyerID($user->getUserID());
            $resobj->setSellerID($itemobj->getUserID());
            $resobj->setPickupDate($sqlstartdate);
            $resobj->setReturnDate($sqlreturndate);
            $resobj->setNumIntervals($numintervals);
            $resobj->setTax($tax);
            $resobj->setDeliveryFee($deliveryfee);
            $resobj->setTotalRent($totalrent);
            $resobj->setDeposit($deposit);
            $resobj->setResFee($resfee);
            $resobj->setStatus('pending');
            $resobj->setDepositStatus('na');

            if($resobj->getDeliveryChoice() == 'deliver') {
                $resobj->setCountry($_POST['cocountry']);
                $resobj->setAddress1($_POST['coaddress1']);
                $resobj->setAddress2($_POST['coaddress2']);
                $resobj->setCity($_POST['cocity']);
                $resobj->setRegion($_POST['costate']);
                $resobj->setPostalCode($_POST['cozip']);
            }

            try {
                if($action == 'nextpage')
                    $reservationid = $resobj->saveReservation();
            } catch (Exception $e) {
                ajaxFailure("Could not confirm your reservation");
            }

            $result = array("success" => true, "tax" => money_format('%i', $tax), "subtotal" => money_format('%i', $totalrent), "deposit" => money_format('%i',$deposit), "deliveryfee" => money_format('%i',$deliveryfee), "totalcost" => money_format('%i',$totalcost), "returndate" => $enreturndate, "dueatpickup" =>  money_format('%i',$due), "reservationid" => $reservationid, "resfee" =>  money_format('%i',$resfee));

            error_log(print_r($resobj,true));
            //error_log('getsubtotal: '.print_r($result,true));
            echo json_encode($result);
            exit;
        default:
            ajaxFailure("Invalid Action");
        }


} catch (Exception $e) {
    ajaxFailure($e->getMessage());
}
error_log(print_r($_POST,true));
exit;


function ajaxFailure($errorstring) {
	$result = array("success" => false,"error" => $errorstring);
	error_log("aj/checkout.php: ".$errorstring);
	echo json_encode($result);
	exit;
}

?>
