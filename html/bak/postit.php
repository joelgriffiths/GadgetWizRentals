<?php
include_once "CatSelector.php";
include_once "Category.php";
include_once "Regions.php";

$title = "List your thing!";

include_once "postit-top.php";
include_once "Item.php";
include_once "userinfo.php";
include_once "states.php";
$stateobj = new States();
$userinfo = new Userinfo($userid);

$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? $_GET['itemid'] : 0;

$itemobj = new Item($itemid);
if($itemid != 0) {
    $state = $itemobj->getState();
    $city = $itemobj->getCity();
} else {
    $state = $userinfo->getState();
    $city = $userinfo->getCity();
}

// Verify the user is not trying to edith somebody else's record.
$acat = array(null,null,null,null,null,null);
if( $itemobj->getUserID() != $user->getUserID() ) {
    if($itemid)
        error_log('ABUSE: User ('.$user->getUserName().') attempted to modify Item '.$itemobj->getItemId());

    $itemobj  = '';
    $itemiobj = null;
    $itemobj = new Item(0);
} else {
}

if($category = new Category($itemobj->getCategory())) {
    $acat = $category->getSBArray();
}

// Let them almost log in
if($lic !== true) :?>

<script type="text/javascript">
<!--
$(function(){

        // Default usage
        $('.default_popup').popup({
        closeContent    :   '',
        modal       :   true
    });
    var popup = new $.Popup({
        closeContent    :   '',
        modal       :   true
    });
    popup.open('login-short.php?nextpage=checkout.php?itemid=<?=$itemid?>');
});
-->
</script>

<?php
endif;
?>

<script type="text/javascript">
<!--
$(document).ready(function()
{
<?php
$ready = $userinfo->ready2Sell();
if($ready === false):?>
alert("Please complete your contact information before renting items.");
window.location = "profile-contact.php";
<? endif; ?>
$(".category1,.category2,.category3,.category4,.category5").change(function() {
    var id=$(this).val();
    var dataString = 'selectedid='+id;
    var thiscat = $(this).attr('class');
    //alert("the selected value is " +$(this).attr('class')+ dataString);

    var request = $.ajax ({
        type: "POST",
        url: "/aj/catselect.php",
        data: dataString,
        cache: false
    });

    request.done(function (response, textStatus, jqXHR){
        $("#ack2").empty();
        if(response.success == true) {
            if(response.options != '<option value="-1"></option>') {
                if(thiscat == 'category1') {
                    $(".category2").html(response.options);
                    $(".category2").css('visibility',"visible");
                    //$(".category2").css('display',"inline");
                }
                if(thiscat == 'category2') {
                    $(".category3").html(response.options);
                    $(".category3").css('visibility',"visible");
                    //$(".category3").css('display',"inline");
                }
                if(thiscat == 'category3') {
                    $(".category4").html(response.options);
                    $(".category4").css('visibility',"visible");
                    //$(".category4").css('display',"inline");
                }
                if(thiscat == 'category4') {
                    $(".category5").html(response.options);
                    $(".category5").css('visibility',"visible");
                    //$(".category5").css('display',"inline");
                }
                switch(thiscat) {
                    case 'category1':
                        $(".category2").change();
                        break;
                    case 'category2':
                        $(".category3").change();
                        break;
                    case 'category3':
                        $(".category4").change();
                        break;
                    case 'category4':
                        $(".category5").change();
                        break;
                    case 'category5':
                        $(".category6").change();
                        break;
                }

            } else {
                switch(thiscat) {
                    case 'category1':
                        //$(".category2").css('display',"none");
                        $(".category2").css('visibility',"hidden");
                        $(".category2").html('');
                    case 'category2':
                        //$(".category3").css('display',"none");
                        $(".category3").css('visibility',"hidden");
                        $(".category3").html('');
                    case 'category3':
                        //$(".category4").css('display',"none");
                        $(".category4").css('visibility',"hidden");
                        $(".category4").html('');
                    case 'category4':
                        //$(".category5").css('display',"none");
                        $(".category5").css('visibility',"hidden");
                        $(".category5").html('');
                    case 'category5':
                        //$(".category6").css('display',"none");
                        $(".category6").css('visibility',"hidden");
                        $(".category6").html('');
                }
            }
        }
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
        //alert("failure");
        $("#ack").empty();
        $("#ack").html("The following error occured: "+ textStatus, errorThrown);
        return false;
    });
});

$(".deliveryoptions").change(function() {
    var selected=$(this).val();
    if(selected != 'pickuponly') {
        $("#deliveryradius").css('display',"inline");
        $("#radtitle").css('display',"inline");
        $("#searchunits").css('display',"inline");
        $("#deliveryfee").css('display',"inline");
        $("#itemonetimefee").css('display',"inline");
    } else {
        $("#deliveryradius").css('display',"none");
        $("#radtitle").css('display',"none");
        $("#searchunits").css('display',"none");
        $("#deliveryfee").css('display',"none");
        $("#itemonetimefee").css('display',"none");
    }
});

$('.deliveryoptions').change();

$(".country,.state").change(function() {
    event.preventDefault();
    var thischange = $(this).attr('class');
    var dataString = 'selectid='+thischange+'&'+$("#itemform").serialize();
    //alert("the selected value is "+$(this).attr('class')+":"+dataString);

    var $inputs = $("#itemform").find("input, select, button, textarea");
//alert($inputs);
    $inputs.prop("disabled", true);

    var request = $.ajax ({
        type: "POST",
        url: "/aj/regionselect.php",
        data: dataString,
        cache: false
    });

    request.done(function (response, textStatus, jqXHR){
        $("#ack2").empty();
        if(response.success == true) {
            //$(".country").html(response.countries);
            $(".state").html(response.states);
            $(".city").html(response.cities);
            //$("#closecities").empty();
            //$("#closecities").html(response.closecities);
        } else {
            alert("No Good");
        }
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
        alert("Internal Error " + errorThrown);
        $("#ack").empty();
        $("#ack").html("The following error occured: "+ textStatus, errorThrown);
        return false;
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
            // reenable the inputs
            $inputs.prop("disabled", false);
            return true;
    });

});

});
-->
</script>


