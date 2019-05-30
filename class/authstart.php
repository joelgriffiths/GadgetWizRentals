<?php

if($lic === false):
?>

<div id="stylized" class="itemform">
<form id="itemform" name='itemform' method="post" action="profile.php">
  <h1>Authentication Required</h1>
  <fieldset>
  <p>We're sorry. You have been logged out.</p>

  <div id="ack2">Please <a href='login.php'>Log In</a> again.</div>

  </fieldset>
  </form>


<?php
// Display the page's content if authenticated is okay
else:
?>
