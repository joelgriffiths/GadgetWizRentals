<?php
include_once('config.php');

function activated($page) {
	$curPage = basename($_SERVER['PHP_SELF']);
	if($curPage === $page) {
		echo 'class="active"';
    }
}

?>
