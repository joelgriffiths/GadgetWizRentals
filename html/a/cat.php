<?php
include_once("config.php"); //include the config
$sess = new Session();
$sess->start_session('_s', false);

include_once "Category.php";
include_once "CatSelector.php";
include_once "User.php";

$user = new User();
$lic = $user->login_check();

if($user->getUserName() != 'joelg') {
    exit;
}

function echoLevels($aLevel, $aVis, $class, $indent = '') {
    $thispage = !empty($_GET['urlname']) ? $_GET['urlname'] : '';
    if(!is_array($aLevel)) {
        return;
    }
    if(empty($aLevel))
        return;
    if($class != 0) echo "$indent<li><!-- Start $class -->\n";
    //print_r($aLevel);
    echo $indent.'<ul class="list-style2-'.$class.'">'."\n";

    /*
    $thiscat = new Category($aLevel[0]);
    $name = $thiscat->getName();
    $parent = $thiscat->getParentID();
    $url = $thiscat->getURL();
    */
    //echo "<input name='$class' value='$class $parent $name $url' />";
    foreach ($aLevel as $nextcat) {
        $nextcat = new Category($nextcat);

        $selected = '';
        $thisitem = $nextcat->getURL();
        if($thispage === $thisitem) {
            //$selected = 'style="color: rgb(204,51,51);"';
            $selected = 'id="selected"';
        }

        $hidden = !in_array($nextcat->getCatID(), $aVis) ? ' style="display: none;" ' : '';
        $hidden = '';

        //echo $indent.'    <li><a '.$selected.$hidden.' href="/for-rent/'.$nextcat->getURL().'.html">'.$nextcat->getName().'</a></li>'."\n";
        echo $indent.'    <li><a '.$selected.$hidden.' href="/for-rent/'.$nextcat->getURL().'.html">'.$nextcat->getParentID().':'.$nextcat->getCatID().' '.$nextcat->getName().'</a> <small><a href="/a/addcat.php?catid='.$nextcat->getCatID().'" class="default_popup">Add</a> <a href="/a/delcat.php?catid='.$nextcat->getCatID().'" class="default_popup">Del</a></small></li>'."\n";
        echoLevels($nextcat->GetChildMembers(), $aVis, $class+1, $indent."    ");
    }
    echo $indent.'</ul><!--END "'.$class." -->\n";
    if($class != 0) echo "$indent</li><!-- End $class -->\n";
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/popup.css" />
<script type="text/javascript" src="/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="/js/jquery.popup.js"></script>
<script type="text/javascript">
<!--
$(function(){
    // Default usage
    $('.default_popup').popup();
});
-->
</script>

</head>
<body>
<?php

echo 'Root <small><a href="/a/addcat.php?catid=0" class="default_popup">Add</a></small>'."\n";
// You can thank me later - This is the worse code I've ever written
$root = new Category(0);
//$selectedCat = new Category($_GET['urlname']);
//$visible = $selectedCat->GetVisArray();
$visible = array();
//error_log(print_r($visible,true));
echoLevels($root->GetChildMembers(), $visible, 0);


?>

<h1><?=$user->getUserName()?></h1>
catid parentid urlname humanname



</body>
</html>


