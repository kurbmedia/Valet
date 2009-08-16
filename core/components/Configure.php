<?php

class Configure{

	/**
	 * Holds all vars.
	 *
	 * @var array
	 **/
	private $_vars;

	/**
	 * Holds assigned lambda functions (PHP 5.3)
	 *
	 * @var string
	 **/
	private $_functions;
	
	/**
	 * Reference
	 *
	 * @var object
	 **/
	private static $_instance;
	
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function __construct(){		
		$this->_vars = array();
	}
	
	/**
	 * Get the instance
	 *
	 * @return void
	 **/
	public static function instance(){
		if(!isset(self::$_instance) || !self::$_instance instanceof Configure) self::$_instance = new Configure();
		return self::$_instance;
	}

	/**
	 * Getter
	 *
	 * @return void
	 **/
	function __get($obj){
		return $this->_vars[$obj];
	}

	/**
	 * Setter
	 *
	 * @return void
	 **/
	function __set($obj, $val){
		if(gettype($obj) == "unknown type" || gettype($obj) == "function"){
			$this->_functions[$obj] = $val;
			return null;
		}
		
		$this->_vars[$obj] = $val;
	}	
	
}


?>