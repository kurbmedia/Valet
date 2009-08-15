<?php


class ViewFile{
	
	/**
	 * Holds the current page content.
	 *
	 * @var string
	 **/
	private $_page_content;
	
	/**
	 * Holds the final template content.
	 *
	 * @var string
	 **/
	private $_template_content;
	
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
	 * Construct our page.
	 *
	 * @return void
	 **/
	public function __construct($path, &$vars, &$helper = array()){

		$this->_vars 	= $vars;
		$this->_helper 	= $helper;
		
		$file = $path.".phtml";
		
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
		
		$this->_template_content = $result;
					
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
		
		foreach($this->_helper as $helper){
			
			if(is_callable(array($helper, $name))){
				return call_user_func_array(array($helper, $name), $args);
			}
		}	
			
		throw new Error("Invalid helper method '$name'");
	}
	
	
	/**
	 * Returns view variables.
	 *
	 * @return mixed
	 **/
	public function __get($k){

		if(isset($this->_vars[$k])){
			return $this->_vars[$k];
		}
		
		$config_var = Configure::read($k);
		
		if(isset($config_var)){
			return $config_var;
		}
		
		throw new Error("Undefined variable '$k'");
		
	}
	
	/**
	 * Allows object to be used as a string.
	 *
	 * @return void
	 **/
	public function __tostring(){
		return $this->_template_content;
	}
	
	
}


?>