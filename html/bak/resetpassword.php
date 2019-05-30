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
$title = "Reset Password";

$code = isset($_GET['code']) ? $_GET['code'] : '/';
$userid = isset($_GET['userID']) ? $_GET['userID'] : '/';
$email = isset($_GET['email']) ? $_GET['email'] : '/';

include "top.php";
include "wide1.php";

?>
            <!--a href="#" class="html_popup">Link</a-->
            <div id="stylized" class="profileform">
                <form id="itemform" name='itemform' method="post" action="">
                <fieldset>
                <input type="hidden" name="code" id="code" value="<?=$code?>"/>
                <input type="hidden" name="userid" id="userid" value="<?=$userid?>" />
                <input type="hidden" name="email" id="email" value="<?=$email?>" />
               
                <div id="ack2"></div>

                <div class="left">
                    <label>New Password<span class="small"></span></label>
                    <input type="password" name="password" id="password" />
		</div>
                
                <div class="right">
                    <label>Confirm New Password<span class="small"></span></label>
                    <input type="password" name="conpassword" id="conpassword" />
               </div>

               <center><input type="submit" class="center" value="Update Profile" /></center>
               </fieldset>
               </form>
            </div> <!-- id="stylized" -->

<script type="text/javascript">
<!--
$(function(){
$("#itemform").submit(function(event) {

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
                url: "aj/recover.php",
                dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
                //alert("success");
                $("#ack").empty();
            if(response.success == true) {
                    $("#ack2").html("Updating Your Password! Hang Tight.");
                    setTimeout("window.location = '/'",1500);
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
});
-->
</script>

<?php
include "wide2.php";
include "bottom.php";
?>
