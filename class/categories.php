<!-- BEGIN CATEGORIES -->
                <div id="sidebar">
                        <div class="box-style3">
                                <div class="t"></div>
                                <div class="content">

                                <?php if($closelocations != '') :?>
                                <div id='regionselect'><span title='Only items from this location are displayed'>
                                <a href="/region-select.php" id='searchcountry' class="regionbutton small orange region_select"><?=$country?></a>
                                <a href="/region-select.php" id='searchstate' class="regionbutton small blue region_select"><?=$state?></a>
                                <a href="/region-select.php" id='searchcity' class="regionbutton small black region_select"><?=$city?></a>
                                </span></div>
                                <?php else: ?>
                                <div id='regionselect'><span title='Where are you searching for items? Click to select.'>
                                <a href="/region-select.php" id='searchglobal' class="regionbutton small orange region_select">Choose Your Search Area</a>
                                </span></div>
                                <?php endif; ?>
                                <br />


                                        <div id="search">
                                                <form method="post" action="/for-rent.php" id="foobar">
                                                        <fieldset>
                                                                <input id="q" type="text" name="search" value="Search all listings" title="Search all listings" />
                                                                <input id="go" type="submit" />
                                                        <!--Rent Your Thing!&trade;-->
                                                        </fieldset>
                                                </form>
                                                <script type="text/javascript">
                                                        $('#foobar').formerize();
                                                </script>
                                        </div>
                                        <div class="categories">
<?php

include_once "Category.php";
function echoLevels($aLevel, $aVis, $class, $thispage, $indent = '') {
    //$thispage2 = !empty($_GET['urlname']) ? $_GET['urlname'] : '';
    //error_log("$thispage -- $thispage2");
    if(empty($aLevel)) {
        return;
    }
    if($class != 0) echo "$indent<li><!-- Start $class -->\n";
    echo $indent.'<ul class="list-style2-'.$class.'">'."\n";
    foreach ($aLevel as $nextcat) {
        $nextcat = new Category($nextcat);

        $selected = '';
        $thisitem = $nextcat->getURL();
        if($thispage === $thisitem) {
            //$selected = 'style="color: rgb(204,51,51);"';
            $selected = 'id="selected"';
        }

        $hidden = !in_array($nextcat->getCatID(), $aVis) ? ' style="display: none;" ' : '';

        echo $indent.'    <li><a '.$selected.$hidden.' href="/for-rent/'.$nextcat->getURL().'.html">'.$nextcat->getName().'</a></li>'."\n";
        echoLevels($nextcat->GetChildMembers(), $aVis, $class+1, $thispage, $indent."    ");
    }
    echo $indent.'</ul><!--END "'.$class." -->\n";
    if($class != 0) echo "$indent</li><!-- End $class -->\n";
}

// You can thank me later - This is the worse code I've ever written
$root = new Category(0);
$urlname = !empty($_GET['urlname']) ? $_GET['urlname'] : '';
if(!isset($selectedCat) || !is_object($selectedCat)) {
    $selectedCat = new Category($urlname);
}
$thiscat = $selectedCat->GetURL();
$visible = $selectedCat->GetVisArray();
echoLevels($root->GetChildMembers(), $visible, 0, $thiscat);

?>
                                        </div>
                                </div>
                                <div class="b"></div>

                        </div>
                        <div>
                        
                        
                        <a href="#"><img src="/images/homepage_12.jpg" alt="" width="260" height="145" /></a></div>
                </div>
<!-- END CATEGORIES -->