<!--a href="#" class="html_popup">Link</a-->
<div id="stylized" class="postit">
<?php if($itemid):?>
&nbsp;<a href="/listings.php"><img src='/images/left_arrows_orange.png'>Back to Listings</a>
<?php endif;?>
<form id="itemform" name='itemform' method="post" action="postit.php">
<input type="hidden" name="itemid" value='<?=$itemid?>' />
  <div id="ack2"></div>
  <fieldset>
  <legend>Basic Information</legend>

  <!--label>Category<span class="small"></span></label-->
  <label class="left">Category<span class="small"></span></label><br /><br/>
  <select name="category1" class="category1">
<?php
$mystyle = '';
$cat = new CatSelector();
error_log(print_r($acat,true));
error_log(print_r($cat,true));
$cat->printSelectedOptions($acat[0]);
?>
  </select>

<?php if(isset($acat[1]) && $acat[1]) $mystyle=" style='visibility: visible' ";?>
  <select name='category2' class='category2' <?=$mystyle?>>
  <option value="-1"></option>
<?php
$cat->printSelectedOptions($acat[1]);
?>
  </select>

<?php if(isset($acat[2]) && $acat[2]) $mystyle=" style='visibility: visible' ";?>
  <select name='category3' class='category3' <?=$mystyle?>>
  <option value="-1"></option>
<?php
$cat->printSelectedOptions($acat[2]);
?>
  </select>

  <!--select name='category4' class='category4'>
  <option value="-1"></option>
  </select>

  <select name='category5' class='category5'>
  <option value="-1"></option>
  </select>

  <select name='category6' class='category6'>
  <option value="-1"></option>
  </select-->


  <div id="formtitle">
  <label class="left">Title<span class="small"></span></label><br />
  <input type="text" name="itemtitle" class="itemtitle" value='<?=$itemobj->getTitle()?>' />
  </div>

  <label class="center">Description<span class="small"></span></label><br />
  <center><textarea name="itemdesc" class="center" id='itemdesc' rows='100' cols='40'><?=$itemobj->getDescription()?></textarea></center>

  </fieldset>
  <br />

  <fieldset>
  <legend>Pickup/Delivery Instructions</legend>
 <div id="leftdelivery">
 <span class="price">
   <label>Delivery Options <a href="/docs/delivery-options.html" class="default_popup"><img src="/images/q.png" alt="?" /></a></label><br /><br/>
   <select name="deliveryoptions" class="deliveryoptions">
   <option value="deliveryavailable" <?=$itemobj->getDeliveryOptionSB('deliveryavailable')?>>Delivery Available</option>
   <option value="deliveryrequired" <?=$itemobj->getDeliveryOptionSB('deliveryrequired')?>>Delivery Required</option>
   <option value="pickuponly" <?=$itemobj->getDeliveryOptionSB('pickuponly')?>>Pickup Only</option>
   </select>
  </span>
  <span class="price">
    <label id="radtitle">Delivery Radius</label><br /><br />
        <select name="deliveryradius" id="deliveryradius" style="margin-left: 28px">
        <?=$itemobj->printRadiusSB()?>
    </select>
    <input type='hidden' value='M' id="searchunits">
    <!--select name="searchunits" id="searchunits">
    <option value='M'>Miles</options>
    <option value='K'>KM</options>
    </select-->
  </span>
 </div>

 <div id="rightdelivery">
  <span class="price">

   <label style="width: 160px;">Delivery/Pickup Location</label><br /><br/>
