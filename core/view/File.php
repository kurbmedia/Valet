<?php

namespace View;

use Components;

class File{

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
	 * References the current page helpers.
	 *
	 * @var string
	 **/
	private $_helpers;

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
	public function __construct($file, &$vars, &$helpers = array()){

		$this->_vars = $vars;		
		$this->_helpers = $helpers;
		
		extract($this->_vars,EXTR_SKIP);

		ob_start();
			include($file);
		$this->_page_content = ob_get_clean();	

		ob_start();
			include("layouts/".Components\Registry::instance()->layout.".phtml");
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
		return call_user_func_array(array($this->_helpers[$name], $name), $args);
	}


	/**
	 * Returns view variables.
	 *
	 * @return mixed
	 **/
	public function __get($k){
		
		if($k == "flash") return \Controller\Flash::instance();

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