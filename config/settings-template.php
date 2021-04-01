<?php
//set off all error for security purposes
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
error_reporting( E_ALL );

// SANDBOX Testing
define( "USESANDBOX", true);
if(USESANDBOX) {
    define( "PAYPALURL", "https://www.sandbox.paypal.com/cgi-bin/webscr");
    define( "BUSINESS", "facilitator@yourdomain.com");
    define( "SELLER_EMAIL", "facilitator@yourdomain.com");
} else {
    define( "PAYPALURL", "https://www.paypal.com/cgi-bin/webscr");
    define( "BUSINESS", "PPCODE");
    define( "SELLER_EMAIL", "seller@yourdomain.com");
}

define( "SUPPORT_EMAIL", "noreply@yourdomain.com");
define( "MAINSITEURL", "www.yourdomain.com");
define( "MAINSITE", "yourdomain.com");
define( "CAPMAINSITE", "YOURDOMAIN.COM");
define( "LONGDOM", "yourdomain.com");
define( "SHORTDOM", "yourdomain");
define( "CAPSHORTDOM", "YOURDOMAIN");

// Percentage of Reservation Fee
define( "RESFEE", 9.95);

// Actual dollar amount
define( "MINFEE", 0.99);
define( "MAXFEE", 9.95);


// FOR PIWIK 2 = GadgetWiz,  1 = ILTZ
define( "SITEID", 1);

//define some contstant
define( "DB_HOSTNAME", "localhost" ); //this constant will be use as our connectionstring/dsn
define( "DB_DATABASE", "rentals" ); //this constant will be use as our connectionstring/dsn
define( "DB_DSN",      "mysql:host=localhost;dbname=rentals" ); //this constant will be use as our connectionstring/dsn

define( "DB_USERNAME", "rentalsrw" ); //username of the database
define( "DB_PASSWORD", "somethingspecial" ); //password of the database
define( "CLS_PATH",    "class" ); //the class path of our project

define( "SALT", "zO4Ru5z1Y5KJAASY0PT6E5G7bbyDL5HpAnLu5aWu8l5U1eLZhV0rI7EM6IRpX5w" ); //password of the database
define('SESSION_SALT',         '516n!S:;=_TTw])ii#x$-*kWAejEDF_e&A:iIw:N~y-]|,&yX]x=fpXf){-$m*6J');
define('SECURE_AUTH_KEY',  'q:4KMSWI48l4+b4>OGJ|-QencKhH+44+<u1Mtfm!<M/ayVCm-iOssHo44@iG32{y');
define('LOGGED_IN_KEY',    'ZY-TqSx;1}`Ne1<[>oN2!ZPO||,)OeiUY;7v57,jE4M<OZoDRwMeg{fQq%$27H5b');
define('NONCE_KEY',        '{y<fmKgx=NZgKA/HDZ!2yt!gjzN|;J2On@vuW%DiF67yEO~lcc|]gQYXz~ON,gUd');
define('AUTH_SALT',        '8grgb7X+b(1C26c%Z=KzZb<tAk8bzu{o?|-bF9#JRC7oX##77zSgnakbbwubhdJz');
define('SECURE_AUTH_SALT', 'pjq|/;8pt4OaL-{Z:]HC|}ttt9oMy3]8?>X@do5+=t)~yhtW d*r3mDT`U{q;U<s');
define('LOGGED_IN_SALT',   'p-rxb~Zbrfr)-njF* ?&.xb2^K9J9{qg1pbp*^b*i9gl+y-GT]0* vayq..}b&Nf');
define('NONCE_SALT',       ' *)ba8kk-NQCNMMYYkUy7t#zW(R]k}9|!l0v]Ozk@h9^m-4H7E}F33Tmy4zk6EK@');

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
