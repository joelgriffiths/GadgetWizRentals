<?php
include_once("config.php"); //include the config
$sess = new MySQLSessionHandler();
session_start();

include_once "Category.php";
include_once "CatSelector.php";
include_once "user.php";

$user = new Users();
$lic = $user->login_check();

if($user->getUserName() != 'joelg') {
    exit;
}
// Okay. Bad me. Again
$db = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT username FROM users where userid > 180";
$stmt = $db->prepare( $sql );
$stmt->bindValue(":userid", $userid, PDO::PARAM_STR );
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>
<html>
<body>

<?php

echo "<pre>";
//foreach ($users as $user) {
    print_r($users);
//}
echo "</pre>";

?>




</body>
</html>


