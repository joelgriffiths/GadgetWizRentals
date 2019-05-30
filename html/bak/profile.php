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
                    <label>First Name<span class="small"></span></label>
                    <input type="text" name="first" id="first" value="<?=$user->getFirstName();?>"/>
		       </div>
                
                <div class="right">
                    <label>Last Name<span class="small"></span></label>
                    <input type="text" name="last" id="last" value="<?=$user->getLastName();?>" />
               </div>


                <!--p>Your email address is ALWAYS kept private</p-->
                <div class="full">
                    <label>Email<span class="small"></span></label>
                    <input type="text" name="email" id="email" value="<?=$user->getEmail();?>" />
                </div>

               <div style="text-align: center;"><input type="submit" class="center" value="Update Profile" /></div>
               </fieldset>
               </form>
            </div> <!-- id="stylized" -->
<?php
include "profile-bottom.php";
?>
