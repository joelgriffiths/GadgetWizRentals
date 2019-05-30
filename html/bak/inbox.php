<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("cItems.php");

$sess = new Session();
$sess->start_session('_s', false);

$mypagetype='nosb';

$title="Listings";
$color = "green";
include "top.php";
include "accountmenu1.php";

$userid =  $user->getUserID();
// Screw the ajax for now. Just get it done
/* Great example how copying the wrong file can be dangerous.
if(isset($_POST['itemid'])) {
    foreach($_POST['itemid'] as $iid) {
        $item = new Item($iid);
        $item->deleteFromDB($userid);
    }
}
*/

$itemlist = new cItems('', $userid, null, 'title');
$aItems = $itemlist->getItems(0,0);

if(true) :
?>
                    <div class="box-style9">
                        <div class="t"></div>
                        <div class="content">
                            <ul class="list-style7">
                                <li>
                                    <div class="image"><!--a href="#"><img src="/images/lists_04.jpg" alt="" width="67" height="58" /></a--></div>
                                    <!--div class="price"><span><a href="/postit.php"></a></span></div-->
                                    <div class="info">
                                        <p style="text-align: center;">This page is incomplete right now.</p>
                                        <h4>Look around your house, you will be amazed by what you can rent.</h4>
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
<form name='listings' id='listingsform' action="/listings.php" method="POST">
<div class="CSSTableGenerator" >
    <table >
        <tr>
            <td class="selectbox">
                X
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
                <a href="/postit.php?itemid=<?=$itemid?>"><?=$itemobj->getTitle();?></a>
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
    <a href='#' id='deleteitems' class='button button-style3'><span>Delete Selected</span></a>
</div>
</form>


<!-- End Content -->
<?php
endif;
include "accountmenu2.php";
include "bottom.php";
?>
