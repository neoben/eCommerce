<?php

require_once("config.php");
require_once("class_session.php");

session_start();

$conn = mysql_connect(HOST,USER,PW);
mysql_select_db(DB, $conn) or die(mysql_error());

$query = "LOCK TABLE cart WRITE";
mysql_query($query,$conn) or die(mysql_error());

$query = "DELETE FROM cart WHERE sid='" .$_SESSION['username']. "'";
//mysql_query($query,$conn);
$result = mysql_query($query);
if(!$result) {
	mysql_query("UNLOCK TABLES");
	mysql_close();
	print mysql_error();
}

$query = "UNLOCK TABLES";
mysql_query($query,$conn) or die(mysql_error());
mysql_close($conn);

header('Location: store.php');

?>

