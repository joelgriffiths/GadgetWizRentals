<?php
include_once("functions.php");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="/favicon.ico" rel="icon" type="image/x-icon" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="default.css?v=<?php echo filemtime('default.css'); ?>" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" type="text/css" href="layout.css?v=<?php echo filemtime('layout.css'); ?>" />
<link rel="stylesheet" type="text/css" href="css_pirobox/style_1/style.css"/>
<?php if ($mypagetype == 'ad' || $mypagetype == 'list' || $mypagetype == 'home'): ?>
<!--[if IE 6]>
<link href="ie6fix.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--script type="text/javascript" src="jquery-1.4.2.min.js"></script-->
<?php endif; ?>


<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="js/jquery.formerize-0.1.js"></script>
<script type="text/javascript">
$(document).ready(function() {
        $().piroBox_ext({
        	piro_speed : 700,
                bg_alpha : 0.5,
                piro_scroll : false // pirobox always positioned at the center of the page
        });
});
</script>
<?php if ($_SERVER['SCRIPT_NAME'] == '/TODOregister.php'): ?>
<script type="text/javascript" src="/js/register.js?v=<?php echo filemtime('js/register.js'); ?>"></script>
<?php endif; ?>
</head>
<?php if ($mypagetype == 'leftsb'): ?>
<body class="subpage1">
<?php elseif ($mypagetype == 'rightsb'): ?>
<body class="subpage2">
<?php else: ?>
<body>
<?php endif;?>
<div id="header-bg">
	<div id="header" class="container">
		<div id="logo">
			<h1><a href="#">Zalaxy.com</a></h1>
			<p>Rent your thing!</p>
		</div>
		<div id="menu">
                                 <ul style="text-align: right;">
<?php
$_SESSION['cpage'] = basename($_SERVER['PHP_SELF']);

$user = new Users();
$lic = $user->login_check();
if($lic === true):
?>
<li style="font-size: 120%;color: #aabb33;"><?=$user->username?></li>
<li style="font-size: 100%" <?php activate("profile.php");?>><a href="profile.php" accesskey="3" title=""><span><span>Profile</span></span></a></li>
<li style="font-size: 100%" <?php activated("account.php");?>><a href="account.php" accesskey="3" title=""><span><span>Account</span></span></a></li>
<li style="font-size: 100%" <?php activated("logout.php");?>><a href="logout.php" accesskey="3" title=""><span><span>Sign Out</span></span></a></li>
<?php else: ?>
<li style="font-size: 100%" <?php activated("login.php");?>><a href="login.php" accesskey="3" title=""><span><span>Sign In</span></span></a></li>
<li style="font-size: 100%" <?php activated("register.php");?>><a href="register.php" accesskey="3" title=""><span><span>Register</span></span></a></li>
<?php endif; ?>



                                 </ul>
			<ul>
				<li <?php activated("index.php");?>><a href="http://www.zalaxy.com/" accesskey="1" title="Go Home"><span><span>Home</span></span></a></li>
				<li <?php activated("categoriws.php");?>><a href="categoriws.php" accesskey="2" title="Browse Categoried"><span><span>Categories</span></span></a></li>
				<li <?php activated("postit.php");?>><a href="postit.php" accesskey="3" title="List an item"><span><span>Post an Ad</span></span></a></li>
				<li <?php activated("help.php");?>><a href="#" accesskey="4" title="Get Help"><span><span>Help &amp; Info</span></span></a></li>
				<li <?php activated("contact.php");?>><a href="contact.php" accesskey="5" title="Contact Us"><span><span>Contact</span></span></a></li>
			</ul>
		</div>
	</div>
</div>
<div id="page-bg">
	<div id="page" class="container">
<?php if ($mypagetype == 'ad' || $mypagetype == 'list' || $mypagetype == 'home'): ?>
		<!--div id="content"-->
<?php elseif ($mypagetype == 'nosb'): ?>
		<!--div id="wide-content"-->
<?php
endif;
?>

<h2 class="my_title"> <a  href="content/login.html" title="Login form" rel="content-310-260" class="pirobox_gall1">â<span class="span__c">HTML Content </span><b style="font-size:12px;">Open </b><span style="font-size:10px;">(310x260px)</span></a></h2>


<!-- END TOP -->

