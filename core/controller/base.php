<?php

namespace Controller;


abstract class Base{
	

	/**
	 * Builds the controller
	 *
	 * @return void
	 **/
	public final function __construct(){
		$this->_run_filters(array('before','around'));
	}
	
	/**
	 * Destroys the controller
	 *
	 * @return void
	 **/
	public final function __destruct(){
		$this->_run_filters(array('around','after'));
	}
	
	/**
	 * Allows redirection with an optional status code.
	 *
	 * @return void
	 **/
	protected final function redirect_to($url, $status_code = null){
		header("Location:$url");
		exit();		
	}
	
	/**
	 * Allows controllers to act differently based on the current request type.
	 *
	 * @return void
	 **/
	protected final function respond_to($request_type, $func){
		if(is_string($func)){
			call_user_func(array($this, $func));
		}else{
			$func();
		}
	}	
	
	// PRIVATE 
	
	private function _run_filters($type = array()){
		
		foreach($type as $type){
			
			$filter = $type."_filter";
			
			if(property_exists($this, $filter)){
							
				settype($this->$filter, 'array');

				foreach($this->$filter as $func){
					call_user_func(array($this, $func));
				}
			}
		}		
	}	
	
}


?>