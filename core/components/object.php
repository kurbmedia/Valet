<?php 

/**
 * Standard Object class.
 *
 * @package valet.core.components
 * @author Brent Kirby
 **/
class Object{

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
	 * Constructor
	 *
	 * @return void
	 **/
	function __construct(){		
		$this->_vars = array();		
	}
	
	/**
	 * Getter
	 *
	 * @return void
	 **/
	function __get($obj){
		return $this->vars[$obj];
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


} // END class 
 

?>