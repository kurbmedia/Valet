<?php


abstract class Controller{
	
	/**
	 * Stores any vars set in the controllers not already predefined
	 *
	 * @var array
	 * @access protected
	 **/
	protected $action_vars;
	
	
	/**
	 * Functions to be called before processing of actions.
	 *
	 * @var mixed
	 * @access protected
	 **/
	protected $before_filter;
	
	/**
	 * Functions to be called before processing of actions, and then again after processing of actions.
	 *
	 * @var mixed
	 * @access protected
	 **/
	protected $around_filter;
	
	/**
	 * Functions to be called after processing of actions.
	 *
	 * @var mixed
	 * @access protected
	 **/
	protected $after_filter;
	
	/**
	 * The current server request type. In this instance GET, POST, or XHR (Ajax requests)
	 *
	 * @var string
	 * @access protected 
	 **/
	protected $request;

	
	/**
	 * Passthrough to override the default view.
	 *
	 * @return void
	 * @access private
	 **/
	private function render($file){
		View::set_view($file);
	}
	
	
	/**
	 * Default variable setter.
	 *
	 * @return void
	 * @access public	
	 **/
	public function __set($key, $val){

		switch($key){
			
			// Specify a layout			
			case "layout":
				View::set_layout($val); 
			break;
					
			// Push anything else into the var stack
			default:
				$this->action_vars[$key] = $val;
			break;
		}	
	}
	
	/**
	 * Default variable getter.
	 *
	 * @return mixed
	 * @access public	
	 **/	
	public function __get($key){
		return $this->action_vars[$key]; break;
	}
	
	/**
	 * Functions as a __constructor (allows controller to set its own constructors).
	 *
	 * @return void
	 * @access protected	
	 **/	
	public final function build_controller(){
		$this->action_vars = array();
		
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
			$this->request = "xhr";
		}else{
			$this->request = (isset($_POST) && !empty($_POST))? "post" : "get";
		}
		
		$this->before_filter = (!is_array($this->before_filter))? array($this->before_filter) : $this->before_filter;
		foreach($this->before_filter as $filter) call_user_func(array($this, $filter));

		$this->around_filter = (!is_array($this->around_filter))? array($this->around_filter) : $this->around_filter;
		foreach($this->around_filter as $filter) call_user_func(array($this, $filter));
	}
	
	/**
	 * Functions as a __destructor (allows controller to set its own destructors).
	 *
	 * @return void
	 * @access protected	
	 **/
	public final function destroy_controller(){

		$this->around_filter = (!is_array($this->around_filter))? array($this->around_filter) : $this->around_filter;
		foreach($this->around_filter as $filter) call_user_func(array($this, $filter));
	
		$this->after_filter = (!is_array($this->after_filter))? array($this->after_filter) : $this->after_filter;
		foreach($this->after_filter as $filter) call_user_func(array($this, $filter));
	}
	
	
	
}

?>