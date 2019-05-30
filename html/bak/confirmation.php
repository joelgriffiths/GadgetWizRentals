<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("userinfo.php");
include_once("Reservation.php");

$mypagetype='rightsb';
//$title="Order Confirmation! One step left.";
$title="It's almost yours ... for awhile anyways";
include "top.php";
include "rightsb1.php";

$resid = isset($_GET['resid']) && is_numeric($_GET['resid']) ? $_GET['resid'] : 0;

// Get the userid of whoever is looking at this page. It's private
$userid = $user->getUserID();

$resobj = new Reservation($resid);
//error_log($resid.'='.print_r($resobj, true));
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

// Maybe later
function xxxgetSubtotal(event, nextpage) {

        event = typeof event !== 'undefined' ? event : null;
        nextpage = typeof nextpage !== 'undefined' ? nextpage : false;

        $("#ack2").empty();
        if($("#startdate") == 0 || $("#numintervals") == 0){
            //alert("Nothing to calc");
            $('#ack2').html("Please provide the rental date and how long you want the item.");
            return;
        }

        if(event) {
            event.preventDefault();
        }

        var $inputs = $("form").find("input, select, button, textarea");
        var formdata = $inputs.serialize();

        // let's disable the inputs for the duration of the ajax request
        $inputs.prop("disabled", true);

        if(nextpage == true) {
            $("#ack2").html("Confirming your reservation! Hold Tight.");
            var serializedData = 'action=nextpage&'+formdata;
        } else {
            var serializedData = 'action=getSubTotal&'+formdata;
        }

        var values = {};


        var request = $.ajax({
                type: "POST",
                url: "aj/confirmation.php",
                dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
            $("#subtotal").empty();
            if(response.success == true) {
                $("#returndate").html("Return Date:"+response.returndate);
                $("#subtotal").html("Rent:"+response.subtotal);
                $("#tax").html("Tax:"+response.tax);
                $("#deposit").html("Deposit:"+response.deposit);
                $("#deliveryfee").html("Delivery:"+response.deliveryfee);
                $("#totalcost").html(response.totalcost);
                if(nextpage == true) {
                    alert("Reservation ID: "+response.reservationid)
                    setTimeout("window.location = 'confirmation.php?resid="+response.reservationid+"'",1500);
                }
                return true;
            } else {
                $("#ack2").html(response.error);
                return false;
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
                //alert("failure");
                $("#ack2").empty();
                $("#ack2").html("The following error occured: "+ textStatus, errorThrown);
                return false;
        });

        // callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
                // reenable the inputs
                $inputs.prop("disabled", false);
                return false;
        });

        return false;
}


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

<?php else :?>
<!--h2 class="title">It's almost yours ... <i>for awhile anyways</i>. </h2-->
<!--p><img src="/images/subpage1_04.jpg" alt="" width="160" height="140" class="alignleft" />Thanks for stumbing across our company.</p-->
<fieldset>
<legend>Order Details</legend/>
<!--span>Reservation ID:</span> <?=$resobj->getReservationID()?><br /-->
<span></span><strong><?=$itemobj->getTitle()?></strong><br />
<span>Leasor Name:</span> <?=$sellerobj->getFirstName()?> <?=$sellerobj->getLastName()?><br />
<span>Leasee Name:</span> <?=$buyerobj->getFirstName()?> <?=$buyerobj->getLastName()?><br />
<span>Pickup:</span> <?=$resobj->getEnPickupDate()?> at <?=$resobj->getEnPickupTime()?><br />
<span>Return:</span> <?=$resobj->getEnReturnDate()?> at <?=$resobj->getEnReturnTime()?><br />
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
<?php if($resobj->getDeposit() > 0) :?>
<span>+ Deposit (Refundable):</span> <?=money_format('%i', $resobj->getDeposit())?><br />
<?php endif;?>
<span>Total Charges</span> <?=money_format('%i', $resobj->getTotal())?><br />
<br />
<?php if($resobj->getResFee() > 0) :?>
<span>Reservation Fee (due now):</span> <?=money_format('%i', $resobj->getResFee())?><br />
<?php endif;?>
<span>Amount due at Delivery/Pickup:</span> <?=money_format('%i', $resobj->getTotal()-$resobj->getResFee())?><br />
</fieldset>
<fieldset>
<legend>Pickup</legend>
<?php if($resobj->getDeliveryChoice() == 'pickup') :?>
You can pick it up in <?=$sellerinfo->getCity()?>,<?=$resobj->getRegion()?> on <?=$resobj->getPickupDate()?>.
<p>Contact information will be provided after your order is complete.</p>
<?php else :?>
<p>Once you confirm the order, the Leasor (seller) will be notified. They will make arrangements to deliver your item to you on <?=$resobj->getEnPickupDate()?> at <?=$resobj->getEnPickupTime()?> to the following address:</p>
<fieldset style="background-color:#eee;">
<div class='deliveryaddress'>
<?=$resobj->getAddress1()?><br />
<?php if($resobj->getAddress2() != '') :?>
<?=$resobj->getAddress2()?><br />
<?php endif;?>
<?=$resobj->getCity()?>,<?=$resobj->getRegion()?> <?=$resobj->getPostalCode()?><br />
</div>
</fieldset>
<p>Contact information will be provided after your order is complete.</p>
<?php endif;?>
</fieldset>
<?php endif;?>
<form name='confirmreservation'>
<input type="checkbox" id='agreed' /><label class="right">I have read and agree with the <a href="/eula.php?pw=500&amp;ph=300" target="_blank">Terms of Service</a>.</label><br /><br />
</form>
<!-- Paypal -->
<div id="payamount"></div>
<form id='ppayment' action="<?=PAYPALURL?>" method="post" target="_top">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="currency_code" value="USD">
<select id="amount" name="amount">

<option value="<?=$resobj->getResFee()?>">Reservation Fee Only - $<?=$resobj->getResFee()?></option>

<!--option value="<?=$resobj->getTotal()?>">Pay All Fees Now - $<?=$resobj->getTotal()?></option-->
</select>
<input type="hidden" name="item_name" value="<?=$itemobj->getTitle()?>">
<input type="hidden" name="item_number" value="<?=$resobj->getReservationID()?>">
<input type="hidden" name="business" value="<?=BUSINESS?>">
<input type="hidden" name="notify_url" value="http://<?=MAINSITEURL?>/ipn.php">
<input type="hidden" name="return" value="http://<?=MAINSITEURL?>/reservationstatus.php?resid=<?=$resid?>">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" id='paynow' alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

					</div>
				</div>
			</div>
			<div id="sidebar">
				<div id="box2">
					<h2>Rent From Your Neighbor!</h2>
					<h2>Rent To Your Neighbor!&trade;</h2>
					<ul class="list-style4">
					</ul>
				</div>
                <br />
                <br />
                <br />
                <br />
				<div id="box3">
					<h2>What do <strong>YOU</strong> have in your Back Yard?</h2>
					<ul class="list-style4">
					</ul>
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
