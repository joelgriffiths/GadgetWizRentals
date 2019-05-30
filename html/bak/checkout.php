<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("userinfo.php");
include_once("Item.php");
include_once("states.php");

$mypagetype='nosb';
$color = "grey";
$stateobj = new States();

$title="Checkout";
include "top.php";
include "wide1.php";

if($_GET['itemid']) {
    $itemobj = new Item($_GET['itemid']);
    $itemid = $itemobj->getItemId();
}

$userinfo = new Userinfo($user->getUserID());

if($lic !== true) :?>

<script type="text/javascript">
<!--
$(function(){

        // Default usage
        $('.default_popup').popup({
		closeContent    :	'',
		modal		: 	true
	});
	var popup = new $.Popup({
		closeContent    :	'',
		modal		: 	true
	});
	popup.open('login-short.php?nextpage=checkout.php?itemid=<?=$itemid?>');
});
-->
</script>

<?php
endif;
?>
<script type='text/javascript'>
<!--
var startdate = 0;
var returndate = 0;

$(function() {
    $( "#startdate" ).datepicker();
    //$( "#returndate" ).datepicker();
});


function getSubtotal(event, nextpage) {

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
                url: "aj/checkout.php",
                dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
            $("#subtotal").empty();
            if(response.success == true) {
                $("#returndate").html("Return Date:<br />"+response.returndate);
                $("#subtotal").html("Rent:<br />"+response.subtotal);
                $("#tax").html("Tax:<br />"+response.tax);
                $("#deposit").html("Deposit:<br />"+response.deposit);
                $("#resfee").html("Due Now:<br/>"+response.resfee);
                $("#deliveryfee").html("Delivery:<br />"+response.deliveryfee);
                $("#totalcost").html("Total Due:<br />"+response.totalcost);
                $("#totaldue").html("Due at Pickup:<br />"+response.dueatpickup);
                if(nextpage == true) {
                    //alert("Reservation ID: "+response.reservationid)
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

    getSubtotal();

<?php
// So sorry
if($itemobj->getDeliveryOption() == 'delivertofixedlocation' || $itemobj->getDeliveryOption() == 'deliveryrequired') :
?>
$("#deliveryaddress").css("display", "inline");
<?php endif; ?>
    $("#checkout").submit(function(event) {

        alert("I DONT THINK THIS IS USED");
        event.preventDefault();
        // if(request) {
        //      request.abort();
        //}

        //var $form = $("#myForm :input");
        var $inputs = $("#itemform").find("input, select, button, textarea");
        //var serializedData = $form.serialize();

        var values = {};
        var serializedData = $("#itemform").serialize();


        // let's disable the inputs for the duration of the ajax request
        $inputs.prop("disabled", true);

        var request = $.ajax({
                type: "POST",
                url: "aj/checkout.php",
                dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
                //alert("success");
                $("#ack").empty();
            if(response.success == true) {
                $("#ack2").html("Confirming your reservation! Be right back.");
                //setTimeout("window.location = 'confirmation.php?resid=response.reservation'",1500);
                setTimeout($("#ack").empty(),1500);
                return true;
            } else {
                $("#ack2").html(response.error);
                return false;
            }
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
                //alert("failure");
                $("#ack").empty();
                $("#ack").html("The following error occured: "+ textStatus, errorThrown);
                return false;
        });

        // callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
                // reenable the inputs
                $inputs.prop("disabled", false);
                return false;
        });

        // prevent default posting of form
        //event.preventDefault();
        return false;
    });

    $("#costate,#cozip,#startdate,#starttime,#numintervals").change(function(event) {
        getSubtotal(event);
    });

    $("#deliveryoptions").change(function(event) {
        if($(this).val() == "deliveryrequired") {
            $("#deliveryaddress").css("display", "inline");
        } else {
            $("#deliveryaddress").css("display", "none");
        }
        getSubtotal(event);
    });

    $("#nextpage").click(function(event) {
        getSubtotal(event, true);
    });


});
-->
</script>
<div id="ack2"></div>
<div class="nextnav"><a href='#' id='nextpage' class='button button-style3'><span>Next Page</span></a></div>
<form name='checkout' id='checkout' action="/checkout.php" method="post">
<input type='hidden' name='itemid' value='<?=$itemid?>' />
<div class="CSSTableGenerator" >
    <table >
        <tr>
            <td>Item</td>
            <td>Price</td>
            <td>Choose Your Options</td>
            <td>Breakdown</td>
            <td>Subtotal</td>
        </tr>
        <tr>
            <td class='itemtitle'>
                <a href="/rental.php?itemid=<?=$itemid?>"><?=$itemobj->getTitle();?></a>
            </td>
            <td class="intervalprice">
                <?=$itemobj->getPrice()?>/<?=ucfirst($itemobj->getInterval())?></td>
            <td id="rentaloptions">
