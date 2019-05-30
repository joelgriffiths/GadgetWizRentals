<?php

include_once("config.php"); //include the config
include_once 'user.php';

$mypagetype='nosb';

// Here for reference
$boxstyles = array( 
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

include "top.php";

$user = new Users();
if($user->checkuid($_GET['userID'])):
$title = "Welcome to ".MAINSITE."!";
include "wide1.php";
?>

<div id="stylized" class="myform">
  <div id="ack">
    <center>
    Your account has been created.<br />
    Please check your email for an authorization key.<br />
    The email may be in your Bulk Mail folder.<br />
    <small>(We don't spam, but some large providers don't trust us yet)</small><br />
    </center>
  </div>
</div>

<?php
else:
$title = "Shovel, CPU, ouch.";
include "wide1.php";
?>

<div id="stylized" class="myform">
  <div id="ack">
    <center>Wierd.</center><br />
    <center>Something is not right.</center>
  </div>
</div>

<!-- End Content -->
<?php
endif;
include "wide2.php";
include "bottom.php";
?>
