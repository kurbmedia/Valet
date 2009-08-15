<?php

interface DatabaseAdapter {
	static function get_dbh($host="localhost", $db=null, $user=null, $password=null, $driver="mysql");
	static function query($query, $dbh=null);
	static function quote($string, $dbh=null, $type=null);
	static function last_insert_id($dbh=null, $resource=null);
}


?>