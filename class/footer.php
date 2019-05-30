<div id="footer-bar-bg">
        <div id="footer-bar" class="container">
                <div class="col1">
                        <h2>Information</h2>
                        <ul class="list-style3">
                                <li class="first"><a href="/whatwedo.php">What do we do?</a></li>
                                <!--li><a href="/limitations.php">Limitation of Liabilities</a></li-->
                                <li><a href="/eula.php" target="_blank">User Agreement</a></li>
                                <li><a href="/credits.php">Credits</a></li>
                        </ul>
                </div>
                <div class="col2">
                        <!--h2>About Zalaxy</h2>
                        <p>Thanks for visiting <a href="http://www.zalaxy.com">Zalaxy.com</a>.</p-->
                </div>
        </div>
</div>
<div id="footer">
        <p>Copyright &copy; 2013-2014 Zalaxy, Inc. All rights reserved.</p>
</div>
</body>
</html>
<?php
if(true) {
} else {
//$usr = new Users; //create a new instance of the Users class
//$usr->storeFormValues( $_POST ); //like I said before we will use the function storeFormValues to store the form values

if( $usr->userLogin() ) {
echo "Welcome";
} else {
echo "Incorrect Username/Password";
}
}
?>

