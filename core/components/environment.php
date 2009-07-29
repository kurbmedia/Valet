<?php

class Environment{
	
	private static $_currentEnvironment;
	public static  $db;
	public static  $base;	
	
	public static function get(){
		return self::$_currentEnvironment;		
	}
	
	public static function set(){
		$server = $_SERVER['SERVER_NAME'];
		$env	= Configure::read('environments');
		foreach($env as $k=>$v){			
			if(preg_match('@(www\.)?'.$server."$@i",$env[$k]['domain'])){
				self::$_currentEnvironment = $k;
				self::$base  = (isset($env[$k]['base']))? $env[$k]['base'] : "/";
				self::$db 	 = (isset($env[$k]['database']))? $env[$k]['database'] : null;
				return null;
			}
		}
		
		throw new Error("Unable to set environment. No configuration found for domain ". $server, E_NOTICE);
	}
	
}

?>