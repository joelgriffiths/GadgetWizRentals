<?php
header('Content-type: application/json');

include_once("config.php"); //include the config
include_once("user.php");
include_once("Feedback.php");
include_once("Reservation.php");

$sess = new MySQLSessionHandler();
session_start();

$user = new Users();
$lic = $user->login_check();
$userid = $user->getUserID();

$resid = $_POST['resid'] && is_numeric($_POST['resid']) ? $_POST['resid'] : null;
$fbscore = $_POST['fbscore'] && is_numeric($_POST['fbscore']) ? $_POST['fbscore'] : null;
$fbtext = $_POST['fbtext'] && $_POST['fbtext'] != 'Feedback' ? strip_tags($_POST['fbtext']) : null;
$fbuser = $_POST['fbuser'] ? $_POST['fbuser'] : null;
// Will calculate this here $fbtype = $_POST['t'] ? $_POST['t'] : null;

error_log(print_r($_POST,true));

try {
    if($resid == null) {
        error_log("User $userid tried to leave feedback for invalid reservation ID $resid");
        throw new Exception("Invalid reservation");
    }

    if($userid != 169 and $fbuser == $userid) {
        error_log("$userid tried to leave feedback from himself");
        throw new Exception("Sorry, you can't leave feeback for yourself. Wouldn't that be a nice feature!");
    }

    if($fbscore == null) {
        throw new Exception("Please rate the member by selecting a star-score.");
    }
        
    if($fbtext == null) {
        throw new Exception("Feedback comments are required.");
    }

    if($lic !== true)
        throw new Exception("Sorry, you must log in to leave feedback");

    // Bit if a hack until I figure out how this is supposed to work
    if($userid == 99) {
        throw new Exception("Sorry, you must log in to leave feedback");
    }

	$error = '';

    $resobj = new Reservation($resid);
    if($fbuser == $resobj->getSellerID() && $userid == $resobj->getBuyerID()) {
        $fbtype = 'seller';
    } elseif($fbuser == $resobj->getBuyerID() && $userid == $resobj->getSellerID()) {
        $fbtype = 'buyer';
    } else {
        throw new Exception("Reservation/User Mismatch");
    }

    $fbobj = new Feedback($resid, $fbuser, $fbtype);
    $exists = $fbobj->checkExists();

    /* Allow updates for now
    if($exists)
        throw new Exception("This Feedback has already been provided");
    */

    if(strlen($fbtext) < 10)
        throw new Exception("Feedback is too short");

    if($fbscore < 1 | $fbscore > 5)
        throw new Exception("Please select a score from 1-5 Stars");


    $fbobj->setFeedback($fbtext);
    $fbobj->setFeedbackScore($fbscore);
    $fbobj->setOtherID($userid);

    $fbobj->save2DB();

} catch ( PDOException $e) {
	error_log($e->getMessage());
	$error .= "Database Error";
	$result = array("success" => false,"error" => "DB Error");
	echo json_encode($result);
	exit;
} catch ( Exception $e ) {
	$result = array("success" => false,"error" => $e->getMessage());
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}

$result = array("success" => true, "resid" => $resid);

echo json_encode($result);
?>
