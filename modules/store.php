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

$error_flag = false;

include("header.html");

?>
	<div id="center">
		<div id="bar">
			<div id="login">
				<b>Login</b><br><br>
				<?php
				if(!empty($_POST) && !isset($_SESSION['username'])) {
					$username = mysql_real_escape_string($_POST['username']);
					$password = mysql_real_escape_string($_POST['password']);

					/* LIMIT 1: stop searching if you find a match */
					$query = "SELECT * FROM users WHERE username='".$username."' AND password='".$password."' LIMIT 1";
					$result = mysql_query($query);

					if(!$result) {
						die ("Query error $query: " . mysql_error());
					}
					
					if(!mysql_num_rows($result)) {
						mysql_close();
						echo "Wrong username or password!";
						$error_flag = true;
					}
					else {
						/* Setup the SESSION */
						$_SESSION['username'] = $username;
						print "Logged as " .$_SESSION['username']; ?>
						<br><br>
						<a href="store.php?logout">Logout</a>			
				<?php
					}
				}
				else if(isset($_SESSION['username'])) {
					print "Logged as " .$_SESSION['username']; ?>
					<br><br>
					<a href="store.php?logout">Logout</a>
				<?php
				}
				else {
					session_regenerate_id();
					echo "Session Expired!";
					$error_flag = true;				
				}
				?>
			</div>
			<div id="cart">
				<b>Cart</b><br><br>
				<?php
				$query = "SELECT * FROM cart INNER JOIN products ON cart.id_item=products.id WHERE sid='" .$_SESSION['username']."'";
				$res = mysql_query($query,$link) or die(mysql_error());
				if(mysql_num_rows($res) == 0) {
					echo "The cart is empty"; 
				}
				else {
					$t = 0;
					while($record = mysql_fetch_assoc($res)) {?>
						<table>
						<tr>
						<tr><?php print $record['item']. " [" .$record['quantity']. "]";?></tr>		
						</tr>
						</table>
				<?php
					$t += $record['prize']*$record['quantity'];
					}?>
					<br><b><i>Total:</i></b>
				<?php
					print " " .$t;?>&euro;<br>
					<br><hr><br>
					<b><i><a href="showcart.php">Show Cart</a></i></b>
				<?php
				}	
				?>	
			</div>
		</div>
		<div id="navigation">
                        <div id="pagenav">
			<?php 
                        if($error_flag == true) { ?>
				<a href="../index.php">Go back to the Login page</a>
			<?php 
			}
			else { ?>
				<b>Store</b><br><br>
				<table>
				<tr>	
				<td><b><i>Product</i></b></td>
				<td><b><i>Price</i></b></td>
				<td><b><i>Quantity</i></b></td>
				<td><b><i>Select</i></b></td>
				</tr>
				</table>
			<?php	
				/* Database query for the products */
				$query = "SELECT * FROM products";
				$products = mysql_query($query);

				if (!$products) {
  					die("Query error  $query: " . mysql_error());
				}
				
				while($product = mysql_fetch_array($products) ) { ?>	
				<table>
				<tr>
				<td><?php print $product['item'];?></td>
				<td><?php print $product['prize'];?> &euro;</td>
				<td>
				<form action="addcart.php" method="post">
				<input type="text" name="quantity[<?php print $product['id'];?>]" value="1" size="3">
				<input type="hidden" name="itemsid[]" value="<?php print $product['id'];?>">				
				</td>
				<td><input type="checkbox" name="sel[<?php print $product['id'];?>]" value="<?php print $product['item']?>"></td>
				</tr>
				</table>
			<?php
				}
			}
			mysql_close();
                        if($error_flag == false) { ?>
			<br>
			<input type="submit" value="Add to Cart">
			</form>
			<?php } ?>
                        </div>
                </div>
	</div>
<?php

include("footer.html");

?>
