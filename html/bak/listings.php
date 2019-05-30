<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("cItems.php");

$mypagetype='nosb';

$title="Listings";
$color = "ngrey";
include "top.php";
include "accountmenu1.php";

$userid =  $user->getUserID();
// Screw the ajax for now. Just get it done
// This is used to delete listings from the database.
// The userid prevent abuse. 
if(isset($_POST['itemid'])) {
    foreach($_POST['itemid'] as $iid) {
        $item = new Item($iid);
        $item->deleteFromDB($userid);
    }
}

$itemlist = new cItems('', $userid, null, 'title');
$aItems = $itemlist->getItems(0,0);

if(count($aItems) === 0) :
?>
                    <div class="box-style9">
                        <div class="t"></div>
                        <div class="content">
                            <ul class="list-style7">
                                <li>
                                    <div class="image"><!--a href="#"><img src="/images/lists_04.jpg" alt="" width="67" height="58" /></a--></div>
                                    <!--div class="price"><span><a href="/postit.php"></a></span></div-->
                                    <div class="info">
                                        <h4>Look around your house, you will be amazed by what you can rent.</h4>
                                        <p style="text-align: center;">You haven't posted anything yet.</p>
<p style="text-align: center;">If you have something to rent, you can <a href="/postit.php">List it here</a> and earn cash for it.</p>

                                        <p style="text-align: center;">There is no fee to list items.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="b"></div>
                    </div>
<?php
else:
?>

<script type='text/javascript'>
<!--
$(document).ready(function(){
    $('#deleteitems').click(function() {
        $('#listingsform').submit();
        return false;
    });
});

-->
</script>
<form name='listings' id='listingsform' action="/listings.php" method="post">
<div class="CSSTableGenerator" >
    <table >
        <tr>
            <td class="selectbox">
                &nbsp;
            </td>
            <td >
                Title
            </td>
            <td>
                System Alerts
            </td>
        </tr>
<?php
foreach($aItems as $itemobj) {
    $itemid = $itemobj->getItemID();
?>

        <tr>
            <td class="selectbox">
                <input type="checkbox" name='itemid[]' value='<?=$itemid?>' />
            </td>
            <td>
                <a href="/postit.php?itemid=<?=$itemid?>"><?=$itemobj->getTitle();?></a><a href="/rental.php?itemid=<?=$itemid?>"><small>(View Final Posting)</small></a>
            </td>
            <td>
                
            </td>
        </tr>
<?php
}
?>
    </table>
</div>
<div class="bottom-nav">
    <a href='#' id='deleteitems' class='button button-style3'><span>Delete Selected Ads</span></a>
</div>
</form>

<!-- End Content -->
<?php
endif;
include "accountmenu2.php";
include "bottom.php";
?>
