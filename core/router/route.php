<?php

class Route{
	
	/**
	 * The controller belonging to this route.
	 *
	 * @var string
	 **/
	public $controller;
	
	/**
	 * The action belonging to this route.
	 *
	 * @var string
	 **/
	public $action;
	
	/**
	 * Parameters belonging to this route.
	 *
	 * @var array
	 **/
	public $params;
	
	/**
	 * The actual route path.
	 *
	 * @var string
	 **/
	public $_route;	
	
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct($route, $args){
		
		$this->_route 	  = $route;
		$this->controller = isset($args['controller']) ? $args['controller'] : null;
		$this->action	  = isset($args['action']) ? $args['action'] : null;
	}
	
	/**
	 * Matches a url with the route.
	 *
	 * @return boolean
	 **/
	public function match($url){

		$good_route = true;

		$action 		= "index";
		$namesapces		= array();
		$request_parts	= array();

		$params 	= array();
		$url_array	= explode("/", $url);
		
		$request_parts = explode("/", $this->_route);
		$counter 	   = 0;
		$params	   	   = array();

		$controller = isset($this->controller)? $this->controller : "";

		foreach($request_parts as $part){
			$part = strtolower($part);

			if($part == ":action"){
				$this->action = isset($this->action) ? $this->action : $url_array[$counter];

			}elseif(substr($part, 0,1) == ":"){

				$params[substr($part, 1)] = $url_array[$counter];					

			}elseif ($part != $url_array[$counter] && str_replace("-","_", $part) != $url_array[$counter]) {

				$controller = isset($this->controller)? $this->controller : $part;
				$good_route = false;

				break;

			}else{
				$controller_paths[] = array_shift($request_parts);
			}

			$counter++;
		}

		$remainder  	= array_slice($url_array, $counter);			
		$this->params 	= array_merge($params, $remainder);

		return $good_route;
	}
	
}


?>