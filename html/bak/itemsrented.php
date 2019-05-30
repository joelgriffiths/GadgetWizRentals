<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("Item.php");
include_once("Reservation.php");
include_once("Feedback.php");

$mypagetype='nosb';

$title="Listings";
$color = "ngrey";
include "top.php";
include "accountmenu1.php";

$userid =  $user->getUserID();

// Okay. Bad me. Again
$db = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT reservationid FROM reservations where sellerid=:userid order by pickupdate desc";
$stmt = $db->prepare( $sql );
$stmt->bindValue(":userid", $userid, PDO::PARAM_STR );
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_COLUMN);

if(count($orders) === 0) :
?>
                    <div class="box-style9">
                        <div class="t"></div>
                        <div class="content">
                            <ul class="list-style7">
                                <li>
                                    <div class="image"><!--a href="#"><img src="/images/lists_04.jpg" alt="" width="67" height="58" /></a--></div>
                                    <!--div class="price"><span><a href="/postit.php"></a></span></div-->
                                    <div class="info">
                                        <h4>Nobody has rented anything from you yet.</h4>
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
<h2>Items Rented To Others</h2>
<div class="CSSTableGenerator" >
    <table >
        <tr>
            <!--td class="selectbox">
                &nbsp;
            </td-->
            <td >
                Title
            </td>
            <td >Pickup</td>
            <td >Return</td>
            <td>
                System Alerts
            </td>
        </tr>
<?php
foreach($orders as $resid) {
    try {
        $resobj = new Reservation($resid);
        $itemid = $resobj->getItemID();
        $itemobj = new Item($itemid);

        $timebeforepickup = $resobj->timeBeforeLastPickup();
        $timebeforereturn = $resobj->timeBeforeReturn();

        $rentalstatus = $resobj->getStatus();
        error_log($resid.":".$rentalstatus.":".$timebeforepickup);
        if($rentalstatus == 'pending' && $timebeforepickup < 0) {
            $resobj->deleteReservation();
            continue;
        }

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
                <a href="/reservationstatus.php?resid=<?=$resid?>"><?=$itemobj->getTitle();?></a>
            </td>
            <td class='pickup'><?=$resobj->getShortEnPickupTime()?> &nbsp; <?=$resobj->getShortEnPickupDate()?></td>
            <td class='return'><?=$resobj->getShortEnReturnTime()?> &nbsp; <?=$resobj->getShortEnReturnDate()?></td>
            <td>
<?php

if($rentalstatus === 'pending' || $rentalstatus === '') :?>
        <p class='important'>Item Not Yet Reserved:<br /><a href="/confirmation.php?resid=<?=$resid?>">Complete Reservation Now</a></p>
<? elseif( $rentalstatus === 'resfeepaid' && $timebeforereturn > 0) : ?>
        <p class='important'>Confirmed:<br /><a href='/reservationstatus.php?resid=<?=$resid?>'>Pickup/Delivery Details</a></p>
<? elseif( $rentalstatus === 'resfeepaid' && $timebeforereturn <= 0) : 
$myfb = new Feedback($resid, $resobj->getSellerID(), 'seller');
$otherfb = new Feedback($resid, $resobj->getBuyerID(), 'buyer');
if($myfb->checkExists()) {
    echo "<p class='fbcomplete'>Feedback Received</p>";
} else {
    echo "<p class='fbwaiting'>No Feedback Received</p>";
}

if($otherfb->checkExists()) {
    echo "<p class='fbcomplete'>Feedback Left</p>";
} else {
    echo "<p class='fbwaiting'><a class='waiting' href='/feedback.php?resid=$resid&t=0'>Leave Feedback</a></p>";
}

endif;
?>
            </td>
        </tr>
<?php
}
?>
    </table>
</div>
<?php endif;?>


<!-- End Content -->
<?php
include "accountmenu2.php";
include "bottom.php";
?>
