<?php

// Yes, I should use the PDO, but this was already written so I'm not going
// to fuss with it at them moment. If you're reading this, have fun with it.
// JLG 7/12/2013

include_once("config.php"); //include the config
include_once("user.php"); //include the config
include_once("validate.php"); //include the config

$mypagetype='nosb';

/*
$boxstyles = array(
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");
*/

include "top.php";

$title = "Activation Page";
include "wide1.php";

// Can change to PUT here and use the class from now on
$user = new Users($_GET);
$user->checkuid($_GET['userID']); // Sets the UserID. Fixing a security flaw the ugly way. JLG
$val = new Validate();

if($user->checkstate()):
?>
<div id="stylized" style="margin: 0 auto" class="myform">
  <form id="recover" name='regform' method="post" action="welcome.php">
  <h1>Welcome .... back?</h1>
  <fieldset>
  <center>Your account has already been activated. There is no need to activate a second time.</center>
  </fieldset>
</form>
<?php
elseif($user->activate()):
?>
<div id="stylized" style="margin: 0 auto" class="myform">
  <form id="recover" name='regform' method="post" action="welcome.php">
  <h1>Welcome to <?php echo SHORTDOM;?></h1>
  <fieldset>
  <center>Welcome! Your account has been activated. We hope you enjoy your stay.</center>
  </fieldset>
</form>
<?php
else :
?>
<div id="stylized" style="margin: 0 auto" class="myform">
  <form id="recover" name='regform' method="post" action="welcome.php">
  <h1>Recovery form</h1>
  <fieldset>
  <div id="ack">I'm sorry. I cannot find the account associated with this activation. If you would like another email, please enter your email address.</div>
  <label>Email<span class="small"></span></label>
  <input type="text" name="email" id="email" /><br />
  <div style="text-align:center;">
  <center><input type="submit" class="center" value="Request a new activation email" /></center>
  </div>
  </span>
  </fieldset>
</form>
<?php
endif;

include "wide2.php";
include "bottom.php";

?>

