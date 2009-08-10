<?php 


class View{
	
	/**
	 * Stores a reference to the currently used helper.
	 *
	 * @var string
	 **/
	private $_helper;
	
	
	/**
	 * Allows passthrough calling of helper methods.
	 *
	 * @return void
	 **/
	public function __call($name, $args){
		call_user_func_array(array($this->_helper, $name), $args);
	}
	
	
}
 

?>