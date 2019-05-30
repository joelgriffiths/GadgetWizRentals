<?php
//Start the session
//session_start();
include "session.php";
$sess = new Session();
$sess->start_session('_s', false);

//Send a image
create_image();
exit();

function create_image(){
    //generate a random string
    $md5_hash = md5(rand(0,999));
	 
    //make it 5 characters long
    $security_code = substr($md5_hash, 15, 5); 

    //Storing the security code in the session
    $_SESSION["security_code"] = $security_code;
    //Create the image
    $image = @imagecreatefromjpeg("images/static.jpg");  

    //Making the font color
    $black = ImageColorAllocate($image, 0, 0, 0);

    //Make the background black 
    //ImageFill($image, 0, 0, $bgImg); 

    //Set some variables for positioning and font-size, "5" is the largest I could get to work
	$vPos = 17;
	$hPos = 50;
	$fontSize = 5;
	
    ImageString($image, $fontSize, $hPos, $vPos, $security_code, $black); 
 
    //Tell the browser what kind of file this is 
    header("Content-Type: image/jpeg"); 

    //Output image as a jpeg 
    ImageJpeg($image);
   
    //Free up stuff
    ImageDestroy($image);
}
?>
