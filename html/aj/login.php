<?php
header('Content-type: application/json');

include_once "user.php";
//include_once "validate.php";
include_once "session.php";

//$sess = new Session();
$sess = new MySQLSessionHandler();
//$sess->start_session('_s', false);
session_start();

//error_log(print_r($_POST,true));
$user = new Users($_POST);
//$val = new Validate($_POST);

// Use try block for immediate failues
// Don't want to alert on existence username and password
// in the same response since it can be used maliciously
try {
	$error = '';
	//$error = $val->validateUsername($_POST['username']);
} catch ( Exception $e ) {
	//$result = array("success" => false,"error" => $e->getMessage());
	$result = array("success" => false,"error" => $e->getMessage());
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}


if ($error !== '') {
	$result = array("success" => false,"error" => $error);
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}

try {
	$user->userLogin($_POST['username'], $_POST['password']);
} catch (Exception $e) {
	$result = array("success" => false,"error" => $e->getMessage());
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}

error_log("aj/login.php ".$_SESSION['username']." is LOGGED IN");

$result = array("success" => true, "userID" => $user->userID);

echo json_encode($result);
?>
