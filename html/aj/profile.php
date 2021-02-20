<?php
header('Content-type: application/json');

include_once("config.php"); //include the config
include_once("user.php");
include_once("userinfo.php");

$sess = new MySQLSessionHandler();
session_start();

$user = new Users();
$lic = $user->login_check();
$userid = $user->getUserID();

error_log(print_r($_POST,true));
$val = new Validate();

// Use try block for immediate failues
// Don't want to alert on existence username and password
// in the same response since it can be used maliciously
try {
    if($lic !== true)
        throw new Exception("Sorry, you must log in to change settings");

    // Bit if a hack until I figure out how this is supposed to work
    if($userid == 99) {
        throw new Exception("Sorry, you must log in to change settings");
    }

	$error = '';
	//$error .= $val->validateUsername($_POST['username']);

    if(isset($_POST['first']))
	    $error .= $val->validateFirstName($_POST['first']);

    if(isset($_POST['last']))
        $error .= $val->validateLastName($_POST['last']);

    if(isset($_POST['email']))
	    $error .= $val->validateEmail($_POST['email'], $user->getUserID());


	// Only check the password if new info provided
    #if( (isset($_POST['password']) || isset($_POST['conpassword']) || isset($_POST['curpassword'])) && (!isset($_POST['password']) || !isset($_POST['conpassword']) || !isset($_POST['curpassword'])) ) {
    #    $error .= "All three password fields are required to change your password.";
    #} else {
	    if(isset($_POST['password']) || isset($_POST['conpassword'])) {
		    if($user->userLogin($user->getUsername(), $_POST['curpassword'])) {
			    $error .= $val->validatePassword($_POST['password'],$_POST['conpassword']);
		    } else {
			    $error .= "I'm sorry, your existing password was not correct.";
		    }
        }
	 #}


	// now the optional for variables

    ###### Yeah. Go ahead and kick my ass for this
    $ui = array();
    try {
	    $ui['pphone'] = !empty($_POST['pphone']) ? $val->validatePhone($_POST['pphone']) : '';
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }
    try {
	    $ui['aphone'] = !empty($_POST['aphone']) ? $val->validatePhone($_POST['aphone'], 'Alternative') : '';
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }
    try {
        $ui['address1'] = !empty($_POST['address1']) ? $val->validateAddress($_POST['address1']) : '';
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }
    try {
        $ui['address2'] = !empty($_POST['address2']) ? $val->validateAddress($_POST['address2']) : '';
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }
    try {
        $ui['city'] = !empty($_POST['city']) ? $val->validateCity($_POST['city']) : '';
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }
    try {
        $ui['state'] = !empty($_POST['state']) ? $val->validateState($_POST['state']) : '';
    } catch (Exception $e) {
        $error .= 'XXX' . $e->getMessage();
    }
    try {
        $ui['zip'] = !empty($_POST['zip']) ? $val->validateZip($_POST['zip']) : '';
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }
    try {
        $ui['zip4'] = !empty($_POST['zip4']) ? $val->validateZip4($_POST['zip4']) : '';
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }
    try {
        $ui['birthday'] = !empty($_POST['birthday']) ? $val->validateBirthday($_POST['birthday']) : '';
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }



} catch ( Exception $e ) {
	$result = array("success" => false,"error" => $error . $e->getMessage());
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
} catch ( PDOException $e) {
	error_log($e->getMessage());
	$error .= "Database Error";
	$result = array("success" => false,"error" => $error);
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
	$user->UpdateRecords($_POST);
	$userinfo = new Userinfo($user->getUserID());
	$userinfo->UpdateRecords($ui);
	//$user->register();
	//$user->sendRegEmail();
} catch (Exception $e) {
	$result = array("success" => false,"error" => $e->getMessage());
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}

$result = array("success" => true, "userID" => $user->userID);

echo json_encode($result);
?>
