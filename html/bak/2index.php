<?php
$title = 'Home/Apartment Rental Services - Zalaxy.com';
include_once("config.php"); //include the config
include_once("Item.php"); //include the config
include_once("cItems.php"); //include the config

$mypagetype='nosb';

$boxstyles = array(
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

$description = 'Peer to Peer rentals. Reservation fees are always less than $9.95. List your stuff for free.';
$metatitle = 'Peer to Peer rentals for less - Zalaxy.com';
include "top.php";

$locids = isset($_SESSION['closelocations']) ? $_SESSION['closelocations'] : null;
$itemobj = new cItems(0, 0, $locids, 'created desc', '');
$items = $itemobj->getItems(0,5);

?>
		<div id="content">
			<div id="box1" class="box-style1">
				<div class="t"></div>
				<div class="content">
                <img src="images/forrent.png" alt="" width="280" height="194" class="alignleft" />
<!--img src="images/reduced-tag.png" alt="Reduced Fees. $9.95 maximum." width="200" height="300" class="alignleft" /-->

<?php
$time= filemtime('.');

$hrtime = date('m-d-Y',$time);
?>

					<h1 style="text-align:center;font-size: 25px;">Rent Almost Anything!</h1>
<p class="more"><a href="/fees.php" class="default_popup button button-style1" style="margin-left:100px;"><span>Free to List</span></a></p>
                    <p class="small" style="text-align: center;"><a href='/fees.php' class="default_popup"  style="margin-left: 0px;">Still as low as 99&cent;. The Maximum Fee is $<?=MAXFEE?>.</a><br /><a href='/register.php' style="margin-left: 0px;">Register now to list items for free</a></p>

<?php
if(count($items) > 0) :?>
<br />
<h2>Most Recent Customer Listings</h2>
<?php
endif;
foreach($items as $item) {
    //error_log(print_r($item,true));
    $itemid = $item->getItemID();
    $itemtitle = $item->getTitle();
    $itemprice = $item->getPrice();
    $iteminterval = ucfirst($item->getInterval());
    $itemcity = $item->getCity();
    $itemstate = $item->getState();
    $itempriority = $item->getPriority();


//error_log(print_r($item->getItemID(),true));
    $imageobj = new Image();
    $imageid = $imageobj->getMainImage($itemid, 'listing');
    $thumbnail = $imageobj->getListingImageSrc($imageid,200);
    $image = $imageobj->getListingImageSrc($imageid,400);

    // Sorry. Need the last item to have a line.
    // Choosing not to use all the better ways.
    $borderbottom = '';
    if($item == end(array_values($items)))
        $borderbottom = "style='border-bottom-width: 1px'";

    switch($itempriority) {
        case 1:
            include "dfeatured.php";
            break;
        case 0:
            include "dregular.php";
            break;
        default:
            include "dfeatured.php";
            break;
    }

}
?>
                    <br />
                    <h2>Rent Your Stuff Here!</h2>

					<p class="text">We're live. Now all we need are <strong>Your items</strong>. Do you have something to rent? We bet you do! List anything from a spare computer to a boat. Don't let it gather dust when you can make $10, $20,$50, $100, or more by renting it.</p>
					<p class="text">What Do You Have In Your Garage?&trade;</p>
                    <br />
                    <h2>How it works?</h2>
				    <center><iframe width="420" height="315" src="//www.youtube.com/embed/YyoMmnFVJuo" frameborder="0" allowfullscreen></iframe></center>
                    <br />
                    <h2>How Can You Rent My Car, Boat, Equipment, etc?</h2>
                    <p class="text">To rent your things, all you need to do is <a href="/register.php">establish an account</a>, then Post Your Item. You can specify delivery or pickup, delivery fees, travel distance, and even how much notice you need. The ads will appear almost immediately in our rental listings.</p>
                    <p class="text">When the leasee finds your boat, car, or thing, they can simply reserve it from our site. They can elect to pay a <a href="fees.php?pw=500&amp;ph=300" class="default_popup">reservation fee</a> (which is deducted from your total rental/delivery fees) and pay the rest to you personally or they can elect to pay for the entire reservation through our website and we will send the fees (minus the <a href="fees.php?pw=500&amp;ph=300" class="default_popup">reservation fee</a>) to you as soon as the rental period is complete.</p>
				</div>
				<div class="b"></div>
			</div>
			<div class="box-style2">
				<div class="content">
					<h2><?=CAPSHORTDOM?> Updates</h2>
					<div class="links"> ( <a href="august2013.php">See Older Posts</a> )<!-- | <a href="postit.php">Post Your Own</a> )--> </div>
				</div>
			</div>
			<div class="box-style1">
				<div class="t"></div>
				<div class="content">
					<ul class="list-style1">
						<!--li class="featured first">
							<div class="image"><a href="#"><img src="images/homepage_06.jpg" alt="" width="190" height="166" /></a></div>
							<div class="price"><span>$125</span></div>
							<div class="info">
								<h3><a href="#">Trucks</a></h3>
							</div>
						</li>
						<li class="featured">
							<div class="image"><a href="#"><img src="images/homepage_07.jpg" alt="" width="190" height="166" /></a></div>
							<div class="price"><span>$25</span></div>
							<div class="info">
								<h3><a href="#">Cars</a></h3>
							</div>
						</li>
						<li class="featured">
							<div class="image"><a href="#"><img src="images/homepage_08.jpg" alt="" width="190" height="166" /></a></div>
							<div class="price"><span>$50</span></div>
							<div class="info">
								<h3><a href="#">children</a></h3>
							</div>
						</li-->
						<li>

