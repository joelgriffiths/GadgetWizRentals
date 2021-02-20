<?php
header('Content-type: application/json');

include_once("config.php"); //include the config
include_once("CatSelector.php");
include_once("User.php");

$sess = new MySQLSessionHandler();
session_start();

$user = new User();
$lic = $user->login_check();

$cats = new CatSelector();

try {

    if(isset($_POST['selectedid']))
	    $options = $cats->getOptions($_POST['selectedid']);

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

$result = array("success" => true, "options" => $options);
echo json_encode($result);

?>
