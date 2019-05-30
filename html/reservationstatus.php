<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("userinfo.php");
include_once("Reservation.php");
include_once("ShortIPN.php");

$mypagetype='rightsb';
$title="Reservation Status";
include "top.php";
include "rightsb1.php";

$resid = isset($_GET['resid']) && is_numeric($_GET['resid']) ? $_GET['resid'] : 0;

// Get the userid of whoever is looking at this page. It's private
$userid = $user->getUserID();

$resobj = new Reservation($resid);
$status = $resobj->getStatus();
switch($status) {
    case 'pending':
        $mainmsg = "Payment has not been made";
        $wtf = 'pending';
        break;
    case 'paidinfull':
    case 'resfeepaid':
    case 'depositpaid':
        $mainmsg = "Your Item as been Reserved. Pickup/Delivery details below.";
        $wtf = 'reserved';
        break;
    case 'complete':
        $mainmsg = "This Reservation has been fullfilled";
        $wtf = 'complete';
        break;
    case 'sellerconfirmed':
        $mainmsg = "This Seller has this reservation";
        $wtf = 'cancel';
        break;
    case 'sellercancelled':
        $mainmsg = "This Seller has Cancelled this Reservation";
        $wtf = 'cancel';
        break;
    case 'buyercancelled':
        $mainmsg = "This Buyer has Cancelled this Reservation";
        $wtf = 'cancel';
        break;
    case 'admincancelled':
        $mainmsg = "We were forced to cancel this reservation. Please call 480-717-5635 if you have any questions.";
        $wtf = 'cancel';
        break;

}

// Okay. Bad me. Need to process multiple payments. Quick hack
$db = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT txn_id FROM orders where reservationid=:reservationid";
$stmt = $db->prepare( $sql );
$stmt->bindValue(":reservationid", $resid, PDO::PARAM_STR );
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_COLUMN);
error_log(print_r($orders,true));

// No afvanced payment is needed
/* No need for this in the current system after I added the resfee. In fact, 
 * it makes stuff paid that should be */
/*if($resobj->getDeposit() == 0 && $resobj->getStatus() == 0 || $resobj->getStatus() == 'pending') {
    $resobj->setStatus('depositpaid');
    $resobj->saveReservation();
    $mainmsg = "Your Item is Reserved";
    $wtf = 'reserved';
}
*/

$itemobj = new Item($resobj->getItemID());

$buyerid = $resobj->getBuyerID();
$buyerobj = new Users();
$buyerobj->loadFromDB($buyerid);
$buyerinfo = new UserInfo($buyerid);

$sellerid = $resobj->getSellerID();
$sellerobj = new Users();
$sellerobj->loadFromDB($sellerid);
$sellerinfo = new UserInfo($sellerid);

//error_log(print_r($sellerinfo, true));
//error_log(print_r($buyerobj, true));
?>
<script type='text/javascript'>
<!--
var startdate = 0;
var returndate = 0;

$(document).ready(function(){

    //getSubtotal();
    $("#amount").change(function(event) {
        //alert($("#amount").val());
        //getSubtotal(event);
    });

    $("#paynow").click(function(event) {
        event.preventDefault();
        if(!$('#agreed').is(":checked")) {
            alert("Please read and accept the Terms of Service");
        } else {
            if($('#amount').val() == 0) {
                window.location = 'reservationstatus.php?resid=<?=$resid?>'
            } else {
                $('#ppayment').submit();
            }
        }
        //getSubtotal(event, true);
    });

});

-->
</script>



			<div class="t"></div>
			<div id="content">
				<div class="content">
					<div>
<?php
// Lets not share this information with the world.
if($userid != $sellerid && $userid != $buyerid) :
?>
This is not a valid reservation.
<?php else :?>
<!--h2 class="title"><?=$mainmsg?></h2-->
<!--p><img src="/images/subpage1_04.jpg" alt="" width="160" height="140" class="alignleft" />Thanks for stumbing across our company.</p-->

<fieldset>
<legend>Order Status</legend>
<strong><?=$mainmsg?></strong>
</fieldset>
<fieldset>
<legend>Order Details</legend>
<!--span>Reservation ID:</span> <?=$resobj->getReservationID()?><br /-->
<span></span><strong><?=$itemobj->getTitle()?></strong><br />
<span>Pickup:</span> <?=$resobj->getEnPickupDate()?> at <?=$resobj->getEnPickupTime()?><br />
<span>Return:</span> <?=$resobj->getEnReturnDate()?> at <?=$resobj->getEnReturnTime()?><br />
<br />
<?php if($wtf === 'pending') :?>
    <?php if($userid == $buyerid) : ?>
        <p class='important'>Your payment has not been confirmed. <a href="/confirmation.php?resid=<?=$resid?>">Complete Reservation Now</a></p>
    <?php else: ?>
        <p class='important'>The Buyer has NOT confirmed payment for this item</p>
    <?php endif; ?>
