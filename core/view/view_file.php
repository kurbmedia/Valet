<?php


class ViewFile{
	
	/**
	 * Holds the current page content.
	 *
	 * @var string
	 **/
	private $_page_content;
	
	/**
	 * References the current page helper.
	 *
	 * @var string
	 **/
	private $_helper;
	
	/**
	 * Holds all of the existing view variables.
	 *
	 * @var array
	 **/
	private $_vars;
	
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct($path, &$vars){

		$this->_vars = $vars;
		
		$parts 	= explode("/", $path);
		
		array_pop($parts); 		
		
		require_once(VALET_APPLICATION_PATH."/helpers/application_helper.php");
		
		if(file_exists(VALET_APPLICATION_PATH."/helpers/".implode("/", $parts)."_helper.php")){		
			$helper = array_pop($parts); 
			$this->_helper = Inflector::camelize($helper."_helper");
			Loader::load("helpers/".implode("/", $parts)."_helper.php");
		}else{
			$helper = "ApplicationHelper";
		}

		$file = VALET_VIEW_PATH."/".$path.".phtml";
		
		if(!file_exists($file) || !is_readable($file)){
			throw new Error("The requested view '".$path.".phtml' is not available.", E_NOTICE);
		}
				
		extract($this->_vars,EXTR_SKIP);
		
		ob_start();
			include($file);
		$this->_page_content = ob_get_clean();	
		
		ob_start();
			include(VALET_VIEW_PATH."/layouts/".View::get_layout().".phtml");
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

		if(isset($this->_vars[$k])){
			return $this->_vars[$k];
		}
		
		throw new Error("Undefined variable '$k'");
		
	}
	
	
}


?>