<div class="image"><!--img src="images/reduced-tag.png" alt="Reduced Fees. $9.95 maximum." width="200" height="300" /--><img src="images/front-page-stars.png" alt="" width="93" height="23" class="alignleft" /></div>
<div class="price"><span>10/25/13</span></div>
<div class="info">
<h3>Feedback System</h3>
<br />
<p><strong>You can now leave feedback for each transaction.</strong></p>
<br />
<p>We finally have the feedback system working. There are still a few issues to work out (like actually showing the feedback scores on the rental pages), but those will be fixed rather quickly. The heavy lifting is done. In the meantime, just know that feedback is available after each transaction. You can see your own feedback summary in the Account pages.</p>
<br />
<br />
</div>


<div class="image"><img src="images/reduced-tag.png" alt="Reduced Fees. $9.95 maximum." width="200" height="300" /><!--img src="images/forrent.png" alt="" width="280" height="194" class="alignleft" /--></div>
<div class="price"><span>10/13/13</span></div>
<div class="info">
<h3>Reduced Fees</h3>
<br />
<p><strong>The maximum fee we charge for any reservation is now $<?=MAXFEE?></strong></p>
<br />
<p>That's right. We changed our fee structure. If somebody reserves your item, you will pay, at most, $<?=MAXFEE?> no matter how much the rent is. The smallest rental fee has not changed, and is still only 99&cent;. Earn $500 renting your boat for a weekend? Our fees are just $<?=MAXFEE?>. Now is a perfect time to find out how much money you can make on that thing gathering dust. Rent it now. As always, listing an item is completely free.</p>
<br />
<br />
</div>

<div class="image"><img src="images/rental-features.png" alt="Choose Fee, Rental Periods, Delivery, Scheduling is coming soon." width="200" height="200" /></div>
<div class="price"><span>10/9/13</span></div>
<div class="info">
<h3>Adding Features</h3>
<br />
<p><strong>Even though we're live, we haven't stopped working.</strong></p>
<br />
<p>We went live last month without much fanfare. That was expected. Since then, we've continued to make incremental improvements to the website. One of the changes we're working on is the ability to schedule rentals. We intend to provide a mechanism where you can specify the times you're willing to rent your item and the times it's unavailable. This should allow you to limit rental periods to times you're available. If you only allow 8 O'clock pickups, or you want to use your boat this weekend, we want to provide an easy scheduling mechanism that allows you to do just that.</p>
<br />
<br />
</div>

<div class="price"><span>10/9/13</span></div>
<div class="info">
<h3>Find Rentals in Your City</h3>
<br />
<p>One of the biggest improvements we've made to the site in the past few weeks has been the ability to search for items in specific geographics regions.</p>
<img src="images/choose-search.png" alt="Display Local Rentals Only" width="269" height="188" /><img src='/images/us-tx-sat.png'  width="269" height="186" alt='Select US Texas, San Antonio' style="float: right;" />
<p>Selecting the orange button, and choosing any place in the world, you will automatically limit all the results displayed on the website to those within 50 miles of your selected city. So if you're trying to rent a truck in Balitmore, you won't see listings in China. Select Baltimore and you will see rentals anywhere within your search radius. Furthermore, when you pull up the listing, it will tell you exactly how far away the item is from your selected location. This is far more versatile than the techiques implemented by sites like Craigslist and we hope you find it valuable.</p>
<br />
<br />
</div>


						</li>
					</ul>
					<!--div class="bottom-nav"> <a href="#" class="button button-style2"><span>Browse All Listings</span></a> <a href="#" class="button button-style3"><span>Post an Ad</span></a> </div-->
				</div>
				<div class="b"></div>
			</div>
		</div>
<script type="text/javascript">
<!--
$(function(){

        // Default usage
        $('.default_popup').popup();

        // Function for content
        $('.function_popup').popup({
                content         : function(){
                        return '<p>'+$(this.ele).attr('title')+'</p>';
                }
        });

        // jQuery for content
        $('.jquery_popup').popup({
                content         : $('#inline')
        });

        // HTML for content
        $('.html_popup').popup({
                content         : '<h1>This is some HTML</h1>',
                type            : 'html'
        });

});

-->
</script>


<?php
include "categories.php";
?>
	</div>
</div>
<?php
include 'footer.php';
?>
