<?php

include_once("config.php"); //include the config

$mypagetype='nosb';

$boxstyles = array( 
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

$title = 'User Registration';
include "top.php";
?>

<div id="wide-content">
        <div class="box-style2">
                <div class="content">
                        <h2>New User Registration</h2>
                </div>
        </div>
        <div class="box-style4">
                <div class="t"></div>
                <div class="content">
                        <div>
                                <h2 class="title">Your Information</h2>

<div id="stylized" class="myform">
<form id="regform" name='regform' method="post" action="welcome.php">
  <h1>Sign-up form</h1>
  <fieldset>
  <!--p>This is the basic look of my form without table</p-->

  <div id="ack"></div>

  <label>Username<span class="small"></span></label>
  <input type="text" name="username" id="usn" />

  <label>First Name<span class="small"></span></label>
  <input type="text" name="first" id="first" />

  <label>Last Name<span class="small"></span></label>
  <input type="text" name="last" id="last" />

  <label>Email<span class="small"></span></label>
  <input type="text" name="email" id="email" />

  <label>Password<span class="small"></span></label>
  <input type="password" name="password" id="passwd" />

  <label>Confirm Password<span class="small"></span></label>
  <input type="password" name="conpassword" id="conpasswd" />

    <!--a class="button button-style2" style="margin-left: 100px;" href="/"><span>Cancel</span></a>
    <button type="submit">Go</button-->
    <center><input type="submit" class="center" value="Register" /></center>
    <!--a id="submit" class="button button-style3" style="float: right; margin-right: 100px;" onclick="document.regform.submit();return false;"><span>Register</span></a-->
  </fieldset>
  </form>
 
<script type="text/javascript">
$("#regform").submit(function(event) {

        event.preventDefault();
        // if(request) {
        //      request.abort();
        //}

        //var $form = $("#myForm :input");
        var $inputs = $("#regform").find("input, select, button, textarea");
        //var serializedData = $form.serialize();

        var values = {};
        var serializedData = $("#regform").serialize();


        // let's disable the inputs for the duration of the ajax request
        $inputs.prop("disabled", true);

        var request = $.ajax({
                type: "POST",
                url: "aj/validate.php",
		dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
                //alert("success");
                $("#ack").empty();
		if(response.success == true) {
                	$("#ack").html("Sending Email!");
			window.location = 'welcome.php?userID='+response.userID;
                	return false;
		} else {
                	$("#ack").html(response.error);
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
</script>
<!--script>
$("#regform").submit(function() {
if ($("input:first").val() == "correct") {
$("#ack").text("Validated...").show();
return true;
}
$("#ack").text("Not valid!").show().fadeOut(100000);
return false;
});
</script-->

<!-- End Content -->
                        </div>
                </div>
                <div class="b"></div>
        </div>
</div>
<?php
include "bottom.php";
?>
