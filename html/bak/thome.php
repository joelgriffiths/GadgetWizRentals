<?php

$mypagetype='home';

$boxstyles = array( 
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");
#$mypagetype='nonav';

include "top.php";
?>
<div id="content">
        <div id="box1" class="box-style1">
                <div class="t"></div>
                <div class="content"> <img src="images/homepage_13.jpg" alt="" width="130" height="120" class="alignleft" />
                        <h1>Tempus blandit aliquam?</h1>
                        <p class="text"><strong>Aliquam lectus nulla</strong> sollicitudin nec viverra sed lorem. Cras rutrum mattis duis sed dolor.</p>
                        <p class="more"><a href="#" class="button button-style1"><span>Sed Faucibus</span></a>
                </div>
                <div class="b"></div>
        </div>
        <div class="box-style2">
                <div class="content">
                        <h2>New Listings</h2>
                        <div class="links"> ( <a href="#">See Them All</a> | <a href="#">Post Your Own</a> ) </div>
                </div>
        </div>
        <div class="box-style1">
                <div class="t"></div>
                <div class="content">
                        <ul class="list-style1">
                                <li class="featured first">
                                        <div class="image"><a href="#"><img src="images/homepage_06.jpg" alt="" width="190" height="166" /></a></div>
                                        <div class="price"><span>$125</span></div>
                                        <div class="info">
                                                <h3><a href="#">NEW Sed veroeros blan</a></h3>
                                        </div>
                                </li>
                                <li class="featured">
                                        <div class="image"><a href="#"><img src="images/homepage_07.jpg" alt="" width="190" height="166" /></a></div>
                                        <div class="price"><span>$25</span></div>
                                        <div class="info">
                                                <h3><a href="#">Donec tortor eget diam...</a></h3>
                                        </div>
                                </li>
                                <li class="featured">
                                        <div class="image"><a href="#"><img src="images/homepage_08.jpg" alt="" width="190" height="166" /></a></div>
                                        <div class="price"><span>$50</span></div>
                                        <div class="info">
                                                <h3><a href="#">Sed lacinia tempus dolor</a></h3>
                                        </div>
                                </li>
                                <li>
                                        <div class="image"><a href="#"><img src="images/homepage_09.jpg" alt="" width="47" height="41" /></a></div>
                                        <div class="price"><span>$50</span></div>
                                        <div class="info">
                                                <h3><a href="#">Nunc dignissim lacus condimentum ut convallis</a></h3>
                                                <p>2 hours ago (Madison, TN)</p>
                                        </div>
                                </li>
                                <li>
                                        <div class="image"><a href="#"><img src="images/homepage_10.jpg" alt="" width="47" height="41" /></a></div>
                                        <div class="price"><span>$50</span></div>
                                        <div class="info">
                                                <h3><a href="#">Risus cursus praesent consequat</a></h3>
                                                <p>2 hours ago (Murfreesboro, TN)</p>
                                        </div>
                                </li>
                                <li>
                                        <div class="image"><a href="#"><img src="images/homepage_11.jpg" alt="" width="47" height="41" /></a></div>
                                        <div class="price"><span>$50</span></div>
                                        <div class="info">
                                                <h3><a href="#">Tellus sit amet enim sollicitudin tincidunt</a></h3>
                                                <p>4 hours ago (Nashville, TN)</p>
                                        </div>
                                </li>
                        </ul>
                        <div class="bottom-nav"> <a href="#" class="button button-style2"><span>Browse All Listings</span></a> <a href="#" class="button button-style3"><span>Post an Ad</span></a> </div>
                </div>
                <div class="b"></div>
        </div>
</div>

<?php
include "bottom.php";
?>
