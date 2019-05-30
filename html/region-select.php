<?php
include_once("config.php"); //include the config
include_once("user.php");
include_once("Item.php");
include_once("Regions.php");

$sess = new Session();
$sess->start_session('_s', false);

$country = isset($_SESSION['country']) ?  $_SESSION['country'] : null;
$city    = isset($_SESSION['city']) ?  $_SESSION['city'] : null;
$state   = isset($_SESSION['state']) ?  $_SESSION['state'] : null;

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
<title>Change Region</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jquery.popup.js"></script>
<script type="text/javascript" src="/js/jquery.form.js"></script>

<link href="/css/region.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/css/region.css')?>" rel="stylesheet" type="text/css" />
</head>
<body>

<form id="regionform" name='regionform' method="post" action="#">
<center>
  <div id="ack2"></div>
  <fieldset>
  <legend>Select Search Region</legend>

  <label><span class="small"></span></label>
  <!--label class="left">Category<span class="small"></span></label><br /><br/-->
  <select name="country" class="country">
<?php
$mystyle = '';
$region = new Regions();
//error_log(print_r($region,true));
echo $region->printSelectedCountries($country);
$country = $region->getCountry();
?>
  </select><br />

<?php if($country) $mystyle=" style='visibility: visible' ";?>
  <select name='state' class='state' <?=$mystyle?>>
<?php
echo $region->printSelectedStates($state);
$state = $region->getState();
?>
  </select>

<?php if(isset($state)) $mystyle=" style='visibility: visible' ";?>
  <select name='city' class='city' <?=$mystyle?>>
<?php
echo $region->printSelectedCities($city);
$city = $region->getCity();
?>
  </select></br>

    <!--br /><label>Maximum Distance</label><br />
    <select name="searchradius" id="searchradius">
<?php
$searchoptions = array(1,5,10,15,20,25,30,40,50,60,70,80,90,100,200,500);
foreach($searchoptions as $option) {
    $selected = $option == $_SESSION['searchradius'] ? 'selected' : '';
    echo "    <option value='$option' $selected>$option</option>\n";
}
?>
    </select>
    <select name="searchunits" id="searchunits">
<?php if($_SESSION['searchunits'] == 'K') : ?>
    <option value='K'>KM</options>
    <option value='M'>Miles</options>
<?php else: ?>
    <option value='M'>Miles</options>
    <option value='K'>KM</options>
<?php endif; ?>
    </select>

  <br /-->
  <input type='hidden' name="searchradius" id="searchradius" value='50'>
  <input type='hidden' name="searchunits" id="searchunits" value='M'>

  <input type="button" id="setlocations" value="Done" />
  <input type="button" id="clearlocations" value="Clear" />
  <!--div id="closecities"></div-->
  </fieldset>
</center>
</form>
 <script>
<!--
$(document).ready(function() {
function processit(event, thischange) {
    event.preventDefault();
    //var id=$(this).val();
    //var thischange = $(this).attr('class');
    var dataString = 'selectid='+thischange+'&'+$("#regionform").serialize();
    //alert("the selected value is "+$(this).attr('class')+":"+dataString);

    var $inputs = $("#regionform").find("input, select, button, textarea");
    $inputs.prop("disabled", true);

    var request = $.ajax ({
        type: "POST",
        url: "/aj/regionselect.php",
        data: dataString,
        cache: false
    });

    request.done(function (response, textStatus, jqXHR){
        $("#ack2").empty();
        //alert('<?$_SESSION['cpage']?>');
        if(response.success == true) {
            $(".country").html(response.countries);

            $(".state").html(response.states);
            //$(".category2").css('visibility',"visible");

            $(".city").html(response.cities);
            $("#closecities").empty();
            //$("#closecities").html("<pre>HELLO</pRE>");
            $("#closecities").html(response.closecities);
            if( thischange == 'submit' || thischange == 'clear' ) {
                setTimeout("window.top.window.location = '<?=$_SESSION['cpage']?>'", 100);
            }
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

}

$(".country,.state").change(function(event) {
    var thischange = $(this).attr('class');
    processit(event, thischange);
});

$("#clearlocations").click(function(event) {
    processit(event, 'clear');
});

$("#setlocations").click(function(event) {
    processit(event, 'submit');
});

$("#regionform").submit(function(event) {
    alert("Not used");
    processit(event, 'submit');
});


});
-->
</script>


</body>
</html>
