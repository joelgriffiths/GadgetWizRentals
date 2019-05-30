<?php

include_once("config.php"); //include the config
include_once("user.php");

$mypagetype='rightsb';

$boxstyles = array(
    'ad' => "box-style7",
    'home' => "box-style1",
    'list' => "box-style1",
    'nosb' => "box-style4",
    'rightsb' =>"box-style5",
    'leftsb'=>"box-style6");

$title = "Contact Us";

include "top.php";
include "rightsb1.php";
//include "wide1.php";

?>

            <div class="t"></div>
            <div id="content">
                <div class="content">
                    <div>

<div id="cu">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="contact" name="contact">
    <fieldset>
        <legend>Contact form</legend>

        <input type='hidden' name='=msg_thankyou' value="Thank You!"/>

        <label for="name">Name</label>
        <input type="text" id="name" name="name" class="required" pattern=".{2,}"  value="<?=$user->getFirstName();?> <?=$user->getLastName();?>"/>
        
        <label for="city">City</label>
        <input type="text" id="city" name="city" class="required" minlength="2"/>
        
        <label for="email">E&ndash;mail</label>
        <input type="text" id="email" name="email" class="required email" value="<?=$user->getEmail();?>"/>
        
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" class="required subject"/>
        
        <label for="message">Message</label>
        <textarea id="message" name="message" cols="50" rows="10" class="required"></textarea>
        
        <h3>Are you human?</h3>
        <img src="captcha.php?different=<?php echo rand();?>" id="captcha" alt="captcha"/>
        <label for="security_code">Captcha</label>
        <input type="text" id="security_code" name="security_code" autocomplete="off" class="required"/>
        
        <input type='button' class='ajaxmail-send' value='Send' />
    </fieldset>
</form>
</div>


                    </div>
                </div>
            </div>
            <div id="sidebar">
                <div id="box2">
                    <a href='/' style='text-decoration: none'>Zalaxy Inc.</a><br />
                    11706 Casa Pinto St<br />
                    San Antonio, TX 78233<br />
                    Phone: (530) 388-5635<br />
                    Fax: (678) 960-7919<br />
                    <br />
                    <img src="/images/support-email.png" alt="support at zalaxy . comm" />
                </div>
                <br />
                <br />
                <br />
                <br />
                <div id="box3">
                    <h2>What do <strong>YOU</strong> have in your Back Yard?</h2>
                </div>
                <div>
                    <h2></h2>
                    <p></p>
                </div>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="b"></div>

<?php
//include "wide2.php";
include "rightsb2.php";
include "bottom.php";
?>

