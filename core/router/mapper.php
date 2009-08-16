<?php

namespace Router;


class Mapper{
	
	/**
	 * Holds all available routes.
	 *
	 * @var array
	 **/
	private $_routes;
	
	/**
	 * Reference to self
	 *
	 * @var string
	 **/
	private static $_instance;	
	
	/**
	 * Returns the current instance.
	 *
	 * @return void
	 **/
	public static function instance(){
		if(!isset(self::$_instance) || !self::$_instance instanceof Mapper) self::$_instance = new Mapper();
		return self::$_instance;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function __construct(){
		$this->_routes = array();
	}
	
	
	/**
	 * Add dynamic routes.
	 *
	 * @return void
	 **/
	public function connect($route, $options){
		
		$route = new Route($route, $options);
		array_push($this->_routes, $route);

	}
	
	/**
	 * Find the current route.
	 *
	 * @return object Route
	 **/
	public function find_route($path){
		
		foreach($this->_routes as $route){
			$result = $route->match($path);

			if($result == true){
				return $route;
			}
		}
		
		return null;
				
	}
	
}


?>