<?php
header('Content-type: application/json');

include_once "user.php";

//error_log(print_r($_POST,true));
$val = new Validate();

// Use try block for immediate failues
// Don't want to alert on existence username and password
// in the same response since it can be used maliciously
try {
	$error = '';
	$error .= $val->validateUsername($_POST['username']);
	$error .= $val->validateFirstName($_POST['first']);
	$error .= $val->validateLastName($_POST['last']);
	$error .= $val->validateEmail($_POST['email']);
	$error .= $val->validatePassword($_POST['password'],$_POST['conpassword']);
} catch ( PDOException $e ) {
	error_log($e->getMessage());
	$result = array("success" => false,"error" => "Internal Error");
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
} catch ( Exception $e ) {
	$result = array("success" => false,"error" => $e->getMessage());
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}


// Normal errors from validation go here
if ($error !== '') {
	$result = array("success" => false,"error" => $error);
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}

// Yippee, they validated. Send them and email
try {
	$user = new Users($_POST);
	$user->register();
	$user->sendRegEmail();
} catch (Exception $e) {
	$result = array("success" => false,"error" => $e->getMessage());
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}

$result = array("success" => true, "userID" => $user->userID);

echo json_encode($result);
?>
