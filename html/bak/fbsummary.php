<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("Item.php");
include_once("Reservation.php");

$mypagetype='nosb';

$title="Listings";
$color = "ngrey";
include "top.php";
include "accountmenu1.php";

$userid =  $user->getUserID();

// Okay. Bad me. Again
$db = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT resid, userid, otherid, role, fbscore, fbtext, ts FROM feedback where otherid=:userid order by ts desc";
$stmt = $db->prepare( $sql );
$stmt->bindValue(":userid", $userid, PDO::PARAM_STR );
$stmt->execute();
$fbleft = $stmt->fetchAll();

?>
<h2>Feedback Left By You</h2>
<?php
if(count($fbleft) === 0) :
?>
                    <div class="box-style9">
                        <div class="t"></div>
                        <div class="content">
                            <ul class="list-style7">
                                <li>
                                    <div class="image"><!--a href="#"><img src="/images/lists_04.jpg" alt="" width="67" height="58" /></a--></div>
                                    <!--div class="price"><span><a href="/postit.php"></a></span></div-->
                                    <div class="info">
                                        <h4 style='text-align:center;'>Nobody has left feedback for you yet.</h4>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="b"></div>
                    </div>
<?php
else:
?>
<div class="CSSTableGenerator" >
    <table >
        <tr>
            <!--td class="selectbox">
                &nbsp;
            </td-->
            <td>Reservation</td>
            <td>Feedback Date</td>
            <td>Feedback</td>
            <td>Comments</td>
        </tr>
<?php
foreach($fbleft as $fb) {
    try {
        $resobj = new Reservation($fb['resid']);
        $itemid = $resobj->getItemID();
        $itemobj = new Item($itemid);
        $timestamp = date('l, M-d-Y', strtotime($fb['ts']));

    } catch (Exception $e) {
        error_log("RESERVATIONS SELLER FAILURE: ".$e->getMessage());
        continue;
    }
?>

        <tr>
            <!--td class="selectbox">
                <input type="checkbox" name='itemid[]' value='<?=$itemid?>' />
            </td-->
            <td>
                <a href="/reservationstatus.php?resid=<?=$fb['resid'];?>"><?=$itemobj->getTitle();?></a>
            </td>
            <td class='timestamp'><?=$timestamp?></td>
            <td class='return'><?=strip_tags($fb['fbtext'])?>)</td>
            <td><div class='centerstar'><span class="stars"><?=$fb['fbscore']?></span></div></td>
        </tr>
<?php
}
?>
    </table>
</div>
<?php endif;?>
<br />
<br />
<br />
<?php
$sql = "SELECT resid, userid, otherid, role, fbscore, fbtext, ts FROM feedback where userid=:userid order by ts desc";
$stmt = $db->prepare( $sql );
$stmt->bindValue(":userid", $userid, PDO::PARAM_STR );
$stmt->execute();
$fbleft = $stmt->fetchAll();
?>
<h2>Feedback Left For You</h2>
<?php
if(count($fbleft) === 0) :
?>
                    <div class="box-style9">
                        <div class="t"></div>
                        <div class="content">
                            <ul class="list-style7">
                                <li>
                                    <div class="image"><!--a href="#"><img src="/images/lists_04.jpg" alt="" width="67" height="58" /></a--></div>
                                    <!--div class="price"><span><a href="/postit.php"></a></span></div-->
                                    <div class="info">
                                        <h4 style='text-align:center;'>You have not received any feedback.</h4>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="b"></div>
                    </div>
<?php
else:
?>
<div class="CSSTableGenerator" >
    <table >
        <tr>
            <!--td class="selectbox">
                &nbsp;
            </td-->
            <td>Reservation</td>
            <td>Feedback Date</td>
            <td>Feedback</td>
            <td>Comments</td>
        </tr>
<?php
foreach($fbleft as $fb) {
    try {
        $resobj = new Reservation($fb['resid']);
        $itemid = $resobj->getItemID();
        $itemobj = new Item($itemid);
        $timestamp = date('l, M-d-Y', strtotime($fb['ts']));

    } catch (Exception $e) {
        error_log("RESERVATIONS SELLER FAILURE: ".$e->getMessage());
        continue;
    }
?>

        <tr>
            <!--td class="selectbox">
                <input type="checkbox" name='itemid[]' value='<?=$itemid?>' />
            </td-->
            <td>
                <a href="/reservationstatus.php?resid=<?=$fb['resid'];?>"><?=$itemobj->getTitle();?></a>
            </td>
            <td class='timestamp'><?=$timestamp?></td>
            <td class='return'><?=strip_tags($fb['fbtext'])?>)</td>
            <td><div class='centerstar'><span class="stars"><?=$fb['fbscore']?></span></div></td>
        </tr>
<?php
}
?>
    </table>
</div>
<?php endif;?>

<script type="text/javascript">
<!--
$.fn.stars = function() {
    return $(this).each(function() {
        // Get the value
        var val = parseFloat($(this).html());
        // Make sure that the value is in 0 - 5 range, multiply to get width
        val = Math.round(val * 4) / 4;
        var size = Math.max(0, (Math.min(5, val))) * 16;
        // Create stars holder
        var $span = $('<span />').width(size);
        // Replace the numerical value with stars
        $(this).html($span);
    });
}

$(function() {
    $('span.stars').stars();
});
-->
</script>


<!-- End Content -->
<?php
include "accountmenu2.php";
include "bottom.php";
?>
