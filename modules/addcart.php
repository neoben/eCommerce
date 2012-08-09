<?php

require_once("config.php");
require_once("class_session.php");

session_start();

if(isset($_GET['logout'])) {
	session_destroy();
	header('Location: ../index.php');
}

/* Get the parameters from the form */
$itemsid = $_POST['itemsid'];
$quantity = $_POST['quantity'];
$sel = $_POST['sel'];

$conn = mysql_connect(HOST,USER,PW);
mysql_select_db(DB, $conn) or die(mysql_error());

$error_log = false;

include("header.html");

?>
	<div id="center">
		<div id="bar">
                       <?php include("logstatus.php"); ?>
		</div>
		<div id="navigation">
                        <div id="pagenav">
				<?php
				/* Update the cart using the cart parameters */
				if(is_array($itemsid) && !empty($itemsid)) {
					$query = "LOCK TABLES cart WRITE";
					mysql_query($query,$conn) or die(mysql_error());
					foreach($itemsid as $id) {
						$q = $quantity[$id];
						if(isset($sel[$id])) {
							$s = $sel[$id];
							$query = "SELECT * FROM cart WHERE sid='".$_SESSION['username']."' AND id_item=".$id;
							//$res = mysql_query($query,$conn) or die(mysql_error());
							$res = mysql_query($query);
                                        		if(!$res) {
                                                		mysql_query("UNLOCK TABLES");
                                                		mysql_close();
                                                		print mysql_error();
                                        		}
							if(mysql_num_rows($res) == 0) {
								$query ="INSERT INTO cart (sid,id_item,quantity) VALUES ('".$_SESSION['username']."',".$id.",".$q.")";
							}
							else {
								$query = "UPDATE cart SET quantity=quantity+".$q." WHERE sid='".$_SESSION['username']."' AND id_item=".$id;
							}
						//mysql_query($query,$conn) or die(mysql_error());
							$result = mysql_query($query);
                                        		if(!$result) {
                                                		mysql_query("UNLOCK TABLES");
                                                		mysql_close();
                                                		print mysql_error();
                                        		}
						}
					}
					$query = "UNLOCK TABLES";
					mysql_query($query,$conn) or die(mysql_error());
				}		
 				
				/* Login error */
                        	if($error_log == true) { ?>
					<a href="../index.php">Go back to the Login page</a>
				<?php
				}
				else {
					mysql_close();
					header('Location: store.php');
				}
				?> 
			</div>
                </div>
	</div>
<?php

include("footer.html");

?>
