<?php

include_once("config.php"); //include the config
include_once("user.php");

$sess = new Session();
$sess->start_session('_s', false);

$_SESSION['cpage'] = "limitations.php";
$mypagetype='nosb';

$boxstyles = array(
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

$title = "Limitation of Liability";

include "top.php";
include "wide1.php";

?>

<h2>This limits our liability. Please Review before renting any equipment.</h2>
<p>Some equipment is dangerous. Do not rent the equipment unless you know how to properly operate it. Do not use the equipment without proper protective gear. Zalaxy, Inc is only a broker and has no technical expertise on the operation of equipment and machinery. Please seek expert advice before using.</p>

<?php
include "wide2.php";
include "bottom.php";

?>

