<?php


namespace View;

use Components;

class Base{
	
	/**
	 * Reference
	 *
	 * @var object
	 **/
	private static $_instance;
	
	/**
	 * Get the instance
	 *
	 * @return void
	 **/
	public static function instance(){
		if(!isset(self::$_instance) || !self::$_instance instanceof Base) self::$_instance = new Base();
		return self::$_instance;
	}
	
	
	/**
	 * Render the current view.
	 *
	 * @return void
	 **/
	public function render(){
		
		$registry = Components\Registry::instance();
		
		$plugins = $registry->plugins;
		$vars	 = $registry->get_view_vars();
		
		$vars['current_controller'] = $registry->controller;
		$vars['current_action']		= $registry->action;
		
		include_once('application_helper.php');
	
		$helper_classes = array();
		$helper_names	= array();

		foreach(glob(VALET_ROOT."/core/libs/helpers/*.php") as $file){
			
			$class = str_replace(".php", "", basename($file));
			$class = \Inflector::camelize($class);

			include_once($file);
			$helper_names[] = $class;
			
		}
		
		$helper_names[] = "ApplicationHelper";
		
		$controller_helper = $registry->helper();
		
		if(@include_once(\Inflector::underscore($controller_helper))){
			$helper_names[] = $controller_helper;
		}
		
		foreach($helper_names as $class){

			$helper = new $class();

			foreach (get_class_methods($helper) as $method) {
				if(substr($method, 0, 1) != '_'){
	            	$helper_classes[$method] = $helper;
	            }
	        }
		}
		
		
		$file = new File($registry->view.".phtml", $vars, $helper_classes);
		print $file;
		
	}
	
	
	
	
}


?>