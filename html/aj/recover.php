<?php
header('Content-type: application/json');

include_once "user.php";
include_once "validate.php";
include_once "session.php";

$sess = new MySQLSessionHandler();
session_start();

//error_log(print_r($_POST,true));
$user = new Users();
$val = new Validate();

$password = isset($_POST['password']) ? $_POST['password'] : '';
$conpassword = isset($_POST['conpassword']) ? $_POST['conpassword'] : '';
$code = isset($_POST['code']) ? $_POST['code'] : '';
$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';

//error_log("$password && $conpassword && $code && $userid && $email");
// Use try block for immediate failues
// Don't want to alert on existence username and password
// in the same response since it can be used maliciously
try {
	$error = '';
    if($code) {

	    $error = $val->validatePassword($password, $conpassword);
        if($error) throw new Exception($error);

	    $val->validateCode($code, $email, $userid);

        $user->ChangePassword($password, $userid, $email);

        error_log("Password is updated?");
        $result = array("success" => true, "message" => "Updating Password");
        echo json_encode($result);
        exit;
    }
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
	$user->resetCode($_POST['email']);
	$user->sendPasswordResetEmail($_POST['email']);
} catch (Exception $e) {
	$result = array("success" => false,"error" => $e->getMessage());
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}

error_log($_POST['email']." requested a password reset");

$result = array("success" => true, "userID" => $user->userID);
echo json_encode($result);
?>
