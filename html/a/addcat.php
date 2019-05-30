<?php
include_once("config.php"); //include the config
$sess = new Session();
$sess->start_session('_s', false);

include_once "Category.php";
include_once "CatSelector.php";
include_once "User.php";

$user = new User();
$lic = $user->login_check();

if($user->getUserName() != 'joelg') {
    exit;
}

if($_GET['catid'] && is_numeric($_GET['catid']))
    $catid = $_GET['catid'];
else
    exit;
?>

<html>
<head></head>
<link rel="stylesheet" type="text/css" href="/css/popup.css" />
<body>
<form method='POST'>
Category Human Name <input name='humanname' type='text' /><br />
Category URL Name <input name='urlname' type='text' /><br />
Category ID <input name='catid' type='text' value='<?=$catid?>' /><br />
<input type='submit' value='Add Category to DB'>
</form>

<!--h1><?=$user->getUserName()?></h1-->



</body>
</html>


