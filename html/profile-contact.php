<?php
include_once("profile-top.php"); //HACK HACK
?>
            <!--a href="#" class="html_popup">Link</a-->
            <div id="stylized" class="profileform">
                <form id="itemform" name='itemform' method="post" action="profile.php">
                <?php include("profilemenu.php");?>
                <fieldset>
               
                <div id="ack2"></div>

                <div class="left">
                <label>Address<span class="small"></span></label>
                <input type="text" name="address1" id="address1" value="<?=$userinfo->getAddress1();?>"/>
                <input type="text" name="address2" id="address2" value="<?=$userinfo->getAddress2();?>"/>
                <label>Zip<span class="small"></span></label>
                <input type="text" name="zip" maxlength='5' class="zip" value="<?=$userinfo->getZip();?>" />-<input type="text" name="zip4" maxlength='4' class="zip4" value="<?=$userinfo->getZip4();?>" />
		       </div>
                
                <div class="right">
                    <label>City<span class="small"></span></label>
                    <input type="text" name="city" class="city" value="<?=$userinfo->getCity();?>"/>
<select name="state" class="state"> 
<option value="" >State</option> 
<?php $userinfo->getStateOptions(); ?>
</select>
                </div>

                <div class="left">
                    <label>Primary Phone<span class="small"></span></label>
                    <input type="text" name="pphone" id="pphone" value="<?=$userinfo->getPrimaryPhone();?>"/>
                </div>
                <div class="right">
                    <label>Alternate Phone<span class="small"></span></label>
                    <input type="text" name="aphone" id="aphone" value="<?=$userinfo->getAltPhone();?>"/>
               </div>


               <div class="full">
               <div style="text-align: center;"><input type="submit" class="center" value="Update Profile" /></div>
               </div>
               </fieldset>
               </form>
            </div> <!-- id="stylized" -->
<?php
include "profile-bottom.php";
?>
