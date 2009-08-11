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
	protected $before_filter = "";
	
	/**
	 * Functions to be called before processing of actions, and then again after processing of actions.
	 *
	 * @var mixed
	 * @access protected
	 **/
	protected $around_filter = "";
	
	/**
	 * Functions to be called after processing of actions.
	 *
	 * @var mixed
	 * @access protected
	 **/
	protected $after_filter = "";
	
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
	 * undocumented function
	 *
	 * @return void
	 **/
	protected final function assign($k, $v){
		View::assign($k, $v);
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
		
	
		$this->_process_filters( array_merge((array)$this->before_filter, (array)$this->around_filter) );		
		
	}
	
	/**
	 * Functions as a __destructor (allows controller to set its own destructors).
	 *
	 * @return void
	 * @access protected	
	 **/
	public final function destroy_controller(){
		$this->_process_filters( array_merge((array)$this->around_filter, (array)$this->after_filter) );		
	}
	
	
	/**
	 * Process controller filters.
	 *
	 * @return void
	 **/
	private final function _process_filters($filters){
				
		foreach($filters as $filter){
			
			if(empty($filter)) continue;
			
			if(is_array($filter)){
				
				$action = $filter[0];
				
				// Check if we are only supposed to run this filter on this current action.
				
				if(isset($filter['only'])){
					if(is_array($filter['only'])){
						if(in_array($filter['only'], Configure::read('current_action'))) continue;
					}else{
						if($filter['only'] != Configure::read('current_action')) continue;
					}
				}				
				
				// Check if we are running an action thats an exception to our filter rules.
				
				if(isset($filter['except'])){
					if(is_array($filter['except'])){
						if(in_array($filter['except'], Configure::read('current_action'))) continue;
					}else{
						if($filter['except'] == Configure::read('current_action')) continue;
					}
				}
				
			}else{
				$action = $filter;
			}
			
			if(!method_exists($this, $action))	throw new Error("The filter: '$action' does not exist.");		
			$this->$action();
		}
	}
	
	
	
}

?>