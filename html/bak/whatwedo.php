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

$title="What We Do";
include "top.php";
include "rightsb1.php";
?>
			<div class="t"></div>
			<div id="content">
				<div class="content">
					<div>
<h2 class="title">We connect people</h2>
<p><img src="images/subpage1_04.jpg" alt="" width="160" height="140" class="alignleft" />Thanks for visiting <?=MAINSITE?>.</p>
<p>You are witnessing the start of a new venture. The site went live Septmeber 8th, 2013. There are still many features to implement, but you should find the site completelety functional at this time. If you do see a bug, or want additional features, <a href="contactus.php">We would love to hear from you.</a> There is however, a pretty good chance your feature is in the works.</p>

<h2 class="title">How We Started</h2>
<p>This site was initially conceived by me, <a href="http://www.joelgriffiths.com">Joel Griffiths</a>. It's an idea I've been tossing around for years. About seven years ago, I hired a developer to write the software. Yes, I am a programmer (among other things), but I didn't have the drive to work full time and then spend my time off  programming at home. Unfortunately, the software he wrote was inoperable. It would require a full rewrite. I was disheartened and put the project aside.</p>

<h2 class="title">What Changed</h2>

<p>I continued to work for several years, letting my jobs guide my course. I was just drifting. <!--I had no life goal, no savings goals, no savings. Bad choices cost me my house and car (I still miss that car). My income dropped almost 20%. The combined effect of all this was that I started to wake up.--> The whole time, this website, this idea, kept haunting me. It seemed like it could fullfill an unmet need. Even after all these years, nobody was offering a complete peer to peer rental service. I had to do something. </p><!--It didn't happen all at once, but I started to read books like Rich Dad, Poor Dad and The Richest Man in Babylon. My gears started turning.</p-->

<!--p>I found a way to stuff 10% of my income into savings. I have no idea how I pulled it off, but "Pay Yourself First" was among one of the most important things I've ever heard.</p-->

<p>On May 15'th, I received an email that changed everything. It was a simple request:</p>
<pre>
Hello!

I want to buy domain zalaxy.com

Best regards,

Archi Zelig
</pre>
<p>I actually considered selling the domain. I even responded to the email, but Archi never answered my response. I'm grateful for that, because it started my engine.</p>

<h2 class="title">Guiding My Own Course</h2>
<p>I started working on the site. For the first time in my life, I was guiding my own course! Even if it was just 15-20 hours a week, I was actively participating in my future. It felt good. I wanted more so I kept increasing the time I spent on this site. I incoroprated, sold the domain to Zalaxy Inc, and started working an ungodly number of hours for free.</p>

<p>The site is now nearing go-live. It will go live on Septmember 1st, but it will still be missing a number of featured. Don't worry. We're working night and day to fill those gaps. If you have suggestions or ideas, you can contact us through our <a href="http://www.<?=SHORTDOM?>.com/contact.php">Contact Form</a> or you can call me directly at (530) 388-5635.</p>

<p>I sincerely hope <?=MAINSITE?> will help convert that old thing in your garage to cash in your wallet. Everybody can use a little financial boost once in awhile and I hope this site provides it for you and, at the same time, provides good deals for your customers.</p>

<h3 style="text-transform: none;">Joel Griffiths, President</h3>
<p>July 25, 2013</p>

					</div>
				</div>
			</div>
			<div id="sidebar">
				<div id="box2">
                    <h2>Rent From Your Neighbor!</h2>
                    <h2>Rent To Your Neighbor!&trade;</h2>
                </div>
                <br />
                <br />
                <br />
                <br />
                <div id="box3">
                    <h2>What do <strong>YOU</strong> have in your Back Yard?</h2>
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
