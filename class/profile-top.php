<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("image.php");
include_once("userinfo.php");
include_once("Item.php");

$mypagetype='nosb';
$boxstyles = array( 
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");


$images = new Image();
$title = "Profile";

$images = new Image();

include "top.php";
$_SESSION['uptext'] = "Upload Your New Profile Image";

if(empty($_GET['itemid']))
    $itemid = 0;
else
    $itemid = $_GET['itemid'];

$userid = $user->getUserID();
$userinfo = new UserInfo($userid);
$images->deleteAllOldImages($userid, 'profile');


//$itemid = !empty($_GET['itemid']) ? $_GET['itemid'] : 0;
//
// $item = new Item($itemid);

?>

<?php
include "amenu.php";
?>

<!-- TITLE -->
<!--div class="box-style2">
    <div class="content">
        <h2>Your profile information</h2>
    </div>
</div-->

<!-- Content Area with -->
<div class="box-leftsb">
    <div class="t"></div>
    <div id="content">
        <div class="content">
