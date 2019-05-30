#!/usr/bin/php
<?php

include_once 'session.php';
include_once 'states.php';
include_once 'Item.php';
include_once 'CatSelector.php';
include_once 'cItems.php';

$items = new cItems(138, 178, array('78251',  '92277'));
$items = new cItems(0, 0, array('92277'));
print_r($items);
exit;

function testsession() {
	$session = new session();
	// Set to true if using https
	$session->start_session('_s', false);
 	
	$_SESSION['something'] = 'A value.';
	//echo $_SESSION['something'];
}

include_once 'user.php';

function testUsers() {
	$input = Array("userID"=>171);
	$user = new Users($input);
	
	print_r($user->getRecordsFromDB());
}

function Login($username, $password) {
	$user = new Users();
	$session = $user->userLogin($username, $password);
	print "\n$session\n";
}

function testStates() {
    $states = new States();
    print_r($states);
    print($states->getStateOptions('TX'));
}

#try {
#	testsession();
#	Login('joelg', 'Devry400');
#} catch (Exception $e) {
#	print_r($e);
#}

#testStates();
$cs = new CatSelector();
$cs->printOptions(0);
?>
