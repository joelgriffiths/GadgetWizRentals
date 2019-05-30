<?php

$authrequired = true;
if(SITEID == 2) {
    $authrequired = false;
}
foreach($donotauth as $openpages)
{
  //error_log("authcheck: ".basename($_SERVER['PHP_SELF']).":".$openpages."<br />");
  if(strpos($_SERVER['PHP_SELF'],'for-rent') !== false)
  {
    $authrequired = false;
    //error_log("Auth Match for-rent");
    break;
  }
  if(strpos(basename($_SERVER['PHP_SELF']), $openpages) === 0) 
  {
    $authrequired = false;
    //error_log("Auth Match openpages");
    break;
  }
}

$nextpage = isset($_GET['nextpage']) ? $_GET['nextpage'] : '/';

if($authrequired === true && $lic === false):
?>

<script type="/text/javascript">
<!--
window.onload = function() {
window.setTimeout("window.location = '<?=$nextpage?>'", 1500);
}
-->
</script>


<div id="stylized" class="itemform">
<form id="itemform" name='itemform' method="post" action="profile.php">
  <h1>Authentication Required</h1>
  <fieldset>
  <!--p>We're sorry. You have been logged out.</p-->

  <div id="ack2">Please <a href='login.php'>Log In</a> again.</div>

  </fieldset>
  </form>
</div>

<?php
include "accountmenu2.php";
include "bottom.php";
exit;
endif;
?>
