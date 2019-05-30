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


<ul class="list-style1">
<li>

<div class="image"><a href="#"><img src="images/homepage_07.jpg" alt="" width="47" height="41" /></a></div>
<div class="price"><span>7/30/13</span></div>
<div class="info">
<h3>Potential and Luck</h3>
<br />
<p>I've rolled back the product announcement date. I was always concerned August 17 was too early, so I'm pushing it out until August 30.</p>
<br />
<p>I am excited to go live though. I've started sharing details with people I know and almost everybody has responded with "That's a good idea." I appreciate the positive feedback. Frankly, it's more positive than I had expected. I was prepared to face headwinds bringing this together, so it's surprising. Don't get me wrong. It's good...but surprising.</p>
<br />
<p>While I really like the fact that others see the promise, I worry that it may be difficult to monetize it. I've done some preliminary math (I need to do more), and it's definitely doable. It's not going to happen overnight though.</p>
<br />
<p>People have great ideas all the time. It takes more than an idea to make something successful. It takes work.  It takes persistence. It takes the ability to adjust to changing requirements. It takes the sense to see those changing needs. Open control systems don't generally function very well. I was going to say, "it takes luck," but I don't believe luck has much to do with it. Today, I'm driving my own future. Living life by default waiting for a lucky stock option payday seems foolish by comparison.</p>
<br />
<p>Okay. Off to bed. I couldn't sleep worth a damn tonight and tossed for two hours before coming in here to write this. I'm going to see if I can actually fall asleep now. Good night world!</p>
<br />
<br />
</div>



<div class="image"><a href="#"><img src="images/homepage_09.jpg" alt="" width="47" height="41" /></a></div>
<div class="price"><span>7/28/13</span></div>
<div class="info">
<h3>Image Uploads, Password Resets, Profile Updates</h3>
<br />
<p>Wow. It's been a busy week. I spent more than 40 hours on this site this week (in addition to my full-time job and the gym). I've got alot done and moved the go live date from 12/1 to 10/20. The product announcement is planned for August 17. I'm not sure I will be able to meet that, but the go-live date is looking promising.</p>
<br />
<p>Things I've done the weekend (yes, this weekend:</p>
<ul>
<li>Profile Image Uploads (Ajax/iFrames/Popups)</li>
<li>Page that allows You to update your profile (Ajax Driven)</li>
<li>Additional Profile Parameters (not all are shown on the profile page yet, but the backend is in place)</li>
<li>Password resets (They log you out right not, but your password does get reset</li>
<li>I had to write tons of validation crap and found a couple security holes in my code I had to take care of.</li>
<li>Finally, I discovered what happens when you type 'DELETE FROM table WHERE $variable = :variable'. Yep, it deletes the entire table. Fun stuff.</li>
</ul>

<p>Upcoming tasks:</p>
<ul>
<li><strike>Blogging Software</strike><small>7/31/2013 Yes, it needs work.</small></li>
<li><strike>Contact Form</strike> <small>8/2/2013</small></li>
<li>User communication mechanism</li>
<li><Strike>Fix Password changes so they don't log you out.</strike><small>7/31/2013</small></li>
<li><strike>User Tracking software at all customer decision points.</strike><small>7/30/2013</small></li>
<li>Profile Images Explosions</li>
</ul>
<p>Thanks for stopping by and participating in my dream.</p>
<br />
<br />
</div>
<br />
<br />
<div class="image"><a href="#"><img src="images/homepage_08.jpg" alt="" width="47" height="41" /></a></div>
<div class="price"><span>7/24/13</span></div>
<div class="info">
<h3>Starting comments and Incorporating</h3>
<br />
<p>I decided today that I would like to keep people updated about what I am doing here.</p>
<br />
<p>To start off. I've been working my butt off on this site trying to get it live. Since I started, I've managed to find at least 15 hours each week to work on this site. Sometimes MUCH more. I had a couple false starts. I started with Joomla. It was too cumbersome for my needs so I switched to ModX. That was much better, but I'm a programmer and even the small administative overhead of ModX was too much. So I scrapped them both and started coding from scratch. Since them, I've been programming like a one-armed paper hanger with crabs (my girlfriend told me that one).</p>
<br />
<p>All the time I've been doing technical work, I've also been listening to and reading books about business. Wow! Talk about a learning process. I never imagined there were so many things I needed to learn. I'm learning to view business, management, and entrepreneurship in completely new ways. Between the business and finanicial books I've read, I literally started feeling sick to my stomach (really) because of how quickly my perceptions shifted. One of these days, when it's not midnight and I'm not trying to de-focus so I can sleep, I will list them. I did post a bunch to my personal facebook page if anybody is interested in looking at them.</p>
<br />
<p>Last but not least. I filed to incorporate as Zalaxy, Inc. today. I should hear back within 10 working days. The last time I started a corporation, I had no idea what I was doing. Today, I have a much better understanding of corporations, how to use them, and how to maintain them. Hopefully, my last false start will be my last false start</p>
<br />
<br />
</div>
</li>
</ul>
					</div>
				</div>
			</div>
			<div id="sidebar">
				<div id="box2">
                    <?php include "blog-menu.php";?>
                <br />
                <br />
                <br />
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
