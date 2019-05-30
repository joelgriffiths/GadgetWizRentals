<?php
include_once "image.php";
include_once "FeedbackScore.php";

// $userid and $starid is required
$starid = $starsobj->getUserID();
$username = $starsobj->getUsername();
$profileimages = new Image();
$profileimage = $profileimages->getImage($starid, 'profile',0);

$fbs = new FeedbackScore($starid);
$buyercount = $fbs->getBuyerCount();
$sellercount = $fbs->getSellerCount();
$totalcount = $fbs->getTotalCount();
$feedbackscore = $fbs->getTotalScore();

error_log(print_r($user,true));

?>
            <div id="sidebar">
                <div id="box2">
                    <?php
                    $images = new Image();
                    $image = $images->getImage($starid, 'profile',0);
                    ?>

                    <h2><?=$username?></h2>

                    <ul class="list-style4" style="text-align:center">
                        <?php if($image == 1) : ?>
                            <li><a href="up.php?pos=0" class="default_popup"><img src='<?=$images->GetProfileImageSrc($starid)?>' width='100' alt='Image <?=$image?>'/></a></li>
                        <?php else :?>
                            <?php
                            if(isset($no_popup) && $no_popup == 1) {?>
                                <li><a href="users.php?un=<?=$username;?>"><img src='<?=$images->GetProfileImageSrc($starid);?>' width='100' alt='Image <?=$image?>'/></a></li>
                            <?php } else { ?>
                                <li><a href="img-box.php?img=<?=$image;?>" class="default_popup"><img src='<?=$images->GetProfileImageSrc($starid);?>' width='100' alt='Image <?=$image?>'/></a></li>
                            <?php } ?>
                        <?php endif;?>

                        <?php
                        $ppage =  basename($_SERVER['PHP_SELF']);
                        if($ppage === 'profile.php') :?>
                            <li><a href="up.php?pos=0&amp;type=profile" class="default_popup">Update Profile Image</a></li>
                        <?php endif; ?>
                        <?php if($feedbackscore == null) :?>
                            <li><p class='centerp'>No Feedback Yet</p></li>
                        <?php else: ?>
                            <li><p class='centerp'>Feedback Score: <?=number_format((float)$feedbackscore,2, '.', '')?></p></li>
                            <li><div class='centerstar'><span class="stars"><?=$feedbackscore?></span></div></li>
                        <?php endif; ?>

                        <!--?php include "stars.php"; ?-->
                        <!-- TODO li>Member Since</li-->
                        <!-- TODO li>Oct 20, 2013</li-->
                        <!-- TODO li><a href="#">Delete</a></li-->
                </ul>
            </div>
        </div>
<br />
<script type="text/javascript">
<!--

$.fn.stars = function() {
    return $(this).each(function() {
        // Get the value
        var val = parseFloat($(this).html());
        // Make sure that the value is in 0 - 5 range, multiply to get width
        val = Math.round(val * 4) / 4;
        var size = Math.max(0, (Math.min(5, val))) * 16;
        // Create stars holder
        var $span = $('<span />').width(size);
        // Replace the numerical value with stars
        $(this).html($span);
    });
}

$(function() {
    $('span.stars').stars();
});

-->
</script>

