<?php

include_once "SimpleImage.php";
include_once "image.php";

$height = isset($_GET['height']) && is_numeric($_GET['height']) ? $_GET['height'] : null;
$width  = isset($_GET['width']) && is_numeric($_GET['width']) ? $_GET['width'] : null;

// Only one can be used, but checking both to simplify logic
$name = isset($_GET['name']) ?  preg_replace("/[^A-Za-z0-9.-]/",'',$_GET['name']) : null;
$imageid  = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;

// Check name first
if ( $name != null )
	$image = getcwd()."/images/".$name;

// ID overrides name
if ( $imageid != null ) {
	$myimage = new Image();
	$image = $myimage->getImagePath($imageid);
	//error_log("Loading image: " . $image);
}

if (!isset($image) || file_exists($image) === false) {
	//error_log($image.' does not exists');
	$image = getcwd().'/images/noimage.gif';
}

// Maximum size so somebody can't force me to send TB's of crap
$height = $height > 1000 ? 100 : $height;
$width = $width > 1000 ? 100 : $width;

$img = new SimpleImage();

$img->load($image);

if($height === null && $width !== null)
	$img->fit_to_width($width);
else if ($width === null && $height !== null)
	$img->fit_to_height($height);
else if ($width !== null and $height !== null)
	$img->resize($height, $width);

//$img->best_fit($height, $width);

//header($img->getHeader());
$img->output();
?>
