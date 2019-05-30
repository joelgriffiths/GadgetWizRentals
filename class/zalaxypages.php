<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="/favicon.ico" rel="icon" type="image/x-icon" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="default.css?v=<?php echo filemtime('default.css'); ?>" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" type="text/css" href="layout.css?v=<?php echo filemtime('layout.css'); ?>">
<?php if ($mypagetype == 'ad' || $mypagetype == 'list' || $mypagetype == 'home'): ?>
<!--[if IE 6]>
<link href="ie6fix.css" rel="stylesheet" type="text/css" />
<![endif]-->
<script type="text/javascript" src="jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery.formerize-0.1.js"></script>
<?php endif; ?>
</head>
<body>
<div id="header-bg">
	<div id="header" class="container">
		<div id="logo">
			<h1><a href="#">Zalaxy.com</a></h1>
			<p>Lorem ipsum veroeros</p>
		</div>
		<div id="menu">
			    <div id="login-wrapper">
                                 <ul>
				<li><a href="login.php" accesskey="3" title=""><span><span>Sign In</span></span></a></li>
				<li><a href="register.php" accesskey="3" title=""><span><span>Register</span></span></a></li>
                                 </ul>
                            </div>
			<ul>
				<li class="active"><a href="#" accesskey="1" title=""><span><span>Home</span></span></a></li>
				<li><a href="#" accesskey="2" title=""><span><span>Categories</span></span></a></li>
				<li><a href="#" accesskey="3" title=""><span><span>Post an Ad</span></span></a></li>
				<li><a href="#" accesskey="4" title=""><span><span>Help &amp; Info</span></span></a></li>
				<li><a href="#" accesskey="5" title=""><span><span>Contact</span></span></a></li>
			</ul>
		</div>
	</div>
</div>
<div id="page-bg">
	<div id="page" class="container">
<?php if ($mypagetype == 'ad' || $mypagetype == 'list' || $mypagetype == 'home'): ?>
		<div id="content">
<?php elseif ($mypagetype == 'nosb'): ?>
		<div id="wide-content">
<?php
endif;

if ($mypagetype == 'home'): ?>
			<div id="box1" class="box-style1">
				<div class="t"></div>
				<div class="content"> <img src="images/homepage_13.jpg" alt="" width="130" height="120" class="alignleft" />
					<h1>Tempus blandit aliquam?</h1>
					<p class="text"><strong>Aliquam lectus nulla</strong> sollicitudin nec viverra sed lorem. Cras rutrum mattis duis sed dolor.</p>
					<p class="more"><a href="#" class="button button-style1"><span>Sed Faucibus</span></a> 
				</div>
				<div class="b"></div>
			</div>
<?php endif; ?>
			<div class="box-style2">
				<div class="content">
					<h2>New Listings</h2>
					<div class="links"> ( <a href="#">See Them All</a> | <a href="#">Post Your Own</a> ) </div>
				</div>
			</div>


			<div class="box-style1">
				<div class="t"></div>
				<div class="content">
					<ul class="list-style1">
						<li class="featured first">
							<div class="image"><a href="#"><img src="images/homepage_06.jpg" alt="" width="190" height="166" /></a></div>
							<div class="price"><span>$125</span></div>
							<div class="info">
								<h3><a href="#">NEW Sed veroeros blan</a></h3>
							</div>
						</li>
						<li class="featured">
							<div class="image"><a href="#"><img src="images/homepage_07.jpg" alt="" width="190" height="166" /></a></div>
							<div class="price"><span>$25</span></div>
							<div class="info">
								<h3><a href="#">Donec tortor eget diam...</a></h3>
							</div>
						</li>
						<li class="featured">
							<div class="image"><a href="#"><img src="images/homepage_08.jpg" alt="" width="190" height="166" /></a></div>
							<div class="price"><span>$50</span></div>
							<div class="info">
								<h3><a href="#">Sed lacinia tempus dolor</a></h3>
							</div>
						</li>
						<li>
							<div class="image"><a href="#"><img src="images/homepage_09.jpg" alt="" width="47" height="41" /></a></div>
							<div class="price"><span>$50</span></div>
							<div class="info">
								<h3><a href="#">Nunc dignissim lacus condimentum ut convallis</a></h3>
								<p>2 hours ago (Madison, TN)</p>
							</div>
						</li>
						<li>
							<div class="image"><a href="#"><img src="images/homepage_10.jpg" alt="" width="47" height="41" /></a></div>
							<div class="price"><span>$50</span></div>
							<div class="info">
								<h3><a href="#">Risus cursus praesent consequat</a></h3>
								<p>2 hours ago (Murfreesboro, TN)</p>
							</div>
						</li>
						<li>
							<div class="image"><a href="#"><img src="images/homepage_11.jpg" alt="" width="47" height="41" /></a></div>
							<div class="price"><span>$50</span></div>
							<div class="info">
								<h3><a href="#">Tellus sit amet enim sollicitudin tincidunt</a></h3>
								<p>4 hours ago (Nashville, TN)</p>
							</div>
						</li>
					</ul>
					<div class="bottom-nav"> <a href="#" class="button button-style2"><span>Browse All Listings</span></a> <a href="#" class="button button-style3"><span>Post an Ad</span></a> </div>
				</div>
				<div class="b"></div>
			</div>
<?php if ($mypagetype == 'ad' || $mypagetype == 'list' || $mypagetype == 'home' || $mypagetype == 'nosb'):?>
		</div>
<?php endif; ?>

<?php
if ($mypagetype == 'ad' || $mypagetype == 'list' || $mypagetype == 'home')
	include "categories.php";
elseif ($mypagetype != 'nosb')
	include "sidebar.php";
?>
	</div>
</div>
<?php include("footer.php");
