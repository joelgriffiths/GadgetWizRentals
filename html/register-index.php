<?php
$title = "Welcome to Zalaxy.com";
$header = "Zalaxy";
$valid=true;
include "header.php";
?>

<div id="container">
    <header id="head" class="container_12">
    <p><span id="logo_container"><?=$header?></span></p>
    <p><a href="register.php"><span id="register">Register</span></a></p>
    <div id="navigation" class="container_12">
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contact us</a></li>
        </ul>
    </div>
    </header>

<div id="contentdiv" class="container_12">
    <div id="mainnav">
        <ul>
            <li><a href="#">Section 1</a></li>
            <li><a href="#">Section 2</a></li>
            <li><a href="#">Section 3</a></li>
            <li><a href="#">Section 4</a></li>
        </ul>
    </div>
    <div id="menu">
        <h3>
            Archives
        </h3>
        <ul>
            <li><a href="#">December 2003</a></li>
            <li><a href="#">November 2003</a></li>
            <li><a href="#">October 2003</a></li>
            <li><a href="#">September 2003</a></li>
            <li><a href="#">August 2003</a></li>
        </ul>
        <h3>
            Last 10 Entries
        </h3>
        <ul>
            <li><a href="#">Entry 120 (4)</a></li>
            <li><a href="#">Entry 119 (0)</a></li>
            <li><a href="#">Entry 118 (9)</a></li>
            <li><a href="#">Entry 117 (3)</a></li>
        </ul>
     </div>
    <div id="contents">
        <div class="blogentry">
            <h2>
                <a href="#" title="Permanent link to this item">Heading here</a>
            </h2>
            <h3>
                Wednesday, 3 December 2003
            </h3>
            <p>
                <!--img class="imagefloat" src="flower.jpg" alt="" width="100" height="100"-->
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. <a href="#">Duis autem vel eum</a> iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent.
            </p>
            <p>
                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.
            </p>
            <ul>
                <li><a href="#">Comments (4)</a></li>
                <li><a href="#">Pingbacks (1)</a></li>
                <li>Category: <a href="#" title="Category">CSS</a></li>
            </ul>
        </div>
    </div>
    <div id="footer">
        Copyright © Zalaxy 2013
    </div>
</div>
</div>

<?php
require 'footer.php';
?>
