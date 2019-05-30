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

$title="Listings";
include "includes/top.php";
include "includes/accountmenu1.php";
?>

<div id="stylized" class="myform">
<form id="regform" name='regform' method="post" action="welcome.php">
  <h1>Login Information</h1>
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
</div>
 
<!-- End Content -->
<?php
include "includes/accountmenu2.php";
include "includes/bottom.php";
?>
