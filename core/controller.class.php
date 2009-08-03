<?php


abstract class Controller{
	
	/**
	 * Stores any vars set in the controllers not already predefined
	 *
	 * @var arrau
	 **/
	protected $action_vars;
	
	
	/**
	 * Stores all filter commands.
	 *
	 * @var array
	 **/
	protected $controller_filters = array('before_filter' => array(), 'after_filter' => array(), 'around_filter' => array());
	
	/**
	 * The current server request type. In this instance GET, POST, or XHR (Ajax requests)
	 *
	 * @var string
	 **/
	protected $request;

	abstract public function index($args);
	
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
			case "before_filter":
			case "after_filter" :
			case "around_filter":
				if(is_array($val)){
					array_merge($controller_filters[$key], $val);
				}else{
					$controller_filters[$key][] = $val;
				}
			break;
			
			// Push anything else into the var stack
			default:
				$this->action_vars[$key] = $val;
			break;
		}	
	}
	
	public function __get($key){
		
		switch($key){
			
			default: return $this->action_vars[$key]; break;
		}
	}
	
	public function __construct(){
		$this->action_vars = array();
		
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
			$this->request = "xhr";
		}else{
			$this->request = (isset($_POST) && !empty($_POST))? "post" : "get";
		}
		
		
		foreach($this->controller_filters['before_filter'] as $filter) call_user_func(array($this, $filter));
		foreach($this->controller_filters['around_filter'] as $filter) call_user_func(array($this, $filter));
	}
	
	public function __destruct(){
		foreach($this->controller_filters['around_filter'] as $filter) call_user_func(array($this, $filter));
		foreach($this->controller_filters['after_filter'] as $filter) call_user_func(array($this, $filter));
	}
	
	
	
}

?>