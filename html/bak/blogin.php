<?php
$title = "Linux Technical Notes";
$header = "Welcome to GadgetWiz.com";
$valid=true;
include "header.php";
?>
		 <div id="login-wrapper">
			 <form method="post" action="">
				 <ul>
					 <li>
						 <label for="usn">Username : </label>
						 <input type="text" maxlength="30" required autofocus name="username" />
					 </li>
					
					 <li>
						 <label for="passwd">Password : </label>
						 <input type="password" maxlength="30" required name="password" />
					 </li>
					 <li class="buttons">
						 <input type="submit" name="login" value="Log me in" />
							<input type="button" name="register" value="Register" onclick="location.href='register.php'" />
					 </li>
					
				 </ul>
			 </form>
				
			</div>
<?php
include 'footer.php';
?>
