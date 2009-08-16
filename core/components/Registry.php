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
	 * The current layout
	 *
	 * @var array
	 **/
	public $layout;
	
	/**
	 * The current view
	 *
	 * @var array
	 **/
	public $view;
	
	/**
	 * Holds self
	 *
	 * @var array
	 **/
	private static $_instance;
	
	/**
	 * Holds all of the vars available to the view.
	 *
	 * @var array
	 **/
	private $_view_vars;
	
	
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct(){
		
		$config = \Configure::instance();
		
		$this->plugins = $config->plugins;
		$this->_view_vars = array();
		$this->layout = (isset($config->options['layout']) && !empty($config->options['layout'])) ? $config->options['layout'] : "main";
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
	 * Store variables to be read into the view.
	 *
	 * @return void
	 **/
	public function assign_to_view($name, $value){
		$this->_view_vars[$name] = $value;
	}
	
	/**
	 * Return the view variables
	 *
	 * @return void
	 **/
	public function get_view_vars(){
		return $this->_view_vars;
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