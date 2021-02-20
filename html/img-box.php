<?php
include_once("config.php"); //include the config
include_once("user.php");
include_once("image.php");
include_once("cImage.php");
include_once("Item.php");

$sess = new MySQLSessionHandler();
session_start();

$image = $_GET['img'] ?  $_GET['img'] : die();
$img = new cImage($image);

$itemid = !empty($_GET['itemid']) ?  $_GET['itemid'] : 0;
$item = new Item($itemid);

$user = new Users();
$lic = $user->login_check();
if($lic === true) {
    $userid = $user->getUserID();
    error_log($userid);
} else {
    $userid = false;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload Form</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jquery.popup.js"></script>
<script type="text/javascript" src="/js/jquery.form.js"></script>
 <script>

$(document).ready(function() {
//elements
var progressbox     = $('#progressbox');
var progressbar     = $('#progressbar');
var statustxt       = $('#statustxt');
var submitbutton    = $("#SubmitButton");
var myform          = $("#UploadForm");
var output          = $("#output");
var completed       = '0%';


$("#itemform").submit(function(event) {

        event.defaultPrevented();
        // if(request) {
        //      request.abort();
        //}

        //var $form = $("#myForm :input");
        var $inputs = $("#itemform").find("input, select, button, textarea");
        //var images = $("#itemimages").find("input");
        //if(images != '') {
        //    serializedData = inputs+'&'+images;
        //}

        //var values = {};
        var serializedData = $('#itemform,#itemimages').serialize();
        //alert(serializedData);
        //return;


        // let's disable the inputs for the duration of the ajax request
        $inputs.prop("disabled", true);

        var request = $.ajax({
                type: "POST",
                url: "aj/postit.php",
                dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
                //alert("success");
                $("#ack").empty();
                if(response.success == true) {
                    $("#ack2").html("Updating Your Settings! Hang Tight." + response.itemID);
                    //window.location = 'login.php';
                    <?php if($itemid) :?>
                        setTimeout("window.location = '/listings.php'",1500);
                    <?php else: ?>
                        window.location = 'rental.php?itemid='+response.itemID;
                        //setTimeout("window.location = '/rental.php?itemid='+response.itemID",1500);
                    <?php endif ?>

                    //profile.php'", 1500);
                    return true;
                } else {
                    $("#ack2").html(response.error);
                    return false;
                }
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
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

        // prevent default posting of form
        event.defaultPrevented();
        return false;
});



$(".delete,.primary").click(function(event) {
    event.preventDefault();
    var id=<?=$img->getImageID();?>;
    //var itemid=<?=$item->getItemID();?>;
    var itemid=<?=$itemid;?>;
    var action = $(this).attr('class');
    var dataString = { 'imagetype': 'listing', 'imageid':id, 'action':action, 'itemid':itemid }
    //alert("img-box the selected value is " + dataString);

    var request = $.ajax ({
        type: "POST",
        url: "/aj/images.php",
        data: dataString,
        cache: false,
        async: false
    });

    request.done(function (response, textStatus, jqXHR){
        //$("#ack2").html(response.message);
        //alert(response.message);
        //$("#ack2").empty();
        //$("#ack2").html(response.message);
        if(response.success == true) {
            $("#itempics").html(response.message);
            $(".popup_close").click();
            //var popup = $('.default_popup').data('popup');
            //var popup = this.data('popup');
            //popup.close();
            //window.top.popclose();
            // Hammer
            <?php if(isset($_SESSION['cpage']) && $_SESSION['cpage'] == 'profile.php') { ?>
              setTimeout("window.top.location = 'profile.php'", 100);
            <?php }?>
            //setTimeout("window.top.default_popup.close()", 500);
            //alert(response.message);
             //$('.default_popup').close();
        }
    });


    request.fail(function (jqXHR, textStatus, errorThrown){
        $("#ack2").empty();
        $("#ack2").html("The following error occured: "+ textStatus, errorThrown);
        return false;
    });


});
});

    </script>
 <link href="/css/upload.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/css/upload.css')?>" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="img-box" class="img-box">
<img src='<?=$img->GetListingImageSrc(400);?>'>
<!--div class="lbuttons"><a href="<?=$_SESSION['cpage']?>" class='primary'>Make Primary</a></div>
<div class="rbuttons"><a href="<?=$_SESSION['cpage']?>" class='delete'>Delete</a></div-->
<?php
$owner = $img->getOwner($image);

if($userid === $owner) {

if($_SESSION['cpage'] == 'profile.php' || $_SESSION['cpage'] == '/profile.php' ): ?>
<div class="cbuttons"><a href="#" class='delete'>Delete</a></div>
<?php elseif($_SESSION['cpage'] == 'postit.php' || $_SESSION['cpage'] == '/postit.php' ):?>
<div class="lbuttons"><a href="#" class='primary'>Make Primary</a></div>
<div class="rbuttons"><a href="#" class='delete'>Delete</a></div>
<?php endif;
}
?>
</div>
</body>
</html>
