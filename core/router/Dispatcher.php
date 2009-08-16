<?php

namespace Router;

use Components;

class Dispatcher {
	
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
	public function __construct(){
		
		require_once('authenticator.php');
		require_once('route.php');
		require_once('mapper.php');
		
		include_once(VALET_ROOT.'/config/routes.php');
	}

	/**
	 * Get the instance
	 *
	 * @return void
	 **/
	public static function instance(){
		if(!isset(self::$_instance) || !self::$_instance instanceof Dispatcher) self::$_instance = new Dispatcher();
		return self::$_instance;
	}
		
	/**
	 * Routes our path.
	 *
	 * @return void
	 **/
	public function dispatch() {
		
		$url   = explode('?',$_SERVER['REQUEST_URI']);
		$path = strtolower($url[0]);

		$path = trim($path, '/\\');

		$authenticator = Authenticator::instance();
		$authenticator->validate($path);
		
		$mapper	= Mapper::instance();
		$route  = $mapper->find_route($path);
		
		if(isset($route)){	
			$this->_connect($route->controller, $route->action, $route->params);
		}else{
			$this->_default($path);
		}
		
	}
	
	/**
	 * Connect to the controller and run our action.
	 *
	 * @return void
	 **/
	private function _connect($class_path, $action, $parameters) {

		$parts 		= explode("/", $class_path);
		$controller = array_pop($parts); 
		
		if(!isset($action) || empty($action)) $action = "index";
		
		Components\Registry::instance()->controller = $controller;
		Components\Registry::instance()->action = $action;
		Components\Registry::instance()->view = $controller."/".$action;
				
		$file_path  = $class_path."_controller.php";

		$class = \Inflector::camelize($controller."_controller");
		require_once('application_controller.php');
		
		if($this->_find_controller($file_path)){
			include_once($file_path);
		}else{
			throw new \Error("The controller '$controller' could not be loaded.");
		}
		
		
		$controller = new $class();
		
		if(!method_exists($controller, $action)){
			throw new \Error("Method '$action' does not exist on $class.");
			return;
		}
		
		$controller->params = &$parameters;
		$controller->$action();
		
		\View\Base::instance()->render();
		
	}
	
	
	/**
	 * Find the default route.
	 *
	 * @return void
	 **/
	private function _default($route){
		
		if(empty($route) || $route == "/"){
			$this->_connect("index", "index", array());
			return;
		}
		
		$parts  = explode("/", $route);
		$paths	= array(VALET_ROOT."/app/controllers/");
		$found  = false;
	
		$plugins = Components\Registry::instance()->plugins;
		
		if(isset($plugins) && is_array($plugins)){
			
			foreach($plugins as $plugin){
				
				$plugin_path = VALET_ROOT."/vendor/plugins/".$plugin."/app/controllers";

				if(is_dir($plugin_path)){
					array_push($paths, $plugin_path."/");
				}else{
					throw new \Error("The plugin '$plugin' was not found.");
				}
			}
		}
		
		$class_path = array();
		
		foreach($paths as $path){			

			$namespaced = false;
			
			foreach($parts as $part){
				
				if(is_dir($path.$part)){
					$path 			= $path.$part."/";
					$class_path[]	= $part;
					$namespaced 	= true;
					continue;
				}
				
				if(is_file($path.$part."_controller.php")){
					$path  		  = $path.$part;
					$class_path[] = $part;
					$found 		  = true;
					break;
				}
				
				break;							
			}		

			if($found == true){
				break;
			}else{
				
				if($namespaced == true && file_exists(implode("/", $class_path)."index_controller.php")){
					$found = true;
					$class_path .= "index";
				}else{
					continue;
				}
			}
			
		}		
		
		
		if($found == false){
			throw new \Error("No controller found for the url '$route'");
		}
		
		array_shift($parts);
		$this->_connect(implode("/", $class_path), array_shift($parts), $parts);
		
						
	}
	
	
	private function _find_controller($controller){
		foreach(explode(PATH_SEPARATOR, get_include_path()) as $path){
			if(file_exists($path."/".$controller)) return true;
		}
		
		return false;
	}
	
}

