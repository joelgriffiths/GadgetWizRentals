<?php
include "settings.php";
ini_set( 'default_charset', 'UTF-8' );
iconv_set_encoding("internal_encoding", "UTF-8");
iconv_set_encoding("input_encoding", "UTF-8");
iconv_set_encoding("output_encoding", "UTF-8");

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');

// Probably faster inside a database, but the numer of compares is small now
$donotauth = array(
"/",
"404.php",
".html",
"ad.php",
"july2013.php",
"august2013.php",
"september2013.php",
"activation.php",
"blank.php",
"catalogue.php",
"credits.php",
"category.php",
"checkout.php",
"credits.php",
"contact.php",
"contactus.php",
"forgot.php",
"for-rent.php",
"index.php",
"index2.php",
"login.php",
"logout.php",
"postit.php",
"rental.php",
"resetpassword.php",
"register.php",
"search.php",
"users.php",
"whatwedo.php",
"welcome.php"
);

$titles = array(
    "Zalaxy - Rent your Thing",
    "Zalaxy - Rent From Your Neighbor!",
    "Zalaxy - Rent To Your Neighbor!",
    "Zalaxy - Peer to Peer Rentals!",
    "Rent From Your Neighbor! Rent To Your Neighbor!"
);

if(!isset($title)) {
    //$title = $titles[rand(0,4)];
    $title = "Rent From Your Neighbor! Rent To Your Neighbor! " . $_SERVER['PHP_SELF'];
    $title = "Rent From Your Neighbor! Rent To Your Neighbor! " . basename($_SERVER['PHP_SELF']);
}

if(!isset($description)) {
    //$title = $titles[rand(0,4)];
    $description = "Make money renting almost anything to your neighbors. Rental fees for suppliers are always less than $9.95. List your item for fee.";
}

include_once "user.php";
?>
