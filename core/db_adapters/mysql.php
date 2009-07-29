<?php

class MySQLAdapter implements DatabaseAdapter {

	static function get_dbh($host="localhost", $db=null, $user=null, $password=null, $driver="mysql") {
		$dbh = mysql_connect($host, $user, $password);
		if (!$dbh)
			throw new Exception("Could not connect to database server: ".mysql_error());
		if (!mysql_select_db($db, $dbh))
			throw new Exception("Could not select database: " . mysql_error());
		return $dbh;
	}

	static function query($query, $dbh=null) {
		$res = mysql_query($query, $dbh);
		if (!$res) {
			throw new Exception("Error executing query: '$query' : " . mysql_error());
		}
		$rows = array();
		if ($res !== true && mysql_num_rows($res) != 0) {
			while ($row = mysql_fetch_assoc($res))	$rows[] = $row;
			mysql_free_result($res);
		}
		return $rows;
	}

	static function quote($string, $dbh=null, $type=null) {
		return "'" . mysql_real_escape_string($string, $dbh) . "'";
	}

	static function last_insert_id($dbh=null, $resource=null) {
		if (is_null($resource))
			return mysql_insert_id();
		else
			return mysql_insert_id($resource);
	}
	
	static function get_columns($table_name, $dbh=null){
		
		$cache = Cache::read('models/'.$table_name);
		if(!isset($cache)){
		
			$res = mysql_query("DESCRIBE $table_name", $dbh);
			while($results[] = mysql_fetch_assoc($res));

			$cols = array();
			foreach($results as $result) if(!empty($result)) $cols[] = $result['Field'];
			Cache::write('models/'.$table_name,$cols);
			return $cols;
		}else{
			return $cache;
		}
	}

}

?>
