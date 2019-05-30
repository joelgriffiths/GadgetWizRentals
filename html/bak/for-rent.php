<?php
include_once("Category.php");
include_once("cItems.php");
include_once("image.php");

$urlname = isset($_GET['urlname']) ? $_GET['urlname'] : '';

try {
    $selectedCat = new Category($urlname);
} catch (Exception $e) {
    header("HTTP/1.1 404 Not Found"); 
    include "404.php";
    exit;
}

include_once("config.php"); //include the config
include_once("user.php");

$mypagetype='nosb';
$itemsperpage = 5;

$thispage = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 0;

// Doing it this way so I can test with GET
$search = null;
$search = isset($_GET['search']) ? $_GET['search'] : $search;
$search = isset($_POST['search']) ? $_POST['search'] : $search;

$firstimg = $thispage*$itemsperpage;
$lastimg = $firstimg + $itemsperpage;

//$title = "Post an Ad";
$hn = $selectedCat->GetName();
$title = 'Rent '.$hn.' From Your Neighbor!';
$description = $hn.' Rentals. Peer to Peer rentals let you rent '.$hn.' from other people in your area.';

include "top.php";

//$itemobj = new cItems($selectedCat->getCatID(), 0, array('78251',  '92277'));
$locids = isset($_SESSION['closelocations']) ? $_SESSION['closelocations'] : null;
$itemobj = new cItems($selectedCat->getCatID(), 0, $locids, null, $search);


$totalcount = $itemobj->getItemCount();

// Adding a section to display subsection items if it's empty.
// Two built in bugs: No more than one leyer further is tested.
//                    Maximum number of items could be alot
//                    No way to page through the results
if($search === null && $totalcount === 0) {
    $items = array();
    $subcats = $selectedCat->getChildTree();
    foreach($subcats as $subcat) {
        $itemobj = new cItems($subcat, 0, null, null);
        $tempitems = $itemobj->getItems($firstimg,$lastimg);

        array_splice($items,count($items),0,$tempitems);

        if(count($items) >= $itemsperpage)
            break;
    }
    //error_log(print_r($items,true));
} else {
    //error_log(print_r($firstimg.'.'.$lastimg));
    $items = $itemobj->getItems($firstimg,$lastimg);
}


$imgstart = $totalcount == 0 ? 0 : $firstimg+1;

if($totalcount <= $lastimg) {
    $imgstop = $totalcount;
    $nextpage = null;
    $prevpage = $thispage < 1 ? null : $thispage-1;
} else {
    $imgstop = $lastimg;
    $nextpage = $thispage+1;
    $prevpage = $thispage < 1 ? null : $thispage-1;
}


include "list1.php";

$numresults = count($items);
$isare = $numresults == 1 ? "is" : "are";
$itemtext = $numresults == 1 ? "item" : "items";
if($numresults == 0 && $thispage == 0)
    include "nolistings.php";
elseif($numresults < $itemsperpage && $thispage == 0)
    include "postyourown.php";


foreach($items as $item) {
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

//include "featured.php";
//include "regular.php";
?>

<script type="text/javascript">
<!--
$(function(){
        // Default usage
        $('.default_popup').popup();
});
-->
</script>



<!-- End Content -->
<?php
include "list2.php";
include "bottom.php";

?>
