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
				/* Login Error */
				if($error_log == true) { ?>
					<a href="../index.php">Go back to the Login page</a>
				<?php
				}
				else { ?>
					<b>Your Cart</b><br><br>
					<table>
					<tr>
                                	<td><b><i>Product</i></b></td>
					<td><b><i>Quantity</i></b></td>
					</tr>
					</table>
				<?php
                                	$query = "SELECT * FROM cart INNER JOIN products ON cart.id_item=products.id WHERE sid='" .$_SESSION['username']."'";
                                	$res = mysql_query($query,$link) or die(mysql_error());
					$t = 0;
                              		while($record = mysql_fetch_assoc($res)) {?>
						<table>
						<tr>
						<td><?php print $record['item'];?></td>
						<td><?php print $record['quantity'];?></td>
						</tr>
						</table>
				<?php
						$t += $record['prize']*$record['quantity'];
					} ?>
					<br><hr><br>
					<b><i>Total:</i></b> <?php print $t;?>&euro;<br>
					<br><hr><br>
					<form action="pay.php" method="post" enctype="application/x-www-form-urlencoded">
					<input type="hidden" name="total" value="<?php print $t;?>">
					<table>
					<tr>					
					<td>Credit Card Number:</td>				
					<td><input type="text" name="cardnumber" size="30"></td>
					</tr>
					<tr>
					<td>Secred Code:</td>
					<td><input type="text" name="secretcode" size="5"></td>
					<tr>
					<tr>
					<td>Expiration Date (mm/yy):</td>
					<td><input type="text" name="expiration" size="5"></td>
					<tr>
					<td><input type="submit" name="checkout" value="Checkout"></td>
					</tr>
					</table>
					</form><br>
					<form action="emptycart.php" method="post" enctype="application/x-www-form-urlencoded">
					<input type="submit" name="emptycart" value="EmptyCart" />
					</form><br>
					<b><a href="store.php">Go back to the store</a></b>
				<?php
					mysql_close();
				} ?>
			</div>
                </div>
        </div>
<?php

include("footer.html");

?>

