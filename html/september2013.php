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
<div class="image"><a href="images/<?=SHORTDOM?>-welcometo.png" class="default_popup"><img src="images/<?=SHORTDOM?>-welcometo.png" alt="" width="160" height="40" /></a></div>
<div class="price"><span>9/8/13</span></div>
<div class="info">
<h3>Finally Live!</h3>
<br />
<p><strong>Now we have to work twice as hard</strong></p>
<br />
<p>We went live yesterday without much fanfare. That was expected. Nobody even knows we exist and very few people even consider searching for peer-to-peer rentals. Those who do probably don't know they have something to rent. That's the challenge today. We need to let people know they can make money with their stuff. Some things I have in my garage include an OBD II tester, a wakebord, and an Oscilloscope. They will be listed as soon as I get a few minutes to breathe. What do you have? Remember, it doesn't cost a penny to list something on the website. You are only billed if somebody rents it from you.</p>
<br />
<p>Don't get me wrong. We may be live, but we're still working feverishly on this website. There is a TON of stuff to do. We need to add <strong>item search, proximity limits, category search, member communication, and product reservation calendars.</strong> Your experience will contunue to improve.</p>
<br />
<p>Now that we're live, we have another job. We need to continue building the site, of course. But we also need to get people here and then we have to get them to take advantage of the service. If you can spread the word, we would really appreciate it. List something then let your friends know they can list something too.</p>
<br />
<br />
</div>


<div class="image"><a href="images/golivedate.png" class="default_popup"><img src="images/golivedate.png" alt="" width="47" height="41" /></a></div>
<div class="price"><span>9/6/13</span></div>
<div class="info">
<h3>Almost There</h3>
<br />
<p><strong>Going Live Tomorrow?</strong></p>
<br />
<p>Tomorrow looks like the day. We will likely be going live with the site tomorrow. Come back this weekend to Rent your things. <strong>What do you have in your garage?</strong> Don't sell it on eBay, Rent It!</p>
<br />
<p>Listing the item is free.  We charge a commission on each rental, but there are no other fees to list your item. Give it a shot, what can you lose?</p>
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
