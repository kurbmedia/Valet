<?php 


class Configure{
	
	private static $_values = array();
	private static $_invalid_keys = array("db_config", "base_path", 'environment', 'db_schema', 'current_controller', 'current_action', 'project', 'view_path', 'options');
	
	public static function load(){
		$data = parse_ini_file(VALET_CONFIG_PATH."/config.ini", true);
		foreach($data as $key => $val){
			self::$_values[$key] = $val;
		}
		
		$db_schema = parse_ini_file(VALET_CONFIG_PATH."/schema.ini", true);
		self::write('db_schema', $db_schema);
	}
	
	public static function read($val){
		return (isset(self::$_values[$val]))? self::$_values[$val] : null;
	}
	
	public static function write($key, $value){
		
		if(in_array($key, self::$_invalid_keys) && isset(self::$_values[$key])){
			throw new Error("The configuration option '$key' is used internally and cannot be overwritten.");
		}
		
		self::$_values[$key] = $value;
	}
	
}
 

?>