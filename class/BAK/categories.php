<!-- BEGIN CATEGORIES -->
                <div id="sidebar">
                        <div class="box-style3">
                                <div class="t"></div>
                                <div class="content">
                                        <div id="search">
                                                <form method="get" action="" id="foobar">
                                                        <fieldset>
                                                                <input id="q" type="text" name="q" value="Search all listings" title="Search all listings" />
                                                                <input id="go" type="submit" />
                                                        </fieldset>
                                                </form>
                                                <script type="text/javascript">
                                                        $('#foobar').formerize();
                                                </script>
                                        </div>
                                        <div class="categories">
                                                <ul class="list-style2">
<?php
include "Category.php";
$root = new Category(0);






$cat = new Category($_GET['urlname']);
echo '<li><a href="'.$cat->getURL().'.html">&lt;&lt;'.$cat->getName().'</a></li>'."\n";
foreach ($cat->GetChildMembers() as $memberid) {
    //echo '<pre>';
    //print_r($memberid);
    //echo '</pre>';
    $member = new Category($memberid[0]);
    echo '<li><a href="'.$member->getURL().'.html">'.$member->getName().'</a></li>'."\n";
}
?>
                                                        <!--li class="first"><a href="#">Antiques</a></li>
                                                        <li><a href="#">Books</a></li>
                                                        <li><a href="#">Business</a></li>
                                                        <li><a href="#">Clothes</a></li>
                                                        <li><a href="#">Computers</a></li>
                                                        <li><a href="#">Construction</a></li>
                                                        <li><a href="#">Electronics</a></li>
                                                        <li><a href="#">Furniture</a></li>
                                                        <li><a href="#">Household</a></li>
                                                        <li><a href="#">Industrial</a></li>
                                                        <li><a href="#">Jewelry</a></li>
                                                        <li><a href="#">Music</a></li>
                                                        <li><a href="#">Musical Instruments</a></li>
                                                        <li><a href="#">Office</a></li>
                                                        <li><a href="#">Phones</a></li>
                                                        <li><a href="#">Photography</a></li>
                                                        <li><a href="#">Toys</a></li>
                                                        <li><a href="#">Video Games</a></li-->
                                                </ul>
                                        </div>
                                </div>
                                <div class="b"></div>
                        </div>
                        <div><a href="#"><img src="/images/homepage_12.jpg" alt="" width="260" height="145" /></a></div>
                </div>
<!-- END CATEGORIES -->
