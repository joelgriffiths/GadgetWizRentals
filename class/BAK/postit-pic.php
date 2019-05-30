<center>
<h2><?$user->getUsername();?></h2>
<ul class="list-style4">
<?php

include_once "cImage.php";

$newsession = array();

if($itemid == 0) {
    //$images = is_array($_SESSION['tmpimageid']) ? $_SESSION['tmpimageid'] : array();
    $images = $images->getAllDBImagesByUserID($user->getUserID(), $itemid, 'listing');
} else {
    $images = $images->getAllDBImagesByUserID($user->getUserID(), $itemid, 'listing');
}

error_log("SESSSION: ".print_r($images,true));
$pos = 0;
foreach($images as $image) {
    $img = new cImage($image);

    $pos = $img->GetPosition();

     // Doing this to address a different bug. Sorry. Annoying.
     if( $pos == '') {
            continue;
     }

    // unset wasn't working, so I'll just do it this way
    array_push($newsession, $image);

?>
        <li><?=$image?></li>
        <!--li><a href="up.php?pos=<?=$pos?>" class="default_popup">Change</a></li-->
        <li><a href="img-box.php?img=<?=$image;?>" class="default_popup"><img src='<?=$img->GetListingImageSrc(100);?>' width='100' alt='Image <?=$image?>'/></a></li>
        <br />
<?php
};
// Update the session variable with the existing images
$_SESSION['tmpimageid'] = $newsession;
$pos++;
if($pos<9) :
?>
        <li><a href='up.php?pos=<?=$pos?>' class='default_popup'>Add Photo</a></li>
<?php endif;?>
        <!-- TODO li>Star Star Star Star</li-->
        <!-- TODO li>Member Since</li-->
        <!-- TODO li>Oct 20, 2013</li-->
        <!-- TODO li><a href="#">Delete</a></li-->
</ul>
</center>
<br />

