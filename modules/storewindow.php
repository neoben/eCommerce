<div id="navigation">
			<div id="pagenav">
				<b>Store Window</b><br><br>
				<table>
				<tr>	
				<td><b><i>Product</i></b></td>
				<td><b><i>Price</i></b></td>
				<td><b><i>Availability</i></b></td>
				</tr>
				</table>
				<?php while($product = mysql_fetch_array($products) ) { ?>
				<table>
				<tr>
				<td><?php print $product['item'];?></td>
				<td><?php print $product['prize'];?> &euro;</td>
				<td><?php print $product['number'];?></td>
				</tr>
				</table>
				<?php } 
				mysql_close();
				?>
				<br><br>
				Please <b>Login</b> to access the store or <b>Sign Up</b> if you're not registered.
			</div>
		</div>
