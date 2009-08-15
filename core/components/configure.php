<?php 


class Configure{
	
	/**
	 * Holds the instance
	 *
	 * @var object
	 **/
	private static $_instance;
	
	/**
	 * Holds database connection information.
	 *
	 * @var array
	 **/
	public $db_access;
	
	/**
	 * Holds project name and description.
	 *
	 * @var array
	 **/
	public $project;
	
	/**
	 * Application options.
	 *
	 * @var array
	 **/
	public $options;
	
	/**
	 * Authenticated paths.
	 *
	 * @var array
	 **/
	public $authentication;
	
	/**
	 * Current View
	 *
	 * @var array
	 **/
	public $view;
	
	/**
	 * Returns the current instance of configure.
	 *
	 * @return void
	 **/
	public static function get_instance(){
		if(isset(self::$_instance) && !empty(self::$_instance)){
			return self::$_instance;
		}else{
			self::$_instance = new Configure();
		}
		
		return self::$_instance;
	}
	
}
 

?>