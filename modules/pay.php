<?php

require_once("config.php");
require_once("class_session.php");

session_start();

if(isset($_GET['logout'])) {
        session_destroy();
        header('Location: ../index.php');
}

$link = mysql_connect(HOST, USER, PW);
if (!$link) {
        die ("Error connecting to the database: " . mysql_error());
}

$db_selected = mysql_select_db(DB, $link);
if (!$db_selected) {
        die ("Error selecting the database: " . mysql_error());
}

/* Get the parameters from the form */
$total = $_POST['total'];
$cardnumber = $_POST['cardnumber'];
$secretcode = $_POST['secretcode'];
$expiration = $_POST['expiration'];

$error_log = false;
$error_flag = false;

include("header.html");

?>
	<div id="center">
                <div id="bar">
                       <?php include("logstatus.php"); ?>
		</div>
		<div id="navigation">
                        <div id="pagenav">
			<?php
				/* Login Error */
				if($error_log == true) { ?>
					<a href="../index.php">Go back to the Login page</a>
			<?php
				}
				/* Check the payment form parameters */
				if($cardnumber == "" ||  $secretcode == "" || $expiration = "") {
                                   	echo "One or more payment fields are empty!";
					$error_flag = true;
				}
				else if(!preg_match("/^([0-9]{16})$/", $cardnumber) || !preg_match("/^([0-9]{3})$/",$secretcode)) {
					echo "One or more fields are wrong!";
					$error_flag = true;
				}
				
				/* Operation Error */
				if($error_flag == true) { ?>
					<br>
					<a href="showcart.php">Go back to the Cart</a>
			<?php
				}
				else {
					$query = "LOCK TABLES products WRITE, cart WRITE";
					mysql_query($query,$link) or die(mysql_error());	
					$query = "SELECT * FROM cart WHERE sid='" .$_SESSION['username']. "'";
					//$res = mysql_query($query,$link) or die(mysql_error());
					$res = mysql_query($query);
					if(!$res) {
						mysql_query("UNLOCK TABLES");
						mysql_close();
						print mysql_error();
					}

					$not_av_flag = false;
					$not_av;

					while($record = mysql_fetch_array($res)) {
						$id = $record['id_item'];
						$q = $record['quantity'];
						$query = "SELECT * FROM products WHERE id=" .$id;
						//$res2 = mysql_query($query,$link) or die(mysql_error());
						$res2 = mysql_query($query);
						if(!$res2) {
							mysql_query("UNLOCK TABLES");
							mysql_close;
							print mysql_error();
						}
						$record2 = mysql_fetch_array($res2);
						$number = $record2['number'];	
						/* The products you've chosen are no longer available or are not present in the needed amount */
						if($number < $q) {
							$not_av_flag = true;
							$not_av[] = $id;
						}
					}

					if($not_av == true) {
						$query = "UNLOCK TABLES";
						mysql_query($query,$link) or die(mysql_error());
						mysql_close();
						echo "Sorry, the products you've chosen are no longer available or are not present in the amount of your choice!";
						?>
						<br>
						<a href="store.php">Go back to the store</a>
			<?php		
					}
					else {
						$random_pay = rand(1, 10);
						/* SIMULATING PAYMENT - UNSUCCESSFUL TRANSATION */
						if($random_pay > 5) {
							$query = "UNLOCK TABLES";
							mysql_query($query,$link) or die(mysql_error());
                                                	mysql_close();
							echo "Sorry, Payment Unsuccessful!";?>
							<br>
                                                	<a href="showcart.php">Go back to the Cart</a>	
			<?php			
						}
						/* SIMULATING PAYMENT - SUCCESSFUL TRANSATION */
						else {
							$query = "SELECT * FROM cart INNER JOIN products ON cart.id_item=products.id WHERE sid='" .$_SESSION['username']. "'";
							$res = mysql_query($query,$link) or die(mysql_error());
							while($record = mysql_fetch_array($res)) {
								$n = $record['quantity'];
								$id = $record['id_item'];
								/* Decrease the product number from the store */
								$query = "UPDATE products SET number=number-" .$n. " WHERE id=" .$id;
								//mysql_query($query,$link) or die(mysql_error());
								$result = mysql_query($query);
								if(!$result) {
                                                        		mysql_query("UNLOCK TABLES");
                                                        		mysql_close();
                                                      			print mysql_error();
                                                		}
							}	
						/* ALL OPERATIONS SUCCESSFUL */
						$query = "DELETE FROM cart WHERE sid='" .$_SESSION['username']. "'";
						//mysql_query($query,$link) or die(mysql_error());
						$result = mysql_query($query);
						if(!$result) {
							mysql_query("UNLOCK TABLES");
							mysql_close();
							print mysql_error();
						}
						$query = "UNLOCK TABLES";
						mysql_query($query,$link) or die(mysql_error());
						mysql_close();
						echo "Payment Successful!"; ?>
						<br>
						<a href="store.php">Go back to the Store</a> if you want to buy something else...
			<?php						
						}			
					}	
				}
			?>
			</div>
                </div>
        </div>
<?php

include("footer.html");

?>
