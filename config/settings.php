<?php
//set off all error for security purposes
//ini_set("log_errors", 1);
//ini_set("error_log", "/tmp/php-error.log");
error_reporting( E_ALL );

// SANDBOX Testing
define( "USESANDBOX", false);
if(USESANDBOX) {
    define( "PAYPALURL", "https://www.sandbox.paypal.com/cgi-bin/webscr");
    define( "BUSINESS", "paypal-facilitator@zalaxy.com");
    define( "SELLER_EMAIL", "paypal-facilitator@zalaxy.com");
    //define( "SELLER_EMAIL", "seller@paypalsandbox.com");
} else {
    define( "PAYPALURL", "https://www.paypal.com/cgi-bin/webscr");
    define( "BUSINESS", "M822DDMZTQXL4");
    define( "SELLER_EMAIL", "paypal@zalaxy.com");
}

define( "SUPPORT_EMAIL", "noreply@zalaxy.com");
define( "MAINSITEURL", "www.iltz.com");
define( "MAINSITE", "ILTZ.com");
define( "CAPMAINSITE", "ZALAXY.COM");
define( "LONGDOM", "iltz.com");
define( "SHORTDOM", "iltz");
define( "CAPSHORTDOM", "ILTZ");

// Percentage of Reservation Fee
define( "RESFEE", 9.95);

// Actual dollar amount
define( "MINFEE", 0.99);
define( "MAXFEE", 9.95);


// FOR PIWIK 2 = GadgetWiz,  1 = ILTZ
define( "SITEID", 1);

//define some contstant
define( "DB_HOSTNAME", "localhost" ); //this constant will be use as our connectionstring/dsn
define( "DB_DATABASE", "zalaxy" ); //this constant will be use as our connectionstring/dsn
define( "DB_DSN",      "mysql:host=localhost;dbname=zalaxy" ); //this constant will be use as our connectionstring/dsn

define( "DB_USERNAME", "zalaxyrw" ); //username of the database
define( "DB_PASSWORD", "mi11ionaire" ); //password of the database
define( "CLS_PATH",    "class" ); //the class path of our project

define( "SALT", "zO4Ru5z1yYkjaasy0pt6eUg7BBYdlEhPaNLuXaWu8lQU1ElzHv0Ri7EM6IRpx5W" ); //password of the database
define('SESSION_SALT',         '516n!S:;=_TTw])ii#x$-*kWAejEDF_e&A:iIw:N~y-]|,&yX]x=fpXf){-$m*6J');
define('SECURE_AUTH_KEY',  'q:9KMSWIm8lB+bF>ogj|-qENCkHh+44+<u1Mtfm!<M/aYvcM-IoSSHo{6@Ig32{y');
define('LOGGED_IN_KEY',    'ZY-tQsX;s}`nE^<[>Onz!zpo||,)oEIuy;7V27,Je<m<ozOdrWmEG{FqQ%$27h5b');
define('NONCE_KEY',        '{Y<FMkCX=nzBka/hdz!2YT!{jzN|;J2On@vuW%dIf67Yeo~Lcc|]6QYXz~On,Rud');
define('AUTH_SALT',        '8GRG67x+%(1c26C%z=kZzZ<TaK8BZU{O?|-Qf9#jrc7Ox##77ZsGNAK+BWUzHDjZ');
define('SECURE_AUTH_SALT', 'PJQ|/;8P)4oAL-{Z:]HC|} Mt9oMy3]8?>x@Do5+=l)~yhGw D*R3Mdt`u{q;U<s');
define('LOGGED_IN_SALT',   'p-rxb~z#RFR)-NJF* ?&.xE2^K9J9{qG1PBp*^3*i9gl+y-GT]0* vAYQ..})&NF');
define('NONCE_SALT',       ' *)ba8A|-nqcnmmyyJuy7t#zW(R]5}9|!l0V]oZ^@H9^M-4H7E}f33tmy4zw6EK@');

define( "MIN_NAME_LEN", 2 ); //the class path of our project
define( "MAX_NAME_LEN", 15 ); //the class path of our project
define( "MIN_USERNAME_LEN", 2 ); //the class path of our project
define( "MAX_USERNAME_LEN", 15 ); //the class path of our project

# Require the trailing slash
define( "TEMPIMGDESTDIR",  '/data/uploads/temp/');
define( "TMPIMGWEBDIR",    '/uploads/temp/');
define( "IMGDESTDIR",      '/data/uploads/');
define( "IMGWEBDIR",       '/uploads/');

?>
