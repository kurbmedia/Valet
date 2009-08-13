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
		
		require_once("view_file.php");
		
		$page = new ViewFile($path, self::$_vars);

	}
	
	
}
 

?>