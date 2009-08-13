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
			$this->_connect($this->_map['base']['controller'], $this->_map['base']['action'], array());
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
		$controller = (count($parts) > 0)? array_shift($parts) : $class_path;
		
		$file_path  = implode("/", $parts);

		$view_path  = (empty($file_path) || $file_path == "")? $controller : $file_path."/".$controller;
		$file_path  = VALET_APPLICATION_PATH."/controllers/".$controller."_controller.php";

		$class = Inflector::camelize($controller."_controller");
		
		if(file_exists($file_path)){
			
			require_once($file_path);
			$controller = new $class();
			
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
		$path	= VALET_APPLICATION_PATH."/controllers/";
		$found  = true;
		
		foreach($parts as $part){

			if(is_dir($path.$part)){
				$path = $path.$part ."/";
				continue;
			}
			
			if(is_file($path.$part."_controller.php")){
				$path = $path.$part ."/";
				array_shift($parts);
				break;
			}
			
			$found = false;
			
		}
		
		if($found == false){
			throw new Error("No controller found for the url '$route'");
		}
		
		$this->_connect($path, array_shift($parts), $parts);
		
						
	}
}

