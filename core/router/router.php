<?php


class Router {
	
	/**
	 * Array of user defined paths.
	 *
	 * @var array
	 **/
	private $_map;
	
	
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function __construct(){
		
		include_once(VALET_CONFIG_PATH."/routes.php");
		$this->_map = &$map;
			
	}
	
	/**
	 * Routes our path.
	 *
	 * @return void
	 **/
	public function route() {
	
		$url   = explode('?',$_SERVER['REQUEST_URI']);
		$route = strtolower($url[0]);
		
		$route = trim($route, '/\\');
		
		$url_array = explode('/', $route);
		
		if($route == "/" || empty($route)){
			$this->_default($this->_map['base']['controller']."/".$this->_map['base']['action']);
			return;
		}
		
		
		if(empty($this->_map['routes'])){
			$this->_default($route);
			return;
		}
		
		$good_route = true;
		
		$action = "index";
		$controller_paths = array();

		$params 	= array();
		
		foreach($this->_map['routes'] as $request =>  $response){
			
			$request_parts = explode("/", $request);
			$counter 	   = 0;
			$params	   	   = array();

			$controller = isset($response['controller'])? $response['controller'] : "";
			
			foreach($request_parts as $part){
				
				$part = strtolower($part);
				
				if($part == ":action"){
					
					if(isset($response['action'])){
						$action = $response['action'];
					}else{
						$action = $url_array[$counter];
					}
				
				}elseif(substr($part, 0,1) == ":"){
					
					$params[substr($part, 1)] = $url_array[$counter];					
				
				}elseif ($part != $url_array[$counter] && str_replace("-","_", $part) != $url_array[$counter]) {

					$controller = (isset($response['controller']))? $response['controller'] : $part;
					$good_route = false;
					
					break;
					
				}else{
					$controller_paths[] = array_shift($request_parts);
				}
				
				$counter++;
			}
			
			$remainder  = array_slice($url_array, $counter);			
			$params 	= array_merge($params, $remainder);
						
		}
		
		if($good_route == true){	
			$this->_connect($controller, $action, $params);
		}else{
			$this->_default($route);
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
		
		if(file_exists($file_path)){
			
			require_once($file_path);
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

			Configure::write('view_path', $view_path."/".$action);			
		
		}else{
			
			throw new Error("The controller '$controller' could not be loaded.");
			
		}
				
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
		
		$plugins = Configure::read('plugins');
		
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

