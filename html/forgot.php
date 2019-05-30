<?php

include_once("config.php"); //include the config
include_once("user.php");

$mypagetype='nosb';

$boxstyles = array( 
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

$user = new Users();
$lic = $user->login_check();
$title = "Forgotten Password";

$nextpage = isset($_GET['nextpage']) ? $_GET['nextpage'] : '/';

include "top.php";
include "wide1.php";
?>

<div id="stylized" class="myform">
<form id="regform" name='regform' method="post" action="profile.php">
  <h1><?=$title?></h1>
  <fieldset>
  <!--p>This is the basic look of my form without table</p-->

  <div id="ack"></div>

  <label>E-Mail Address<span class="small"></span></label>
  <input type="text" name="email" id="email" />

  <center><input type="submit" class="center" value="Recover Your Password" /></center>
  </fieldset>
  <center><a href="register.php">Register</a></center>
  </form>
 
<script>
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
                url: "aj/recover.php",
		dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
                //alert("success");
                $("#ack").empty();
		if(response.success == true) {
                	$("#ack").html("Sending you an email!");
			//window.location = 'welcome.php?userID='+response.userID;
			//window.location = 'login.php';
			setTimeout("window.location = '<?=$nextpage?>'", 2500);
                	return true;
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
<?php
include "wide2.php";
include "bottom.php";

?>
