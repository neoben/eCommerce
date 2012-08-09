<?php

require_once("modules/config.php");
require_once("modules/class_session.php");

$session_class = new Sessions();
session_start();

if(isset($_SESSION['username'])) {
	header('Location: modules/store.php');
}

$link = mysql_connect(HOST, USER, PW);
if (!$link) {
  die ("Error connecting to the database: " . mysql_error());
}

$db_selected = mysql_select_db(DB, $link);
if (!$db_selected) {
  die ("Error selecting the database: " . mysql_error());
}

/* Database query for the products */
$query = "SELECT * FROM products";
$products = mysql_query($query);

if (!$products) {
  	die("Query error  $query: " . mysql_error());
}

include("modules/header.html");

?>
	<div id="center">
		<?php 

		include("modules/storewindow.php"); 
		include("modules/login.html");		
	
		?>
		
	</div>
<?php

include("modules/footer.html");

?>
