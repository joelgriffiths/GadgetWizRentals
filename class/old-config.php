<?php
//set off all error for security purposes
//ini_set("log_errors", 1);
//ini_set("error_log", "/tmp/php-error.log");
error_reporting( E_ALL );


//define some contstant
define( "DB_HOSTNAME", "localhost" ); //this constant will be use as our connectionstring/dsn
define( "DB_DATABASE", "zalaxy" ); //this constant will be use as our connectionstring/dsn
define( "DB_DSN",      "mysql:host=localhost;dbname=zalaxy" ); //this constant will be use as our connectionstring/dsn

define( "DB_USERNAME", "zalrw" ); //username of the database
define( "DB_PASSWORD", "mi11ionaire" ); //password of the database
define( "CLS_PATH",    "class" ); //the class path of our project


define( "MIN_NAME_LEN", 2 ); //the class path of our project
define( "MAX_NAME_LEN", 15 ); //the class path of our project
define( "MIN_USERNAME_LEN", 2 ); //the class path of our project
define( "MAX_USERNAME_LEN", 15 ); //the class path of our project

include_once( "user.php" );

?>
