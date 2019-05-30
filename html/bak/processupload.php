<noscript>
<div align="center"><a href="index.php">Sorry, JavaScript is required for uploads</a></div><!-- If javascript is disabled -->
</noscript>
<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

include_once("config.php"); //include the config
include_once("user.php");
include_once("image.php");

$sess = new Session();
$sess->start_session('_s', false);

$user = new Users();
$lic = $user->login_check();

$image = new Image();

// Clear existing errors
$_SESSION['ack2'] = '';

$position = isset($_POST['pos']) && is_numeric($_POST['pos']) ? $_POST['pos'] : 0;
$itemid = isset($_POST['itemid']) && is_numeric($_POST['itemid']) ? $_POST['itemid'] : 0;
error_log("PROCESSUPLOAD: UID: ".$user->GetUserID().", ITEMID=$itemid, POS: $position");

if(isset($_POST))
{
    $BigImageMaxSize        = 500; //Image Maximum height or width

    if($_SESSION['cpage'] == 'postit.php' && $itemid == 0) {
        $DestinationDirectory   = TEMPIMGDESTDIR;
        $WebDirectory           = TMPIMGWEBDIR;
    } else {
        $DestinationDirectory   = IMGDESTDIR;
        $WebDirectory           = IMGWEBDIR;
    }
    $Quality                = 90;

    // check $_FILES['ImageFile'] array is not empty
    // "is_uploaded_file" Tells whether the file was uploaded via HTTP POST
    if(!isset($_FILES['ImageFile']) || !is_uploaded_file($_FILES['ImageFile']['tmp_name']))
    {
        error_log('UPLOAD FAILED: '.$_FILES['ImageFile']['error']);
        if($_FILES['ImageFile']['error'] == UPLOAD_ERR_INI_SIZE) {
            $_SESSION['ack2'] = 'File is too large';
        } else {
            $_SESSION['ack2'] = 'Image upload failed: '.$_FILES['ImageFile']['error'];
        }
        error_log($_SESSION['ack2']);

        die('Something went wrong with Upload!'); // output error when above checks fail.
    }

    // Random number for both file, will be added after image name
    $RandomNumber   = rand(0, 9999999999);

    // Elements (values) of $_FILES['ImageFile'] array
    //let's access these values by using their index position
    $ImageName      = str_replace(' ','-',strtolower($_FILES['ImageFile']['name']));
    $ImageSize      = $_FILES['ImageFile']['size']; // Obtain original image size
    $TempSrc        = $_FILES['ImageFile']['tmp_name']; // Tmp name of image file stored in PHP tmp folder
    $ImageType      = $_FILES['ImageFile']['type']; //Obtain file type, returns "image/png", image/jpeg, text/plain etc.

    //Let's use $ImageType variable to check wheather uploaded file is supported.
    //We use PHP SWITCH statement to check valid image format, PHP SWITCH is similar to IF/ELSE statements
    //suitable if we want to compare the a variable with many different values
    switch(strtolower($ImageType))
    {
        case 'image/png':
            $CreatedImage =  imagecreatefrompng($_FILES['ImageFile']['tmp_name']);
            break;
        case 'image/gif':
            $CreatedImage =  imagecreatefromgif($_FILES['ImageFile']['tmp_name']);
            break;
        case 'image/jpeg':
        case 'image/pjpg':
            $CreatedImage = imagecreatefromjpeg($_FILES['ImageFile']['tmp_name']);
            break;
        default:
            $_SESSION['ack2'] = 'Unsupport File. Please use PNG, GIF, or JPB Files';
            die('Unsupported File!'); //output error and exit
    }

    //Let's get first two values from image, width and height.
    list($CurWidth,$CurHeight)=getimagesize($TempSrc);

    //Get file extension from Image name, this will be re-added after random name
    $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
    $ImageExt = str_replace('.','',$ImageExt);

    //remove extension from filename
    $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);

    //Construct a new image name (with random number added) for our new image.
    // Sorry about the depth thing. Gotta go to work.
    // Profiles and images with itemid's get permananent locations
    if($_SESSION['cpage'] == 'profile.php' || $itemid) {
        list($NewImageName, $DestinationDirectory) = $image->getRandomFilename($DestinationDirectory,$ImageExt, 3);
    } else {
        list($NewImageName, $DestinationDirectory) = $image->getRandomFilename($DestinationDirectory,$ImageExt, 0);
    }

    //set the Destination Image
    $DestRandImageName          = $DestinationDirectory.$NewImageName; //Name for Big Image
    error_log("DestRandImageName: $DestRandImageName");

    //Resize image to our Specified Size by calling resizeImage function.
    if(resizeImage($CurWidth,$CurHeight,$BigImageMaxSize,$DestRandImageName,$CreatedImage,$Quality,$ImageType))
    {
        //Get New Image Size
        list($ResizedWidth,$ResizedHeight)=getimagesize($DestRandImageName);

        // This is left over stuff that can probably be removed.
        echo '<table width="100%" border="0" cellpadding="4" cellspacing="0">';
        echo '<tr>';
        //echo '<td align="center"><img src="'.$WebDirectory.'/'.$ThumbPrefix.$NewImageName.'" alt="Thumbnail" height="'.$ThumbSquareSize.'" width="'.$ThumbSquareSize.'"></td>';
        echo '</tr><tr>';
        echo '<td align="center"><img src="'.$WebDirectory.'/'.$NewImageName.'" alt="Resized Image" height="'.$ResizedHeight.'" width="'.$ResizedWidth.'"></td>';
        echo '</tr>';
        echo '</table>';

	    echo $user->GetUserID();
        error_log($_SESSION['cpage'].':'.$NewImageName);
        if($_SESSION['cpage'] == 'profile.php') {
	        $imageid = $image->putDBImage($NewImageName, $DestinationDirectory, $user->GetUserID(), $user->GetUserID(), $position, 'profile');
            if($imageid) {
                $image->deleteOldProfileImages($user->GetUserID(), $imageid);
            }
        } elseif($_SESSION['cpage'] == 'postit.php' && $itemid) {
	        $imageid = $image->putDBImage($NewImageName, $DestinationDirectory, $itemid, $user->GetUserID(), $position, 'listing');
        } else {
            if (!isset($_SESSION['tmpimageid'])) {
                $_SESSION['tmpimageid'] = array();
            }
	        $id = $image->putDBImage($NewImageName, $DestinationDirectory, 0, $user->GetUserID(), $position, 'listing');
            array_push($_SESSION['tmpimageid'],$id);
         }

    }else{
        $_SESSION['ack2'] = 'Could not resize your image';
        die('Resize Error'); //output error
    }

}


