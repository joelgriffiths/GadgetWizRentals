<?php
header('Content-type: application/json');

include_once("config.php"); //include the config
include_once("image.php");
include_once("cImage.php");
include_once "cImgbox.php";

$sess = new Session();
$sess->start_session('_s', false);

$user = new Users();
$lic = $user->login_check();
$uid = $user->getUserID();

$action     = isset($_POST['action']) ? $_POST['action'] : '';
$imageid    = isset($_POST['imageid']) ? $_POST['imageid'] : '';
$itemid    = isset($_POST['itemid']) ? $_POST['itemid'] : '';
$imagetype  = isset($_POST['imagetype']) ? $_POST['imagetype'] : 'listing';
$claimeduid = isset($_SESSION['userID']) ? $_SESSION['userID'] : '';

error_log("AJAX/IMAGE CALLED: $action $imageid $imagetype $claimeduid $itemid");


// Check for hackers
if ($uid === $claimeduid) {
    $uid = $claimeduid;
} else {
    error_log("ABUSE: Hacker in aj/images.php");
    $result = array("success" => false,"error" => 'root# ');
    echo json_encode($result);
	exit;
}

$image = new Image($imageid);

$success = false;
$output = '';
try {

    if(($action == 'primary' || $action == 'delete') && $imageid && $uid)
	    $success = $image->makePrimary($imageid, $imagetype, $uid);

    if($action == 'delete' && $imageid && $uid)
	    $success = $image->deleteImage($imageid, $uid);

    if(($action == 'refresh' || $action == 'primary' || $action == 'delete') && $itemid != '' && $uid) {
        error_log("AJ: cImgbox($uid,$itemid, $imagetype)");
        $imgbox = new cImgbox($uid,$itemid,$imagetype);
        $output .= $imgbox->printImages();
        $success = true;
    }


} catch ( PDOException $e) {
	error_log($e->getMessage());
    $result = array("success" => false,"error" => "Database Error");
    echo json_encode($result);
	exit;
} catch ( Exception $e ) {
	error_log($e->getMessage());
    $result = array("success" => false,"error" => $error . $e->getMessage());
    echo json_encode($result);
	exit;
}

if($success === false)
    $result = array("success" => false,"message" => "Operation Failed");
else
    $result = array("success" => true, "message" => $output);

echo json_encode($result);

?>
