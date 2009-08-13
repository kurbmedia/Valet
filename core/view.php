<?php 


class View{
	
	/**
	 * Stores a reference to the currently used helper.
	 *
	 * @var string
	 **/
	private $_helper;
	
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
	 * Holds the template content for the most recent render.
	 *
	 * @var string
	 **/
	private $_page_content;
	
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
		
			$this->_layout	= $file; 
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
	public static function set_helper($helper){
		
	}
	
	/**
	 * Render the current view.
	 *
	 * @return void
	 **/
	public function render_view($file){
		
		$options = Configure::read('options');
		
		$file = trim($file, '/\\');
		$file = explode("/", $file);
				
		if(count($file) > 1 && $file[0] == "index") $file = array_shift($file);
		
		$file = (is_array($file))? implode("/", $file) : $file;
		$file = VALET_VIEW_PATH."/".$file.".phtml";
		
		if(!file_exists($file) || !is_readable($file)){
			throw new Error("The requested view '".basename($file)."' is not available.", E_NOTICE);
		}
		
		$internals = array();
		
		foreach(glob(VALET_CORE_PATH."/plugins/plugin.*.php") as $plugin) include_once($plugin);
		foreach(glob(VALET_BASE_PATH."/vendor/plugins/plugin.*.php") as $plugin) include_once($plugin);
		
		extract(self::$_vars,EXTR_SKIP);
		
		ob_start();
			include($file);
		$this->_page_content = ob_get_clean();	
		
		ob_start();
			include(VALET_VIEW_PATH."/layouts/".$this->_layout.".phtml");
		$result = ob_get_clean();
		
		print $result;

	}
	
	/**
	 * Releases the current template content.
	 *
	 * @return void
	 **/
	private final function yield(){
		echo($this->_page_content);
	}
	
	/**
	 * Allows passthrough calling of helper methods.
	 *
	 * @return void
	 **/
	public function __call($name, $args){
		$helper = (empty($this->_helper))? "ApplicationHelper" : $this->_helper;
		
		Loader::load($helper);
		
		if(is_callable(array($helper, $name))){
			return call_user_func_array(array($helper, $name), $args);
		}else{
			throw new Error("Invalid helper method '$name'");
		}
	}
	
	
	/**
	 * Returns view variables.
	 *
	 * @return mixed
	 **/
	function __get($k){

		if(isset(self::$_vars[$k])){
			return self::$_vars[$k];
		}
		
		throw new Error("Undefined variable '$k'");
		
	}
	
}
 

?>