<?php 
// Datetime is most irritating php object ever
$datetime = new DateTime('now');
$minnotice = $itemobj->getMinNotice() + 1;
$oneday = new DateInterval('P1D');
$starttime = new DateInterval('PT'.$minnotice.'H');
$datetime->add($starttime);
$pickuphour = $datetime->format('H');

// After closing, auto-select next morning.
if($pickuphour >= 17) {
    $pickuphour = 9;
    $datetime->add($oneday);
}

$pickupday = $datetime->format('m/d/Y');
?>
                Rental Date: <input class='date' id='startdate' name='startdate' value='<?=$pickupday?>'/>&nbsp;&nbsp; <select class='time' id='starttime' name='starttime'>
<?php
// This will be optional from the seller later on
for($time=7; $time<=19; $time++) {
    $selected = '';
    if($time == $pickuphour)
        $selected = "selected='selected'";

    if($time == 0) {
        $entime = '12 AM';
    } elseif($time < 12) {
        $entime = $time." AM";
    } elseif($time > 12) {
        $entime = ($time - 12) . " PM";
    } elseif($time == 12) {
        $entime = 'Noon';
    }
    echo "<option $selected value='$time'>$entime</option>";
}
?>
                </select>
                <br />
                &nbsp;&nbsp;How Long: <input class='smallint' id='numintervals' name='numintervals' value='1' maxlength='2' /><?=ucfirst($itemobj->getInterval())?>(s)<br />
 
                <br />
                <div id='returndate'></div>
                <br />
<?php if($itemobj->getDeliveryOption() == 'deliveryavailable') : ?>
                <p>Rental will be:</p>
                <select name="deliveryoptions" id="deliveryoptions">
                   <option value="pickuponly">Picked up from Seller</option>
                   <option value="deliveryrequired">Delivered to You at:</option>
                </select>
<?php elseif($itemobj->getDeliveryOption() == 'pickuponly') : ?>
                <p>You must pick up this item from the Seller</p>
                <input type='hidden' name='deliveryoptions' value='pickuponly' />
<?php elseif($itemobj->getDeliveryOption() == 'delivertofixedlocation') : ?>
                <p>The item will be delivered to the following location:</p>
                <input type='hidden' name='deliveryoptions' value='deliveryrequired' />
<?php elseif($itemobj->getDeliveryOption() == 'deliveryrequired') : ?>
                <p>Item will be delivered to you</p>
                <input type='hidden' name='deliveryoptions' value='deliveryrequired' />
<?php endif; ?>
               <br />
               <div id='deliveryaddress' style="text-align: right">
<?php
$country = $userinfo->getCountry() ? $userinfo->getCountry() : $itemobj->getCountry();
?>
                <input type="hidden" name='cocountry' value='<?=$country?>' />
                <span>Address1:</span><input id='coaddress1' name='coaddress1' value='<?=$userinfo->getAddress1()?>' /><br/>
                <span>Address2:</span><input id='coaddress2' name='coaddress2' value='<?=$userinfo->getAddress2()?>'/><br/>
                <span>City:</span><input id='cocity' name='cocity' value='<?=$userinfo->getCity()?>' /><br/>
                <span>State:</span><select name="costate" id="costate">
                <?php $stateobj->getStateOptions($itemobj->getState()); ?>
                </select><br />
                <span>Zip:</span><input id='cozip' name='cozip' maxlength='10' value='<?=$userinfo->getZip()?>'/><br/>
                <br/>
                <div id='b'></div>
               </div>
               <!--input class='day' id='returndate' name='returndate' /><br /><br /-->
            </td>
            <td>
                <br />
                <div id='subtotal'></div>
                <div id='deliveryfee'></div>
                <div id='tax'></div>
                <div id='deposit'></div>
            </td>
            <td><div id='totalcost'></div><br /><div id='resfee'></div><br /><div id='totaldue'></div></td>
        </tr>
    </table>
</div>
<br />
<p></small>The Reservation Fee is non-refundable unless the seller fails to complete the order. It is deducted from the total rental fees.</small></p>
<p></small>The Deposit is refundable if the item is returned on time in the same condition it was rented.</small></p>
<!--div class="bottom-nav">
    <a href='' id='deleteitems' class='button button-style3'><span>Next Page</span></a>
</div-->
</form>


<div class="clearfix">&nbsp;</div>


<?php
include "wide2.php";
include "bottom.php";
?>
