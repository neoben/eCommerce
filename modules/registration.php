<?php

require_once("config.php");

$link = mysql_connect(HOST, USER, PW);
if (!$link) {
	die ("Error connecting to the database: " . mysql_error());
}

$db_selected = mysql_select_db(DB, $link);
if (!$db_selected) {
	die ("Error selecting the database: " . mysql_error());
}

/* Get the variables from the registration form */
$name = mysql_real_escape_string($_POST['name']);
$surname = mysql_real_escape_string($_POST['surname']);
$username = mysql_real_escape_string($_POST['username']);
$password = mysql_real_escape_string($_POST['password']);
$country = mysql_real_escape_string($_POST['country']);
$address = mysql_real_escape_string($_POST['address']);
$email = mysql_real_escape_string($_POST['email']);

/* Check section parameters */
$text_match = "/^[a-zA-Z][a-z]*/";
$email_match = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/";

$error_flag = false;

include("header.html");

?>
	<div id="center">
		<div id="navigation">
			<div id="pagenav">
			<?php
			/* Check the registration form parameteres */
			if($name == '' || $surname == '' || $username == '' || $password == '' || $address == '' || $email == '') {
				echo "Error: one or more form fields are empty!";
				$error_flag = true; 
				mysql_close(); ?>
				<br><br>
				<a href="register.html">Go back to the registration form</a>			
			<?php }

			if($error_flag == false && ( !preg_match($text_match, $name) || !preg_match($text_match, $surname) || !preg_match($text_match, $username) || !preg_match($text_match, $country) || !preg_match($email_match, $email) )) {
				echo "Error: one or more form fields are in not valid format!";
				$error_flag = true; 
				mysql_close(); ?>
                                <br><br>
                                <a href="register.html">Go back to the registration form</a>
			<?php }

			if($error_flag == false && ( strlen($password) < 6 || strlen($password) > 15 )) {
				echo "The password must be at least 6 characters (max 15 characters)!";
				$error_flag = true;
				mysql_close(); ?>
				<br><br>
                                <a href="register.html">Go back to the registration form</a>
			<?php } 
			
			if($error_flag == false) {
				/* Check if the username already exists (lock the users database table) */
				if(!mysql_query("LOCK TABLES users WRITE")) {
	           			mysql_close();
		    			print mysql_error();
	        		}

				/* LIMIT 1: stop searching if you find a match */
				$query = "SELECT username FROM users WHERE username = '".$username."' LIMIT 1";
				$result = mysql_query($query);

				if (!$result) {
	            			mysql_query("UNLOCK TABLES");
	            			mysql_close();
		    			print mysql_error();
	        		}
	
				/* The usernmae already exists (unlock the user database table) */
				if(mysql_num_rows($result) != 0) { 
					mysql_query("UNLOCK TABLES");
		        		mysql_close();
					echo "The username $username is already in use. Please choose another username";
					$error_flag = true ?>
					<br><br>
					<a href="register.html">Go back to the registration form</a>
				<?php }
			}
			
			if($error_flag == false) {
				/* Insert the user data in the database */
				$query = sprintf("INSERT INTO users (name, surname, country, address, password, email, username) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $name, $surname, $country, $address, $password, $email,$username);
				$result = mysql_query($query);
				
				if (!$result) {
                                        mysql_query("UNLOCK TABLES");
                                        mysql_close();
                                        print mysql_error();
                                }

				/* Registration Successful */
				mysql_query("UNLOCK TABLES");
				echo "Registration successful!";?>
				<br><br>
				Please <a href="../index.php">Login</a> to access the store.
 			<?php
			}
			?>
			</div>
		</div>
	</div>
<?php

include("footer.html");

?>
