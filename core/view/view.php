<?php 


class View{

	/**
	 * Identifies the current layout.
	 *
	 * @var string
	 **/
	private static $_layout;
	
	/**
	 * Holds all variables assigned to the view.
	 *
	 * @var array
	 **/
	private static $_vars = array();
	
	/**
	 * Holds the current view file.
	 *
	 * @var string
	 **/
	private static $_current_view;
	
	
	/**
	 * Holds a reference to the config object.
	 *
	 * @var string
	 **/
	private $_config;
	
	/**
	 * View constructor
	 *
	 * @return void
	 **/
	public function __construct(){
		
		$this->_config = Configure::get_instance();
		
		if(empty($this->_layout)){
			$file = $this->_config->options;
			$file = $file['layout'];
		
			if(empty($file)) $file = "main";

			self::$_layout = $file; 
		}
				
	}
	
	/**
	 * Assign variables to the view.
	 *
	 * @return void
	 **/
	public static function assign($k, $v){
		self::$_vars[$k] = $v;
	}
	
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public static function get_layout(){
		return self::$_layout;
	}

	/**
	 * Render the current view.
	 *
	 * @return void
	 **/
	public function render(){
		
		$options = $this->_config->options;
		$path	 = $this->_config->view;
		$plugins = $this->_config->plugins;
		
		$view_paths = array(VALET_APPLICATION_PATH."/views");
		$pass	 = true;
		
		if(isset($plugins['configure']) && is_array($plugins['configure'])){
			
			foreach($plugins['configure'] as $plugin){
				
				$plugin_path = VALET_PLUGIN_PATH."/".$plugin."/app/views";
				if(is_dir($plugin_path)) $view_paths[] = $plugin_path;
			}
			
		}
		
		foreach($view_paths as $check_path){

			if(file_exists($check_path."/".$path.".phtml")){
				$view_file = $check_path."/".$path.".phtml";
				$pass = true;
				break;
			}
		}
		
		if($pass == false){
			$this->throw_404($path.".phtml");
		}
		
		require_once("view_file.php");
		
		$parts 	= explode("/", $path);
		
		array_pop($parts); 		
		
		if($options['caching'] == true){
			
			$compile_dir = VALET_BASE_PATH."/cache/compile/";		
			$cache_file  = md5($path.".phtml")."_".str_replace("/", "_", $path).".php";
			
		}
		
		$controller_path_parts = explode("/", $path);
		array_pop($controller_path_parts);
		
		$helpers = ResourceManager::get_helpers(implode("/", $controller_path_parts));
		$helper_classes = array();
		
		foreach($helpers as $file => $helper_class){
			
			include_once($file);
			
			$helper = new $helper_class();
			
			foreach (get_class_methods($helper) as $method) {
		          if(substr($method, 0, 1) != '_'){
	            	$helper_classes[$method] = $helper;
	            }
	        }
		}	

		$page = new ViewFile($view_file, self::$_vars, $helper_classes);
		print $page;
	

	}
	
	
	/**
	 * View file not found. Throw a 404.
	 *
	 * @return void
	 **/
	private function throw_404($file){
		throw new Error("The requested view '$file' is not available.", E_NOTICE);
	}
	
	
}
 

?>