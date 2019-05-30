<?php
include_once "FeedbackScore.php";

// Really need to stick this somewhere else. This is sloppy.

$fbs = new FeedbackScore($starid);
$buyercount = $fbs->getBuyerCount();
$sellercount = $fbs->getSellerCount();
$totalcount = $fbs->getTotalCount();
$feedbackscore = $fbs->getTotalScore();

?>

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
<?php
if($feedbackscore == null) :?>
                    <p class='centerp'>No Feedback Yet</p>
<?php else: ?>
                    <p class='centerp'>Feedback Score: <?=$feedbackscore?></p>
                    <div class='centerstar'><span class="stars"><?=$feedbackscore?></span></div>
<?php endif; ?>

