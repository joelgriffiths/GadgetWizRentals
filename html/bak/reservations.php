<?php

include_once("config.php"); //include the config
include_once("user.php");
include_once("cItems.php");
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

$sql = "SELECT reservationid FROM reservations where buyerid=:buyerid order by pickupdate desc";
$stmt = $db->prepare( $sql );
$stmt->bindValue(":buyerid", $userid, PDO::PARAM_STR );
$stmt->execute();
$purchases = $stmt->fetchAll(PDO::FETCH_COLUMN);

if(count($purchases) === 0) :
?>
                    <div class="box-style9">
                        <div class="t"></div>
                        <div class="content">
                            <ul class="list-style7">
                                <li>
                                    <div class="image"><!--a href="#"><img src="/images/lists_04.jpg" alt="" width="67" height="58" /></a--></div>
                                    <!--div class="price"><span><a href="/postit.php"></a></span></div-->
                                    <div class="info">
                                        <h4>You have not tried to Rent anything yet.</h4>
                                        <p style="text-align: center;">You haven't rented anything yet.</p>
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
<h2>Items Rented From Others</h2>
<form name='reservations' id='reservations' action="/reservations.php" method="POST">
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

/*
// Duplicated in checkout.php too (should be a function)
$datetime = new DateTime('now');
$minnotice = $itemobj->getMinNotice() + 1;
$oneday = new DateInterval('P1D');
$starttime = new DateInterval('PT'.$minnotice.'H');
$datetime->add($starttime);
$pickuphour = $datetime->format('H');

// After closing, auto-select next morning.
if($pickuphour >= 17) {
    $pickuphour = 9;
    $datetime->add($oneday);
}

$pickupday = $datetime->format('m/d/Y');
*/

foreach($purchases as $resid) {
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
        error_log("RESERVATIONS PURCHASE FAILURE: ".$e->getMessage());
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
$otherfb = new Feedback($resid, $resobj->getSellerID(), 'seller');
$myfb = new Feedback($resid, $resobj->getBuyerID(), 'buyer');
if($myfb->checkExists()) {
    echo "<p class='fbcomplete'>Feedback Received</p>";
} else {
    echo "<p class='fbwaiting'>No Feedback Received</p>";
}

if($otherfb->checkExists()) {
    echo "<p class='fbcomplete'>Feedback Left</p>";
} else {
    echo "<p class='fbwaiting'><a class='waiting' href='/feedback.php?resid=$resid&t=1'>Leave Feedback</a></p>";
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
</form>
<?php endif;?>

<!-- End Content -->
<?php
include "accountmenu2.php";
include "bottom.php";
?>
