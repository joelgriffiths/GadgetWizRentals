<?php
include_once("config.php"); //include the config
include_once("user.php");

$sess = new Session();
$sess->start_session('_s', false);

$position = isset($_GET['pos']) && is_numeric($_GET['pos']) ? $_GET['pos'] : 0;
$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? $_GET['itemid'] : 0;

# What kind of image (profile or listing - default to listing)
$imagetype = isset($_GET['type']) ? $_GET['type'] : 'listing';

$uptext = isset($_GET['type']) && $_GET['type'] == 'profile' ? "Upload your new profile image" : "Upload a picture of your thing";

$user = new Users();
$lic = $user->login_check();
$userid = $user->getUserID();

include_once "cImgbox.php";
include_once("Item.php");

$itemid = !empty($_GET['itemid']) ?  $_GET['itemid'] : 0;
$item = new Item($itemid);

error_log("UP.PHP: $itemid");
error_log(print_r($_GET,true));

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload Form</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
 <script type="text/javascript">
function submitimage() {
    var itemid=<?=$item->getItemID();?>;
    var dataString = { 'imagetype': '<?=$imagetype?>', 'imageid':'', 'action':'refresh', 'itemid':itemid }

    var request = $.ajax ({
        type: "POST",
        url: "/aj/images.php",
        data: dataString,
        cache: false,
        async: false
    });

    request.done(function (response, textStatus, jqXHR){
        if(response.success == true) {
            $("#itempics").html(response.message);
        }
        //if('<?=$_SESSION['ack2']?>' != '') {
        //    $("#ack2").empty();
        //    $("#ack2").text('<?=$_SESSION['ack2']?>');
        //} else {
        //    $("#ack2").empty();
        //    $("#ack2").text('');
        //}
    });


    request.fail(function (jqXHR, textStatus, errorThrown){
        $("#ack2").empty();
        $("#ack2").html("The following error occured: "+ textStatus, errorThrown);
        return false;
    });


};

$(document).ready(function() {
        //elements
        var progressbox     = $('#progressbox');
        var progressbar     = $('#progressbar');
        var statustxt       = $('#statustxt');
        var submitbutton    = $("#SubmitButton");
        var myform          = $("#UploadForm");
        var output          = $("#output");
        var completed       = '0%';

        $(myform).ajaxForm({
            beforeSend: function() { //brfore sending form
                submitbutton.attr('disabled', ''); // disable upload button
                statustxt.empty();
                progressbox.slideDown(); //show progressbar
                progressbar.width(completed); //initial value 0% of progressbar
                statustxt.html(completed); //set status text
                statustxt.css('color','#000'); //initial color of status text
            },
            uploadProgress: function(event, position, total, percentComplete) { //on progress
                progressbar.width(percentComplete + '%') //update progressbar percent complete
                statustxt.html(percentComplete + '%'); //update status text
                if(percentComplete>50)
                    {
                        statustxt.css('color','#fff'); //change status text to white after 50%
                    }
                },
            complete: function(response) { // on complete
                output.html(response.responseText); //update element with received data
                myform.resetForm();  // reset form
                submitbutton.removeAttr('disabled'); //enable submit button
                progressbox.slideUp(); // hide progressbar
                if('<?=$_SESSION['cpage']?>' == 'postit.php' || '<?=$_SESSION['cpage']?>' == 'postit-region.php') {
                    $(".popup_close").click();
                    submitimage();
                } else {
                    setTimeout("window.top.window.location = '<?=$_SESSION['cpage']?>'", 500);
                }
            }
    });
});

    </script>
 <link href="css/upload.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="stylized" class="myform">
<form action="processupload.php" method="post" enctype="multipart/form-data" id="UploadForm">
<input type='hidden' name='pos' value='<?=$position?>' />
<input type='hidden' name='imagetype' value='<?=$imagetype?>' />
<?php if($itemid) :?>
<input type='hidden' name='itemid' value='<?=$itemid?>' />
<?php endif; ?>
  <h1><?=$uptext?></h1>
  <fieldset>

  <div id="lresponse"></div>

  <label>File<span class="small"></span></label>
  <input type="file" name="ImageFile" id="usn" />

  <center><input type="submit"  id="SubmitButton" value="Upload" /></center>
  </fieldset>
</form>

</div>
<div id="progressbox"><div id="progressbar"></div><div id="statustxt">0%</div></div>
</body>
</html>
<?php
$_SESSION['ack2'] = '';
?>
