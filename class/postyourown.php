                    <!-- FEATURED START -->
                    <div class="box-style9">
                        <div class="t"></div>
                        <div class="content">
                            <ul class="list-style7">
                                <li>
                                    <div class="image"><!--a href="#"><img src="/images/lists_04.jpg" alt="" width="67" height="58" /></a--></div>
                                    <!--div class="price"><span><a href="/postit.php"></a></span></div-->
                                    <div class="info">
                                                            <h4 style="text-align: center">Website Launched September 8, 2013 - We Need Your Things</h4>

                                        <p style="text-align: center;">There <?=$isare?> only <?=$numresults?> <?=$itemtext?> for rent in this category.</p>
<?php if ($lic === true) : ?>
                                        <p style="text-align: center;">If you have something to rent, you can <a href="/postit.php">List it here</a> and earn cash for it.</p>
<?php else: ?>
                                        <p style="text-align: center;">If you have something to rent, you can <a href="/login.php">Sign In</a> or  <a href="/register.php">Register for an account</a> and earn cash for it.</p>
<?php endif ?>
                                        <p style="text-align: center; margin: 10px 0px 0px 0px;">There are no fees if your item is not rented (<a href='/fees.php' class="default_popup">See Details</a>).</p>
                                        <p style="text-align: center; margin: 10px 0px 0px 0px;"><a href='/contact.php'>We would love to hear from you</a></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="b"></div>
                    </div>
                    <!-- FEATURED END -->