<? endif;?>
<?php if($resobj->getDeliveryChoice() == 'pickup') :?>

    <?php if($wtf === 'pending') :?>
        <p>Pickup Location (when confirmed): <?=$sellerinfo->getCity()?>,<?=$resobj->getRegion()?> on <?=$resobj->getPickupDate()?>.</p>
        <p>Pickup information will be provided after your order is confirmed.</p>
    <?php else:?>
        <p>Buyer must pick up item from the seller.</p>
        <p>Pickup Address:</p>
        <div class='deliveryaddress'>
            <?=$sellerinfo->getAddress1()?><br />
            <?php if($sellerinfo->getAddress2() != '') :?>
                <?=$sellerinfo->getAddress2()?><br />
            <?php endif;?>
            <?=$sellerinfo->getCity()?>, <?=$sellerinfo->getState()?> <?=$sellerinfo->getZip()?><br />
        </div>
    <?php endif;?>

<?php else : //Delivery ?>
  <?php if($wtf === 'pending') :?>
  <p>Contact information will be provided after your order is confirmed.</p>
  <?php else: ?>
    <p>The seller will deliver your item to you on <?=$resobj->getEnPickupDate()?> at <?=$resobj->getEnPickupTime()?>.</p>
    <p>Delivery will be made to the following address:</p>
    <div class='deliveryaddress'>
    <?=$resobj->getAddress1()?><br />
    <?php if($resobj->getAddress2() != '') :?>
       <?=$resobj->getAddress2()?><br />
    <?php endif;?>
    <?=$resobj->getCity()?>, <?=$resobj->getRegion()?> <?=$resobj->getPostalCode()?><br />
    </div>
  <?php endif;?>
<br />
<?php endif;?>
</fieldset>

<fieldset>
<legend>Contact Information</legend>
<?php if($wtf === 'pending') :?>
<p>Contact Information will be provided when payment is confirmed</p>
<?php else :?>
<span>Leasor Name:</span> <?=$sellerobj->getFirstName()?> <?=$sellerobj->getLastName()?><br />
<span>Leasor Phone:</span> <?=$sellerinfo->getPrimaryPhone()?><br />
<span>Leasor Email:</span> <?=$sellerobj->getEmail()?><br />

<br />
<span>Leasee Name:</span> <?=$buyerobj->getFirstName()?> <?=$buyerobj->getLastName()?><br />
<span>Leasee Phone:</span> <?=$buyerinfo->getPrimaryPhone()?><br />
<span>Leasee Email:</span> <?=$buyerobj->getEmail()?><br />
<?php endif;?>
</fieldset>
<fieldset>
<legend>Payment Information</legend>
<span>Total Rent:</span> <?=money_format('%i', $resobj->getTotalRent())?><br />
<?php if($resobj->getDeliveryFee() > 0) :?>
<span>+ Delivery Fee:</span> <?=money_format('%i', $resobj->getDeliveryFee())?><br />
<?php endif;?>
<?php if($resobj->getTax() > 0) :?>
<span>+ Tax:</span> <?=money_format('%i', $resobj->getTax())?><br />
<?php endif;?>
<span>Total Charges</span> <?=money_format('%i', $resobj->getTotal())?><br />
<br />
<?php
$deposit = $resobj->getDeposit();
$total = $resobj->getTotal();
foreach($orders as $txn_id) {
    error_log("Reading TXN $txn_id");
    $ipn = new ShortIPN(0, $txn_id);
    $amount = $ipn->getPaymentAmount();
    $total -= $amount;
    if($amount == $deposit) {
        echo '<span>- Deposit Received:</span> '.
          money_format('%i', $amount).'<br />';
    } else {
        echo '<span>- Payment Received:</span> '.
          money_format('%i', $amount).'<br />';
    }
}

if($total < 0)
    $text = '<br /><strong>Overpayment</strong><br /> Amount due Leasee (buyer) by Leasor when item is returned:';
else
    $text = 'Amount due at Delivery/Pickup:';
?>
<span><?=$text?></span> <?=money_format('%i', abs($total))?><br />
</fieldset>
<?php endif;?>

					</div>
				</div>
			</div>
			<div id="sidebar">
				<div id="box2">
					<h2>Rent From Your Neighbor!</h2>
					<h2>Rent To Your Neighbor!&trade;</h2>
					<ul class="list-style4"><li></li></ul>
				</div>
                <br />
                <br />
                <br />
                <br />
				<div id="box3">
					<h2>What do <strong>YOU</strong> have in your Back Yard?</h2>
					<ul class="list-style4"><li></li></ul>
				</div>
				<div>
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="b"></div>
	

<?php
include "rightsb2.php";
include "bottom.php";
?>
