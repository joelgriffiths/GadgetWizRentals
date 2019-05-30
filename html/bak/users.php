<?php
$mypagetype = 'leftsb';
include_once("top.php");
include_once("userinfo.php");
include_once("cItems.php");

$refpage = isset($_SESSION['refpage']) ? $_SESSION['refpage'] : null;
$starsobj = new User('',$_GET['un']);
$uiobj = new Userinfo($starsobj->getUserID());
?>
<!-- Content Area with -->
<div class="box-leftsb">
    <div class="t"></div>
    <div id="content">
            <div class="content">

            <?php if($refpage) :?>
            <a href="<?=$refpage?>">&lt;&lt; Back</a><br /><br />
            <?php endif; ?>
            <h2>Member Information</h2>
            <!--a href="#" class="html_popup">Link</a-->
            <div id="stylized" class="profileform">
                <!--?php include("profilemenu.php");?-->
               
                <div id="ack2"></div>

                <?=$starsobj->getUsername()?><br />
                <?=$uiobj->getCity();?>, <?=$uiobj->getState()?> <?=$uiobj->getCountry();?><br />
                Member Since <?=date('M-d-Y', strtotime($starsobj->getCreated()))?>

                <!--div class="left">
                    <label>Username: <?=$starsobj->getUsername()?><span class="small"></span></label>
		       </div>
                
                <div class="right">
                    <label>Location: <?=$uiobj->getCity();?>, <?=$uiobj->getCountry();?><span class="small"></span></label><br />
               </div-->


                <!--p>Your email address is ALWAYS kept private</p-->
                <!--div class="full">
                    <label>Email<span class="small"></span></label>
                    <input type="text" name="email" id="email" value="<?=$user->getEmail();?>" />
                </div-->

               <!--div style="text-align: center;"><input type="submit" class="center" value="Update Profile" /></div-->
            </div> <!-- id="stylized" -->

<?php
$itemlist = new cItems('', $starsobj->getUserID(), null, 'title');
$aItems = $itemlist->getItems(0,0);

if(count($aItems) > 0) :?>
<br />
<h2>Current Rental Listings</h2>
<?php
foreach($aItems as $item) {
    //error_log(print_r($item,true));
    $itemid = $item->getItemID();
    $itemtitle = $item->getTitle();
    $itemprice = $item->getPrice();
    $iteminterval = ucfirst($item->getInterval());
    $itemcity = $item->getCity();
    $itemstate = $item->getState();
    $itempriority = $item->getPriority();


error_log(print_r($item->getItemID(),true));
    $imageobj = new Image();
    $imageid = $imageobj->getMainImage($itemid, 'listing');
    $thumbnail = $imageobj->getListingImageSrc($imageid,200);
    $image = $imageobj->getListingImageSrc($imageid,400);

    // Sorry. Need the last item to have a line.
    // Choosing not to use all the better ways.
    $borderbottom = '';
    if($item == end(array_values($aItems)))
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
else :?>
<br />
<h2>No Active Listings</h2>
<?php endif; ?>
        </div> <!-- class="content"-->
    </div> <!-- id="content"-->
    <?php include "user-summary.php"?>
    <div class="clearfix">&nbsp;</div>
    <div class="b"></div>

</div> <!-- class="box-leftsb"-->
<script type="text/javascript">
<!--
$(function(){
        $('.default_popup').popup();
});
-->
</script>
<?php
include "bottom.php";
?>
