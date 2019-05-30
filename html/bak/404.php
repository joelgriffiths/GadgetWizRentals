<?php

include_once("config.php"); //include the config
include_once("user.php");

$mypagetype='rightsb';

$boxstyles = array( 
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

$title="Ooops! 404 Error!";
include "top.php";
include "rightsb1.php";
?>
			<div class="t"></div>
			<div id="content">
				<div class="content">
					<div>
<h2 class="title">Lucky Us - 404 Error</h2>
<p><img src="/images/subpage1_04.jpg" alt="" width="160" height="140" class="alignleft" />Thanks for stumbing across our company.</p>
<p>Welcome to <a href="/"><?=MAINSITE?></a>. This site is designed to let you rent items to and from your Neighbors. If you have something to rent (almost everybody has something), or you are looking for something, you're at the right place, though probably not on the correct page.</p>
<p>If you have questions/suggestions about us, <a href="contact.php">we're all ears</a>.</p>

<p>If you're interested in what we do, visit our <a href="/">Home Page</a> and take a look around.</p>

					</div>
				</div>
			</div>
			<div id="sidebar">
				<div id="box2">
					<h2>Rent From Your Neighbor!</h2>
					<h2>Rent To Your Neighbor!&trade;</h2>
					<ul class="list-style4">
					</ul>
				</div>
                <br />
                <br />
                <br />
                <br />
				<div id="box3">
					<h2>What do <strong>YOU</strong> have in your Back Yard?</h2>
					<ul class="list-style4">
					</ul>
				</div>
				<div>
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="b"></div>
	

<?php
include "rightsb2.php";
include "bottom.php";
?>
