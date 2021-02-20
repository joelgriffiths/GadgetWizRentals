<?php 
include_once "config.php";
include_once 'session.php';
//$sess = new Session();
//$sess->start_session('_s', false);
$sess = new MySQLSessionHandler();
session_start();


$active[basename($_SERVER['PHP_SELF'])] = 'class="active"';;
$thistextpage = basename($_SERVER['PHP_SELF']);

$cpage = basename($_SERVER['REQUEST_URI']);
// Don't reset refpage on reloads
if(isset($_SESSION['cpage']) && $_SESSION['cpage'] != $cpage) {
    $_SESSION['refpage'] = strip_tags($_SESSION['cpage']);
}
$_SESSION['cpage'] = $cpage;

$country = isset($_SESSION['country']) ? $_SESSION['country'] : '';
$state = isset($_SESSION['state']) ? $_SESSION['state'] : '';
$city = isset($_SESSION['city']) ? $_SESSION['city'] : '';
$closelocations = isset($_SESSION['closelocations']) ? $_SESSION['closelocations'] : '';
error_log("<<<<<<<<<<<<<<<<<<".$_SESSION['refpage']);
error_log(">>>>>>>>>>>>>>>>>>".$_SESSION['cpage']);

include_once "functions.php";
include_once "User.php";

// May use this later for different countries
setlocale(LC_MONETARY, 'en_US');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$title?></title>
<link href="/images/<?=SHORTDOM?>-favicon.ico" rel="icon" type="image/x-icon" />
<meta name="keywords" content="peer to peer rentals" />
<meta name="msvalidate.01" content="ADE74ED59CD634535EB33EFFF6738A2E" />
<meta name="norton-safeweb-site-verification" content="rao-urv3oez6er7e04b2hmd5r84wbs7mymj0ig9oyfxn5khm2d-8rwcespwuiuqi1ngv6qv5oq-mb0yxo6jo0md2wwqbh6-ett-5coqynz1w4fhst56e075xltsme48a" />
<meta name="description" content="<?=$description?>" />
<?php // =filemtime($_SERVER['DOCUMENT_ROOT'].'/css/upload.css')  ;?>
<link href="/css/default.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/css/default.css')?>" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" type="text/css" href="/css/layout.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/css/layout.css')?>" />
<link rel="stylesheet" type="text/css" href="/css/popup.css" />
<?php if ($mypagetype == 'ad' || $mypagetype == 'list' || $mypagetype == 'home'): ?>
<!--[if IE 6]>
<link href="ie6fix.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--script type="text/javascript" src="jquery-1.4.2.min.js"></script-->
<?php endif; ?>
<script type="text/javascript" src="/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="/js/jquery.formerize-0.1.js"></script>
<script type="text/javascript" src="/js/jquery.popup.js"></script>
<?php if ($thistextpage == 'listings.php' || $thistextpage == 'checkout.php' || $thistextpage == 'reservations.php' || $thistextpage == 'itemsrented.php' || $thistextpage == 'fbsummary.php' ) : ?>
    <link rel="stylesheet" type="text/css" href="/css/tables/<?=$color?>.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/css/tables/'.$color.'.css')?>" />
    <link rel="stylesheet" href="/themes/base/jquery.ui.all.css" />
    <script type="text/javascript" src="/ui/jquery.ui.core.js"></script>
    <script type="text/javascript" src="/ui/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="/ui/jquery.ui.datepicker.js"></script>
<?php endif; ?>
<?php
// Gonna make it so I can load a different css file for every page
// to override the functions I wanna override.
$CSSfile = '/css/'.basename("$thistextpage","php").'css';
$OSfile = $_SERVER['DOCUMENT_ROOT'].$CSSfile;
if(file_exists($OSfile) === true) {
    $filetime = filemtime($OSfile); ?>
    <link rel="stylesheet" type="text/css" href="<?=$CSSfile?>?v=<?=$filetime?>" />
<?php } ?>

<script type="text/javascript" src="/js/contactus.js?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/js/contactus.js')?>"></script>
<!-- Piwik -->
<script type="text/javascript"> 
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://track.zalaxy.com//";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '<?=SITEID?>']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
    g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();

</script>
<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-4766902-3', 'zalaxy.com');
  ga('send', 'pageview');

