<?php 


class Configure{
	
	private static $values = array();
	
	public static function load(){
		$data = parse_ini_file(BASE_PATH."/config/config.ini");
	}
	
	public static function read($val){
		return (isset(self::$values[$val]))? self::$values[$val] : null;
	}
	
	public static function write($key, $value){
		
		$invalid_keys = array("db_config", "base_path", 'environment');
		
		if(in_array($key, $invalid_keys) && isset(self::$values[$key])){
			throw new Error("The configuration option '$key' is used internally and cannot be overwrittern.");
		}
		
		self::$values[$key] = $value;
	}
	
}
 

?>