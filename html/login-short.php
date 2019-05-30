<?php

include_once("config.php"); //include the config
include_once("user.php");

$sess = new Session();
$sess->start_session('_s', false);

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
if($lic === false) 
	$title = "Login";
else
	$title = "Hey Dude. Welcome";

$nextpage = isset($_GET['nextpage']) ? $_GET['nextpage'] : '/';
?>

<div id="stylized" class="myform">
<form id="regform" name='regform' method="post" action="profile.php">
  <h1><?=$title?></h1>
  <fieldset>
  <!--p>This is the basic look of my form without table</p-->

  <div id="lresponse"></div>

  <label>Email<span class="small"></span></label>
  <input type="hidden" name="nextpage" id="nextpage" value='<?=$nextpage?>' />
  <input type="text" name="username" id="usn" />

  <label>Password<span class="small"></span></label>
  <input type="password" name="password" id="passwd" />

  <center><input type="submit" class="center" value="Login" /></center>
  </fieldset>
  <center><a href="register.php">Register</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="forgot.php">Forgot Your Password?</a></center>
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
                url: "aj/login.php",
		dataType: 'json',
                data: serializedData
        });

        // callblresponse handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
                //alert("success");
                $("#lresponse").empty();
		if(response.success == true) {
                	$("#lresponse").html("Logging In!");
			//window.location = 'welcome.php?userID='+response.userID;
			//window.location = 'login.php';
			setTimeout("window.location = '<?=$nextpage?>'", 1500);
                	return true;
		} else {
                	$("#lresponse").html(response.error);
                	return false;
		}
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
                //alert("failure");
                $("#lresponse").empty();
                $("#lresponse").html("The following error occured: "+ textStatus, errorThrown);
                return false;
        });

        // callblresponse handler that will be called regardless
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
$("#lresponse").text("Validated...").show();
return true;
}
$("#lresponse").text("Not valid!").show().fadeOut(100000);
return false;
});
</script-->

<!-- End Content -->
<?php

?>
