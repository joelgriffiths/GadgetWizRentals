<?php
include_once("config.php");
include_once("Category.php");
include_once("user.php");
include_once("Item.php");
include_once("image.php");
include_once("cGeocode.php");
include_once("userinfo.php");

try {
    if(!isset($_GET['itemid']))
        throw new Exception("Nothing to see here without an ID");

    $item = new Item($_GET['itemid']);
    //$geo = new Geocode($item->getZip(), 'US');
    $geo = new Geocode('','','','',$item->getLocId());
    $itemid = $item->getItemID();

    // Sorry, the name matters for the Category list included below
    $selectedCat = new Category($item->getCategory());

    //$parent = new Category($selectedCat->getParentID());
} catch (Exception $e) {
    header("HTTP/1.1 404 Not Found"); 
    include "404.php";
    exit;
}
$mypagetype='nosb';

$hn = $selectedCat->GetName();
$title = 'Rent '.$hn.' From Your Neighbor!';

include "top.php";

$mine = false;
try {
    $zip = "Not Available";
    $distance = "Please login to see distance.";

    // If we're logged in
    if($lic != false) {
        $userid = $user->getUserID();
        $userinfo = new Userinfo($user->getUserID());
        $locid = $userinfo->getLocId();
        $distance = $geo->getDistanceByLocId($locid).' Miles';
        $mine = $item->getUserID() === $userid ? true : false;
    }

    // Override with a session saved zip code
    if(isset($_SESSION['mylocid']) && isset($_SESSION['city'])) {
        $mylocid = $_SESSION['mylocid'];
        $distance = $geo->getDistanceByLocId($mylocid).' Miles from '.$_SESSION['city'];
    }

} catch (Exception $e) {
    error_log($e->getMessage());
}

?>

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

<!-- START LIST1-->
        <div id="content">
            <div class="box-style2">
                <div class="content">
                    <p>&#8249;&nbsp;&nbsp;<a href="for-rent/<?=$selectedCat->getURL()?>.html"><?=$selectedCat->getName()?></a></p>

<?php if($lic === true): ?>
    <?php if($mine):?>
                    <div class="links"><!--Posted 6 hours ago--> ( <a href="/postit.php?itemid=<?=$itemid?>">Edit This Listing</a> ) </div>
    <?php else:?>
                    <div class="links"><!--Posted 6 hours ago--> ( <a href="/postit.php">Post Your Own</a> ) </div>

    <?php endif;?>
<?php else: ?>
                    <div class="links"><!--Posted 6 hours ago--> ( <a href="/register.php">Register to Rent This</a> ) </div>
<?php endif ?>
                </div>
            </div>
            <!-- 7=small, 6=full -->
            <div class="box-style7">
                <div class="t"></div>
                <div class="content">
                    <h2 class="title" style="clear: both"><?=$item->getTitle()?></h2>
                    <div id="ads-content">

                        <div class="ads">
                            <div class="price" style="clear: both"><span>$<?=$item->getPrice()?> / <?=$item->getInterval()?></span></div>
                        </div >
                        <div>
                            <ul class="list-style5">
                                <!--li><strong>Item Condition:</strong> Brand New</li-->
                                <li><strong>Location:</strong> <?=$geo->getCity()?>, <?=$geo->getState()?></li>
                                <li><strong>Distance:</strong> <?=$distance?></li>
                            </ul>
                        </div>

<?php if ($item->getDeliveryOption() !== 'pickuponly') : ?>
                        <br />
                        <div class="ads">
                            <div class="price" style="clear: both"><span>Delivery: $<?=$item->getOneTimeFee()?></span></div>
                        </div >
                        <div>
                            <ul class="list-style5">
                                <li><strong><?=$item->getEnDeliveryOption()?></strong> <a href="/docs/delivery-options.html" class="default_popup"><img src="/images/q.png" alt="help" /></a></li>
                                <li><strong>Maximum Delivery Range:</strong> <?=$item->getRadius()?> Miles</li>
                            </ul>
                        </div>
                        <br />
<?php endif; ?>

                    </div>
                    <div>
                    <?=$item->getDescription()?>
                    </div>
                    <div class="box-style8">
                        <div class="t"></div>
                        <div class="content">
                            <ul class="list-style6">
<?php
$cimages = new Image();
$aimages = $cimages->getAllDBImages($itemid,'listing');
foreach($aimages as $itemimage) {
    echo '<li><a href="img-box.php?img='.$itemimage.'" class="default_popup"><img width="160" src="'.$cimages->getListingImageSrc($itemimage, 160).'" alt="Photograph" /></a></li>'."\n";
}
if(count($aimages) == 0) echo "<li>&nbsp</li>";
?>
                            </ul>
                        </div>
                        <div class="b">&nbsp;</div>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="bottom-nav"><a href="checkout.php?itemid=<?=$itemid?>" class="button button-style5"><span>Rent This Thing!</span></a> <!--a href="#" class="button button-style6"><span>Bookmark</span></a--> </div>
                </div>
                <div class="b"></div>
            </div>
        </div>
<?php
$sellerid = $item->getUserID();
$starsobj = new Users();
$starsobj->loadFromDB($sellerid);
error_log(print_r($sellerid,true));
error_log(print_r($starsobj,true));

$no_popup = 1;
include "user-summary.php";
?>

<!-- End Content -->
<?php
include "bottom.php";

?>
