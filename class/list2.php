<!-- BOTTOM -->
                    <div class="bottom-nav">
<?php
/*
 * button-style3 = Enabled
 * button-style4 = Disabled
 */

if($prevpage !== null) {
    if($prevpage === 0) {
        $pagetext = '';
    } else {
        $pagetext = 'page'.$prevpage.'/';
    }
    echo "<a href='/for-rent/$pagetext".$selectedCat->GetURLHTML()."' class='button button-style3'><span>Previous $itemsperpage Listings</span></a>";
}

if($nextpage !== null) {
    echo "<a href='/for-rent/page$nextpage/".$selectedCat->GetURLHTML()."' class='button button-style3'><span>Next $itemsperpage Listings</span></a>";
}
?>
                </div>
                </div>
                <div class="b"></div>
            </div>
        </div>
<?php
include "categories.php";
?>

