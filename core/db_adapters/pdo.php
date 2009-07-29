<?php

class PDOAdapter implements DatabaseAdapter {

	static function get_dbh($host="localhost", $db="", $user="", $password="", $driver="mysql") {
		$dbh = new PDO("$driver:host=$host;dbname=$db", $user, $password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbh;
	}

	static function query($query, $dbh=null) {
		return $dbh->query($query, PDO::FETCH_ASSOC);
	}

	static function quote($string, $dbh=null, $type=null) {
		return $dbh->quote($string, $type);
	}

	static function last_insert_id($dbh=null, $resource=null) {
		return $dbh->lastInsertId($resource);
	}
	
	static function get_columns($table_name, $dbh=null){
		
		$cache = Cache::read('models/'.$table_name);
		if(!isset($cache)){
			$cols = array();
			$describe = $dbh->query("DESCRIBE $table_name",PDO::FETCH_ASSOC);
			
			foreach($describe as $item) $cols[] = $item['Field'];
						
			Cache::write('models/'.$table_name,$cols);			
			return $cols;			
	
		}else{
			return $cache;
		}
		
		
	}

}

?>
