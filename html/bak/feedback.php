<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("Reservation.php");
include_once("Feedback.php");
include_once("FeedbackScore.php");
include_once("image.php");

$mypagetype='rightsb';

$boxstyles = array( 
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

$resid = isset($_GET['resid']) && is_numeric($_GET['resid']) ? $_GET['resid'] : 0;

if( isset($_GET['t']) && $_GET['t'] == 0) {
    $fbrole = 'buyer';
} elseif( isset($_GET['t']) && $_GET['t'] == 1) {
    $fbrole = 'seller';
} else {
    error_log("NOT". $_GET['t']);
    die;
}

include "top.php";

$resobj = new Reservation($resid);
$itemid = $resobj->getItemID();
$itemobj = new Item($itemid);

// I'm still allowing self feedback for now (for testing)
$myid = $user->getUserID();
if($myid === $resobj->getSellerID()) {
    $sellerobj = $user;
    $buyerobj = new Users();
    $buyerobj->loadFromDB($resobj->getBuyerID());
    $otheruser = $buyerobj;
} elseif($myid === $resobj->getBuyerID()) {
    $buyerobj = $user;
    $sellerobj = new Users();
    $sellerobj->loadFromDB($resobj->getSellerID());
    $otheruser = $sellerobj;
} else {
    // Self Promoter
    error_log("ABUSE: ".$user->getUserID()." tried to leave feedback for reservation ID:".$resid);
}

$otherid = $otheruser->getUserID();
$fbscore = 2;

$fbobj = new Feedback($resid, $otherid, $fbrole);
$fbscore = $fbobj->getFeedbackScore() ? $fbobj->getFeedbackScore() : 0;
$fbtext = $fbobj->getFeedback() ?  $fbobj->getFeedback() : 'Feedback';
$fbotherid = $fbobj->getOtherID();

// Prbably do this in a number of places.
if($fbobj->checkExists() === true && $fbotherid !== $myid) {
    error_log("Cannot modify feedback for somebody else $fbotherid !== $myid");
    die;
}
error_log(print_r($fbobj,true));

/*
$fbs = new FeedbackScore($otherid);
$buyercount = $fbs->getBuyerCount();
$sellercount = $fbs->getSellerCount();
$totalcount = $fbs->getTotalCount();
$feedbackscore = $fbs->getTotalScore();
*/


$rentalstatus = $resobj->getStatus();

if($fbrole == 'buyer') {
    $title='Feedback For Your Rental To '.$otheruser->getUsername().'!';
} else {
    $title='Feedback For Your Rental From '.$otheruser->getUsername().'!';
}

include "rightsb1.php";
?>
<script type="text/javascript">
<!--
function starClick(count) {
    $("#fbscore").val(count);
    switch(count) {
        case 5:
            $('#5star').attr("src",'/images/ystar.png');
        case 4:
            $('#4star').attr("src",'/images/ystar.png');
        case 3:
            $('#3star').attr("src",'/images/ystar.png');
        case 2:
            $('#2star').attr("src",'/images/ystar.png');
        case 1:
            $('#1star').attr("src",'/images/ystar.png');
    }
}

function clearAll() {
    $('#5star').attr("src",'/images/gstar.png');
    $('#4star').attr("src",'/images/gstar.png');
    $('#3star').attr("src",'/images/gstar.png');
    $('#2star').attr("src",'/images/gstar.png');
    $('#1star').attr("src",'/images/gstar.png');
}


$(document).ready(function(){
function submitfeedback() {

        var $inputs = $("#feedbackform").find("input, select, button, textarea");
        var serializedData = $("#feedbackform").serialize();

        // let's disable the inputs for the duration of the ajax request
        $inputs.prop("disabled", true);

        var request = $.ajax({
                type: "POST",
                url: "aj/feedback.php",
                dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
                //alert("success");
                $("#ack2").empty();
                if(response.success == true) {
                    $("#ack2").html("Updating Your Settings! Hang Tight.");
                    setTimeout("window.location = 'fbsummary.php'",1500);
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

// Catch <Enter> press
$('#feedbackform').submit(function(event) {
    event.preventDefault();
    submitfeedback();
});
$('#savefb').click(function(event) {
    event.preventDefault();
    submitfeedback();
});

$('#5star').click(function() {
    clearAll();
    starClick(5);
});
$('#4star').click(function() {
    clearAll();
    starClick(4);
});
$('#3star').click(function() {
    clearAll();
    starClick(3);
});
$('#2star').click(function() {
    clearAll();
    starClick(2);
});
$('#1star').click(function() {
    clearAll();
    starClick(1);
});

$('#5star').hover(function() {
    $('#stardesc').html("The Transaction Went Smmothly");
});
$('#4star').hover(function() {
    $('#stardesc').html("There was a minor problem, but it was resolved");
});
$('#3star').hover(function() {
    $('#stardesc').html("There was a minor unresolved problem");
});
$('#2star').hover(function() {
    $('#stardesc').html("There was a major, unresolved issue");
});
$('#1star').hover(function() {
    $('#stardesc').html("This transaction was a complete failue");
});


starClick(<?=$fbscore?>);
});

-->
</script>
			<div class="t"></div>
			<div id="content">
				<div class="content">
					<div>
<h2 class="title" style="text-align:center;"><?=$itemobj->getTitle();?></h2>
<div id='ack2'></div>
<p class='centerp'>Rate <?=$otheruser->getUsername();?> for this transaction</p>
<p class='centerp'><div id='stardesc'>5 Stars Means the Transaction Went Smoothly</div></p>
<center>
<img src='/images/gstar.png' alt="One Star" id='1star' width=16 height=16 />
<img src='/images/gstar.png' alt="Two Stars" id='2star' width=16 height=16 />
<img src='/images/gstar.png' alt="Three Stars" id='3star' width=16 height=16 />
<img src='/images/gstar.png' alt="Four Stars" id='4star' width=16 height=16 />
<img src='/images/gstar.png' alt="Five Stars" id='5star' width=16 height=16 />
</center>
<br />
<div id="feedback">
<form name='feedback' method="post" id="feedbackform">
<input type='hidden' name="resid" value="<?=$resid?>" />
<input type='hidden' name="fbscore" id="fbscore" value="<?=$fbscore?>" />
<input type='hidden' name="fbuser" id="fbuser" value="<?=$otherid?>" />
<input id="fbtext" type="text" name="fbtext" title="Feedback" maxlength=80 value="<?=$fbtext?>" />
<br />
<center>
<a href="" id='savefb' class="button button-style5"><span>Save Feedback!</span></a>
</center>
</form>
<script type="text/javascript">
<!--
$('#feedbackform').formerize();
-->
</script>
</div>

					</div>
				</div>
			</div>
			<div id="sidebar">
				<div id="box2"><center>
<?php
$images = new Image();
$image = $images->getImage($otherid, 'profile',0);
?>

                    <img src='<?=$images->GetProfileImageSrc($otheruser->getUserID());?>' width='100' alt='Image <?=$image?>'/></a>
					<h2><?=$otheruser->getUsername()?></h2>
                    </center>
<?php
$starid = $otherid;
include "stars.php";
?>
                    <!--input type="text" name="amount" value="2.53" />
                    <input type="submit" value="update"-->
					<!--h2>Rent To Your Neighbor!&trade;</h2-->
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
