<?php

namespace Components;

class Registry{
	
	/**
	 * Current Controller
	 *
	 * @var string
	 **/
	public $controller;
	
	/**
	 * Current Action
	 *
	 * @var string
	 **/
	public $action;
	
	/**
	 * All active plugins
	 *
	 * @var array
	 **/
	public $plugins;
	
	/**
	 * Holds self
	 *
	 * @var array
	 **/
	private static $_instance;
	
	
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct(){
		$this->plugins = \Configure::instance()->plugins;
	}
	
	/**
	 * Get the instance
	 *
	 * @return void
	 **/
	public static function instance(){
		if(!isset(self::$_instance) || !self::$_instance instanceof Registry) self::$_instance = new Registry;
		return self::$_instance;
	}
	
	/**
	 * Returns the current helper for the action.
	 *
	 * @return void
	 **/
	public function helper(){
		return $this->controller."Helper";
	}
	
}


?>