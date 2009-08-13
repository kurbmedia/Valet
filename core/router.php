<?php 


class Router{

	/**
	 * Namespace for found controller class.
	 *
	 * @var string
	 * @access private
	 **/
	private $_namespace;
	
	/**
	 * Selected controller action
	 *
	 * @var string
	 * @access private
	 **/
	private $_action;
	
	/**
	 * Found controller class name
	 *
	 * @var string
	 * @access private
	 **/
	private $_controller;
	
	/**
	 * Stores arguments to be passed to the controller action.
	 *
	 * @var array
	 **/
	private $_args;

	/**
	 * Process the incoming request url.
	 *
	 * @return void
	 **/
	public function process_request(){
		
		$this->_namespace = array();
		
		$route = $_SERVER['REQUEST_URI'];
		
		$all = explode('/',$route);
		$env = explode('/',Configure::read('base_path'));
	
		$result = array_diff($all,$env);
		$route = implode("/",$result);
		
		$route = trim($route, '/\\');	
		
		// Check against user created routes
		if($this->_process_routes($route)){
			$this->_connect();
			return null;
		}
		
		// Get separate parts
		$parts = explode('/', $route);
		
		$cmd_path  = VALET_APPLICATION_PATH."/controllers/";
	
		foreach ($parts as $part) {
			$fullpath = $cmd_path . $part;
			
			// Check for directory
			if(is_dir($fullpath)){
				$cmd_path .= $part . "/";
				$this->_namespace[] = $part;
				array_shift($parts);
				continue;
			}

			// Find the file
			if (is_file($fullpath . '.php')) {
				$this->_controller = $part;
				array_shift($parts);
				break;
			}
			
		}

		if(empty($this->_controller)){
			$this->_controller = "index";
		}
		
		// Get action
		$action = array_shift($parts);
		if(empty($action)) $action = 'index';

		$this->_action 	  = $action;
		$this->_args   	  = $parts;
		$this->_namespace = (empty($this->_namespace))? "" : implode("/", $this->_namespace);
		
	}
	
	/**
	 * Connect to controller and run action.
	 *
	 * @return void
	 * @access private
	 **/
	private function _connect(){
		
		Configure::write('current_controller', $this->_controller);
		Configure::write('current_action', $this->_action);
		
		$file_name = strtolower($this->_controller);
		
		Loader::load('controllers/'.$this->_namespace."/".$file_name);
		
		$ns = (empty($this->_namespace))? "/" : $this->_namespace."/";
		
		if(Loader::check('helpers/'.$ns.$this->_controller."_helper")){
			Loader::load('helpers/'.$ns.$this->_controller."_helper");
			View::set_helper(Inflector::camelize($this->_controller."_helper"));
		}

		$class_name = Inflector::camelize($this->_controller)."Controller";
		$class = new $class_name();
		$class->build_controller();
		
		$action = $this->_action;
		if(method_exists($class, $action)){
			$class->$action($this->_args);
		}else{
			throw new Error("You appear to be missing the action '$action' in your controller.");
		}
		
		$class->destroy_controller();
		
		return $this->_namespace."/".$this->_controller."/".$this->_action;
		
	}
	
	
	/**
	 * Process user created routes
	 *
	 * @return boolean
	 * @access private
	 **/
	function _process_routes($full_route){
		
		include_once(VALET_BASE_PATH."/config/routes.php");
		
		foreach($routes as $route){
			if(preg_match('@'.$route[0]."/?(.*)$@i", $full_route, $matches)){
				$this->_controller = $route['controller'];
				$this->_action	   = $route['action'];
				$this->_args = explode("/",$matches[1]);
				return true;
			}
		}
		
		return false;
	}
	
}
 

?>