</script>
<!-- End Piwik Code -->
</head>
<?php if ($mypagetype == 'leftsb'): ?>
<body class="subpage1">
<?php elseif ($mypagetype == 'rightsb'): ?>
<body class="subpage2">
<?php else: ?>
<body>
<noscript><p><img src="http://track.zalaxy.com/piwik.php?idsite=1" style="border:0" alt="-" /></p></noscript>
<?php endif;?>
<div id="fb-root"></div>
<script type="text/javascript">(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<script type="text/javascript">
    $('.region_select').popup({
        width: '400',
        height: '250'
    });
</script>

            <div class="social-button-container">
                <div class="social-button">
                     <script type="text/javascript">
                     <!--
                        //document.write('<div class="fb-like" data-href="http://<?=MAINSITEURL?>/" data-width="450" data-colorscheme="light" data-layout="box_count" data-show-faces="true" data-send="true"></div>');
                        document.write('<div class="fb-like" data-href="https://www.facebook.com/zalaxyinc" data-width="100" data-layout="box_count" data-show-faces="false" data-send="false"></div>');
                    -->
                    </script>
                    <!--div class="fb-like" data-href="#" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false"></div-->
                </div>
                <div class="social-button">
<script type="text/javascript">
<!--
document.write('<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?=MAINSITEURL?>" data-via="zalaxyinc" data-related="joelgriffiths" data-count="none" data-hashtags="rentyourthing">Tweet</a>');
-->
</script>
<script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                </div>
                <!--
                <div class="social-button">
                    <div class="g-plusone" data-size="medium" data-width="100" data-href="#"></div>
                </div>
                -->

                <!-- 
                     <div class="clear"></div> here
                    note in CSS.
                -->
            </div>
<div id="header-bg">
	<div id="header" class="container">
		<div id="logo">
			<h1><a href="http://<?=MAINSITEURL?>/" style="background: url(/images/<?=SHORTDOM?>-logo-small.png) no-repeat left top; background-size: auto 50px;"><?=MAINSITE?></a></h1>
			<p>Rent your thing!</p>
		</div>
		<div id="menu">
                                 <ul style="text-align: right;">
<?php

$user = new User();
$lic = $user->login_check();

if($lic === true):
?>
<!--li style="font-size: 80%;color: #aabb33;"><?=$user->username?></li-->
<li <?php activated("index.php");?>><a href="http://<?=MAINSITEURL?>/" accesskey="1" title="Go Home"><span><span>Home</span></span></a></li>
<li <?php
activated("account.php");
activated("profile.php");
activated("inbox.php");
activated("listings.php");
activated("reservations.php");
activated("itemsrented.php");
activated("fbsummary.php");
activated("profile-contact.php");
activated("profile-password.php");
activated("profile-availability.php");
?>><a href="/profile.php" accesskey="3" title=""><span><span>Account</span></span></a></li>
<li <?php activated("postit.php");?>><a href="/postit.php" accesskey="3" title="List an item"><span><span>Post an Ad</span></span></a></li>
<li <?php activated("contact.php");?>><a href="/contact.php" accesskey="3" title=""><span><span>Contact Us</span></span></a></li>
<li <?php activated("logout.php");?>><a href="/logout.php" accesskey="3" title=""><span><span>Sign Out</span></span></a></li>
<?php else: ?>
<li <?php activated("login.php");?>><a href="/login.php" accesskey="3" title=""><span><span>Sign In</span></span></a></li>
<li <?php activated("postit.php");?>><a href="/register.php" accesskey="3" title="List an item"><span><span>Post an Ad</span></span></a></li>
<li <?php activated("contact.php");?>><a href="/contact.php" accesskey="3" title=""><span><span>Contact Us</span></span></a></li>
<li <?php activated("register.php");?>><a href="/register.php" accesskey="3" title=""><span><span>Register</span></span></a></li>
<?php endif; ?>

                                 </ul>
		</div><!-- id="menu" -->
	</div>
</div>
<div id="page-bg">
<!--
<?php if($closelocations != '') :?>
<div id='regionselect'><span>
<a href="http://www.zalaxy.com/region-select.php" id='searchcountry' class="regionbutton small region_select"><?=$country?></a>
<a href="http://www.zalaxy.com/region-select.php" id='searchstate' class="regionbutton small region_select"><?=$state?></a>
<a href="http://www.zalaxy.com/region-select.php" id='searchcity' class="regionbutton small region_select"><?=$city?></a>
</span></div>
<?php else: ?>
<div id='regionselect'><span>
<a href="http://www.zalaxy.com/region-select.php" id='searchglobal' class="regionbutton small region_select">Search Globally</a>
</span></div>
<?php endif; ?>
-->
	<div id="page" class="container">
<?php
include "authcheck.php";
?>
<!-- END TOP -->

