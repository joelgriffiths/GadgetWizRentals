<?php

include_once("config.php"); //include the config

$mypagetype='nosb';

// Here for reference
$boxstyles = array( 
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

$nextpage = isset($_GET['nextpage']) ? $_GET['nextpage'] : '/';

include "top.php";

$_SESSION = array();
//$params = session_get_cookie_params();
// Delete the actual cookie.
//setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
// Destroy session
session_destroy();
session_unset();


$title = "Thanks for visiting!";
include "wide1.php";
?>

<script>
<!--
$(document).ready(function(){
    setTimeout("window.location = '<?=$nextpage?>'", 1500);
});
-->
</script>

<div id="stylized" class="myform">
  <div id="ack">
    You account has been logged out.<br />
  </div>
</div>

<!-- End Content -->
<?php
include "wide2.php";
include "bottom.php";
?>
