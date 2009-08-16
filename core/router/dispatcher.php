<?php

namespace Router;

use Components;

require_once('authenticator.php');
require_once('route.php');

class Dispatcher {
	
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
		if(!isset(self::$_instance) || !self::$_instance instanceof Dispatcher) self::$_instance = new Dispatcher;
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
		
		$mapper	= Mapper::get_instance();
		$route  = $mapper->find_route($path);
		
		if(isset($route) && $route instanceof Route){	
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

		$view_path  = array_slice($parts, array_search("controllers", $parts) + 1);
		array_push($view_path, $controller);
		
		$view_path = implode("/", $view_path);
				
		$file_path  = $class_path."_controller.php";

		$class = Inflector::camelize($controller."_controller");

		try{
			require_once($file_path);
		}
		catch(Exception $e){
			throw new Error("The controller '$controller' could not be loaded.");
		}
		
		
		$controller = new $class();
			
		if(!isset($action) || empty($action)) $action = "index";
		
		if(!method_exists($controller, $action)){
			throw new Error("Method '$action' does not exist on $class.");
			return;
		}
		
		$controller->params = &$parameters;
		$controller->build_controller();
		
		$controller->$action();
		$controller->destroy_controller();
		
		$config = Configure::get_instance();
		$config->view = $view_path."/".$action;	
	}
	
	
	/**
	 * Find the default route.
	 *
	 * @return void
	 **/
	private function _default($route){

		$parts  = explode("/", $route);
		$paths	= array(VALET_APPLICATION_PATH."/controllers/");
		$found  = false;
		
		$config  = Configure::get_instance();		
		$plugins = $config->plugins;
		
		if(isset($plugins['configure']) && is_array($plugins['configure'])){
			
			foreach($plugins['configure'] as $plugin){
				
				$plugin_path = VALET_PLUGIN_PATH."/".$plugin."/app/controllers";

				if(is_dir($plugin_path)){
					array_push($paths, $plugin_path."/");
				}else{
					throw new Error("The plugin '$plugin' was not found.");
				}
			}
		}
		
		$class_path = "";
		
		foreach($paths as $path){			

			$namespaced = false;
			
			foreach($parts as $part){

				if(is_dir($path.$part)){
					$path 		= $path.$part."/";
					$namespaced = true;
					continue;
				}
			
				if(is_file($path.$part."_controller.php")){
					$path  = $path.$part;
					$found = true;
					break;
				}
				
				break;							
			}
			
			$class_path = $path;			

			if($found == true){
				break;
			}else{
				
				if($namespaced == true && file_exists($class_path."index_controller.php")){
					$found = true;
					$class_path .= "index";
				}else{
					continue;
				}
			}
			
		}		
		
		
		if($found == false){
			throw new Error("No controller found for the url '$route'");
		}
		
		array_shift($parts);
		$this->_connect($class_path, array_shift($parts), $parts);
		
						
	}
}

