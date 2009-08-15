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
	 * View constructor
	 *
	 * @return void
	 **/
	public function __construct(){
		
		if(empty($this->_layout)){
			$file = Configure::read('options');
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
		
		$options = Configure::read('options');
		$path	 = Configure::read('view_path');
		$plugins = Configure::read('plugins');
		
		$view_paths = array(VALET_VIEW_PATH);
		$pass	 = true;
		
		if(isset($plugins['configure']) && is_array($plugins['configure'])){
			
			foreach($plugins['configure'] as $plugin){
				
				$plugin_path = VALET_PLUGIN_PATH."/".$plugin."/app/views";
				if(is_dir($plugin_path)) $view_paths[] = $plugin_path;
			}
			
		}
		
		foreach($view_paths as $check_path){
			
			if(file_exists($check_path."/".$path.".phtml")){
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
		
		require_once(VALET_APPLICATION_PATH."/helpers/application_helper.php");
		
		$helpers = array();
		
		if(file_exists(VALET_APPLICATION_PATH."/helpers/".implode("/", $parts)."_helper.php")){		
			$helper = array_pop($parts); 
			$helpers[] = Inflector::camelize($helper."_helper");
			Loader::load("helpers/".implode("/", $parts)."_helper.php");
		}		
		
		
		if(empty($helpers)) $helpers[] = "ApplicationHelper";
		
		if($options['caching'] == true){
			
			$compile_dir = VALET_BASE_PATH."/cache/compile/";		
			$cache_file  = md5($path.".phtml")."_".str_replace("/", "_", $path).".php";
			
		}
			

		$page = new ViewFile($path, self::$_vars, $helpers);
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