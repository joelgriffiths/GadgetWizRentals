<?php
include_once("profile-top.php"); //HACK HACK
?>
            <!--a href="#" class="html_popup">Link</a-->
            <div id="stylized" class="profileform">
                <form id="itemform" name='itemform' method="post" action="profile.php">
                <?php include("profilemenu.php");?>
                <fieldset>
               
                <div id="ack2"></div>

                <div class="full">
                    <label>Current Password<span class="small"></span></label>
                    <input type="password" name="curpassword" id="curpassword" />
                </div>

                <div class="left">
                    <label>New Password<span class="small"></span></label>
                    <input type="password" name="password" id="password" />
		</div>
                
                <div class="right">
                    <label>Confirm New Password<span class="small"></span></label>
                    <input type="password" name="conpassword" id="conpassword" />
               </div>

               <div style="text-align: center;"><input type="submit" class="center" value="Update Profile" /></div>
               </fieldset>
               </form>
            </div> <!-- id="stylized" -->
<?php
include "profile-bottom.php";
?>
