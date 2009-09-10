<?php

namespace Controller;

class Flash{
	
	/**
	 * Holds the class instance
	 *
	 * @var object
	 **/
	private static $_instance;
	
	/**
	 * Holds all current flash messages
	 *
	 * @var array
	 **/
	private $_messages = array();	
	
	/**
	 * Get the instance
	 *
	 * @return void
	 **/
	public static function instance(){
		if(!isset(self::$_instance) || !self::$_instance instanceof Flash) self::$_instance = new Flash();
		return self::$_instance;
	}
	
	/**
	 * Handles adding messages
	 *
	 * @return void
	 **/
	public function __call($name, $args){

		if(!isset($args[0])){
		
			$message = $args[0];
		
			if(isset($this->_messages[$name])){
				$this->_messages[$name][] = $message;
			}else{
				$this->_messages[$name] = array($message);
			}
			
		}else{
			return (isset($this->_messages[$name]))? $this->_messages[$name] :  "";
		}
	}
	
	
	
}


?>