<?php
$mystyle = '';
$region = new Regions();
$country = $userinfo->getCountry();
$region->setCountry($country);
?>
  <input type='hidden' name="country" class="country" value='<?=$country?>'>

<?php if($country) $mystyle=" style='visibility: visible' ";?>
  <select name='state' class='state' <?=$mystyle?>>
<?php

echo $region->printSelectedStates($state);
$state = $region->getState();
?>
  </select>

  <label>&nbsp;</label><br />
  <select name='city' class='city' <?=$mystyle?>>
<?php
echo $region->printSelectedCities($city);
$city = $region->getCity();
?>
  </select></br>
  <br />



   <!--label>Item Postal Code</label><br /><br /-->
<?php
if($itemobj->getZip() != null)
    $zip = $itemobj->getZip();
else
    $zip = $userinfo->getZip();
?>
   <input type="hidden" name="itemzipcode" id="itemzipcode" maxlength='5' value="<?=$zip?>"/>
  </span>
 </div>

  </fieldset>
  <br />
 
  <fieldset>
  <legend>Fees and Taxes</legend>

 <div id="leftcol">
  <span class="price">
  <label>Rental Fee<span class="small"></span></label><br /><br />
    $<input type="text" name="itemrentalfee" id="itemrentalfee" maxlength='7' value='<?=$itemobj->getPrice()?>'/>&nbsp;&nbsp; per
    <select name="iteminterval" id="iteminterval">
        <?=$itemobj->printIntervalSB()?>
    </select>
  </span>
 </div>
 <div id="rightcol">
  <span class="price">
    <label>Sales tax<span class="small"></span></label><br /><br />
        <input type="text" name="tax" id="itemtax" maxlength='7' value='<?=$itemobj->getTaxRate()?>'/>%
        <select name="taxstate" id="taxstate">
            <option value="No Tax">No Tax</option>
            <?php $stateobj->getStateOptions($itemobj->getTaxState()); ?>
        </select>
  </span>
 </div>
 <div class="itemcenter">
  <center><label id="deliveryfee" style='clear: both;margin-top:10px;margin-left:80px;'>Delivery Fee</label>
  <input type="text" name="itemonetimefee" id="itemonetimefee" style="float: left;"  maxlength='7' value="<?=$itemobj->getOneTimeFee()?>"/></center>
 </div>
  <!--div class="itemcenter">
    <center>Deposit <a href="/docs/deposit.html" class="default_popup"><img src="/images/q.png" alt="?" /></a> <input type="text" name="itemdeposit" id="itemdeposit" value='<?=$itemobj->getDeposit()?>' maxlength='8'/></center>
  </div-->


  </fieldset>
  <br />
  <fieldset>
    <legend>Post Your Advertisement</legend>
 <div class="itemcenter">
  <input type="checkbox" name='agreed' /><label class="right">I have read and agree with the <a href="/eula.php?pw=500&amp;ph=300" target="_blank">Terms of Service</a> and <a href="fees.php?pw=500&amp;ph=300" class="default_popup">Rental Fees</a>.</label><br /><br />

  <label class="center">
<?php if($itemid): ?>
  <input type="submit" value="Update Your Listing" />
<?php else: ?>
  <input type="submit" value="Post Your Item" />
<?php endif;?>
  </label>
 </div>
  </fieldset>
  </form>
 </div>

<!-- End Content -->
<?php
include "postit-bottom.php";

?>
