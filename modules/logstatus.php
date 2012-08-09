<div id="login">
 	<b>Login</b><br><br>
	<?php
	if(isset($_SESSION['username'])) {
   	print "Logged as " .$_SESSION['username']; ?>
      	<br><br>
    	<a href="store.php?logout">Logout</a>
	<?php
	}
	else {
		echo "Login error!";
		$error_log = true;
	} ?>
</div>
