<?php

require_once("config.php");

class Sessions{
	
	protected $lifetime;
	private $conn;
	
	function Sessions() 
	{
		$this->lifetime = 60*120;

		session_set_save_handler(
			array(&$this,"open"),
			array(&$this,"close"),
			array(&$this,"read"),
			array(&$this,"write"),
			array(&$this,"destroy"),
			array(&$this,"gc")
		);		
	}
	
	public function open()
	{
		if($this->conn = mysql_connect(HOST,USER,PW)) {	
			$res = mysql_select_db(DB, $this->conn);
			$this->gc();
			return $res;
		}
	}
	
	public function close()
	{
		mysql_close($this->conn);
		return true;
	}
	
	public function read($session_id)
	{
		$time=time();

		$sql="SELECT data FROM sessions WHERE sid = '".$session_id."' AND sexpire > ".$time;

		if($result = mysql_query($sql, $this->conn)){
			if(mysql_num_rows($result)){
				$record = mysql_fetch_assoc($result);
				$_SESSION['username'] = $record['data'];
			}
		}
		return '';
	}
	
	public function write($session_id)
	{
		$time = time()+$this->lifetime;
		
		if(!isset($_SESSION['username']))
			return false;

		$data = $_SESSION['username'];
		$sql = "REPLACE INTO sessions VALUES (\"".$session_id."\",\"".$data."\",\"".$time."\")";

		return mysql_query($sql,$this->conn) or die (mysql_error());
	}
	
	public function destroy($session_id)
	{

		$sql = "DELETE FROM sessions WHERE sid ='".$session_id."'";
		mysql_query($sql,$this->conn) or die (mysql_error());

		return true;
	}
	
	public function gc()
	{
		$time = time();

		if(!mysql_query("LOCK TABLES cart WRITE, sessions WRITE",$this->conn))
			return mysql_error();

		
		$sql = "DELETE FROM sessions WHERE sexpire < ".$time;
		if(!mysql_query($sql,$this->conn)) {
			return mysql_error();
		}

		$sql = "DELETE FROM cart WHERE sid NOT IN (SELECT sdata FROM sessions)";
		if(!mysql_query($sql,$this->conn)) {
			return mysql_error(); 
		}
				
		if(mysql_query("UNLOCK TABLES",$this->conn)) {
			return true;
		}
	}
}
?>
