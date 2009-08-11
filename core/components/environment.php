<?php

class Environment{
	
	/**
	 * Holds the current environment's name
	 *
	 * @var string
	 * @access private
	 **/
	private static $_current_environment;
	
	/**
	 * Return the current environment's name
	 *
	 * @return void
	 * @access public
	 **/
	public static function get(){
		return self::$_current_environment;		
	}
	
	/**
	 * Loads all available environments, selects the current and sets it.
	 *
	 * @return void
	 * @access public
	 **/
	public static function load(){
		$data = parse_ini_file(BASE_PATH."/config/environments.ini", true);		
		$server = $_SERVER['SERVER_NAME'];

		foreach($data as $key => $env){
			
			if(strpos($key, ":") !== false) continue;
			
			if(preg_match('@(www\.)?'.$server."$@i", $env['domain'])){
				
				Configure::write('environment', $key);
				Configure::write('base_path', $env['base']);
				unset($data[$key]['base'], $data[$key]['domain']);
				$environment = $key;
				
				$db_config = array();		
				foreach($data["database:".$environment] as $k => $v)	$db_config[$k] = $v;

				Configure::write('db_config', $db_config);
				self::$_current_environment = $environment;
				
				return null;				
			}
		}		
		
		throw new Error("Unable to set environment. No configuration found for domain ". $server, E_NOTICE);
		
		
	}
		
}

?>