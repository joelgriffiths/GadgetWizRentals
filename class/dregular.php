                    <!-- REGULAR START -->
                    <ul class="list-style1" <?=$borderbottom?>>
                        <li class="first-list">
                            <?php if(is_numeric($imageid)) :?>
                            <div class="image"><a href="/img-box.php?img=<?=$imageid?>" class="default_popup"><img src="<?=$thumbnail?>" alt="" width="80" /></a></div>
                            <?php else :?>
                            <div class="image"><img src="<?=$thumbnail?>" alt="" width="80" /></div>
                            <?php endif;?>
                            <div class="price"><span>$<?=$itemprice?>/<?=$iteminterval?></span></div>
                            <div class="info">
                                <h3><a href="/rental.php?itemid=<?=$itemid?>"><?=$itemtitle?></a></h3>
                                <p>Rental Period: <?=$iteminterval?></p>
                                <p>Item Location: <?=$itemcity?>, <?=$itemstate?></p>
                            </div>
                        </li>
                    </ul>
                    <!-- REGULAR END -->