// This function will proportionally resize image
function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType)
{
    //Check Image size is not 0
    if($CurWidth <= 0 || $CurHeight <= 0)
    {
        return false;
    }

    //Construct a proportional size of new image
    $ImageScale         = min($MaxSize/$CurWidth, $MaxSize/$CurHeight);
    $NewWidth           = ceil($ImageScale*$CurWidth);
    $NewHeight          = ceil($ImageScale*$CurHeight);

    if($CurWidth < $NewWidth || $CurHeight < $NewHeight)
    {
        $NewWidth = $CurWidth;
        $NewHeight = $CurHeight;
    }
    $NewCanves  = imagecreatetruecolor($NewWidth, $NewHeight);
    // Resize Image
    if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
    {
        switch(strtolower($ImageType))
        {
            case 'image/png':
                imagepng($NewCanves,$DestFolder);
                break;
            case 'image/gif':
                imagegif($NewCanves,$DestFolder);
                break;
            case 'image/jpeg':
            case 'image/pjpeg':
                imagejpeg($NewCanves,$DestFolder,$Quality);
                break;
            default:
                return false;
        }
    //Destroy image, frees up memory
    if(is_resource($NewCanves)) {imagedestroy($NewCanves);}
    return true;
    }

}

//This function corps image to create exact square images, no matter what its original size!
function xxxcropImage($CurWidth,$CurHeight,$iSize,$DestFolder,$SrcImage,$Quality,$ImageType)
{
    //Check Image size is not 0
    if($CurWidth <= 0 || $CurHeight <= 0)
    {
        return false;
    }

    //abeautifulsite.net has excellent article about "Cropping an Image to Make Square"
    //http://www.abeautifulsite.net/blog/2009/08/cropping-an-image-to-make-square-thumbnails-in-php/
    if($CurWidth>$CurHeight)
    {
        $y_offset = 0;
        $x_offset = ($CurWidth - $CurHeight) / 2;
        $square_size    = $CurWidth - ($x_offset * 2);
    }else{
        $x_offset = 0;
        $y_offset = ($CurHeight - $CurWidth) / 2;
        $square_size = $CurHeight - ($y_offset * 2);
    }

    $NewCanves  = imagecreatetruecolor($iSize, $iSize);
    if(imagecopyresampled($NewCanves, $SrcImage,0, 0, $x_offset, $y_offset, $iSize, $iSize, $square_size, $square_size))
    {
        switch(strtolower($ImageType))
        {
            case 'image/png':
                imagepng($NewCanves,$DestFolder);
                break;
            case 'image/gif':
                imagegif($NewCanves,$DestFolder);
                break;
            case 'image/jpeg':
            case 'image/pjpeg':
                imagejpeg($NewCanves,$DestFolder,$Quality);
                break;
            default:
                return false;
        }
    //Destroy image, frees up memory
    if(is_resource($NewCanves)) {imagedestroy($NewCanves);}
    return true;

    }

}
