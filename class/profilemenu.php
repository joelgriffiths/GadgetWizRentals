<?php

function active($page) {
    $thispage = basename($_SERVER['PHP_SELF']);

    if(strcmp($thispage, $page) == 0)
        echo "class='topmenu active'";
    else
        echo "class='topmenu'";
}
?>
<h1 class='pmenu'>
<a <?php active("profile.php");?> href="profile.php">Identity</a>
<a <?php active("profile-contact.php");?> href="profile-contact.php">Contact Information</a>
<a <?php active("profile-password.php");?> href="profile-password.php">Change Password</a>
<!--a <?php active("profile-availability.php");?> href="profile-availability.php">Hours (Availability)</a-->
</h1>

