<?php


abstract class Controller{
	
	private protected var $action_vars = array();
	private protected var $controller_filters = array('before_filter' => array(), 'after_filter' => array(), 'around_filter' => array());

	abstract public function index($args){}
	
	private function render($file){
		View::set_view($file);
	}
	
	public function __set($key, $val){
		
		switch($key){
			
			// Specify a layout			
			case "layout":
				View::set_layout($val); 
			break;
			
			
			// Add controller filters			
			case "before_filter" || "after_filter" || "around_filter":
				if(is_array($val)){
					array_merge($controller_filters[$key], $val);
				}else{
					$controller_filters[$key][] = $val;
				}
			break;
			
			// Push anything else into the var stack
			default: $this->action_vars[$key] = $val; break;
		}	
	}
	
	public function __get($key){
		
		switch($key){
			
			// Get the current request type.
			case "request":
				return ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')? "xhr" : "http";			
			break;
			
			default: return $this->action_vars[$key]; break;
		}
	}
	
	public function __construct(){
		foreach($controller_filters['before_filter'] as $filter) call_user_func(array($this, $filter));
		foreach($controller_filters['around_filter'] as $filter) call_user_func(array($this, $filter));
	}
	
	public function __destruct(){
		foreach($controller_filters['around_filter'] as $filter) call_user_func(array($this, $filter));
		foreach($controller_filters['after_filter'] as $filter) call_user_func(array($this, $filter));
	}
	
	
	
}

?>