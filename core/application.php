<?php

class Application{
	
	/**
	 * Gather all necessary environment files.
	 *
	 * @return void
	 * @access public
	 **/
	public function __construct(){
		
		Loader::load('components/ResourceManager');
		Loader::load('components/Cache');
		Loader::load('components/Object');

		Loader::load('view/View');
		Loader::load('view/HelperBase');
		
		Loader::load('router/Router');
		
		Loader::load('Controller');
		Loader::load('ControllerBase');		
		
	}

	
	/**
	 * Allows enabling of certain Application functionality.
	 *
	 * @return void
	 * @access public
	 **/
	public function enable($options = "auth,flash,db"){
		
		$options = explode(",", $options);
		
		// Load enabled options
		if(in_array('auth', $options)){	
			Loader::load('components/Auth'); 
			Loader::load('components/Flash');
		}
		
		if(in_array('flash', $options))	Loader::load('components/flash');		
		if(in_array('db', $options)){
			
			Loader::load('activerecord/ActiveRecord');
			Loader::load('activerecord/ActiveRecordException');
			
			$behaviors = array('association','belongsto','hasmany','hasone');
			foreach($behaviors as $behavior) Loader::load('activerecord/'.$behavior);
			
			$config = Configure::get_instance();
			
			$db 	 = $config->db_access;
			$adapter = $db['adapter'];
			
			define('VALET_DB_ADAPTER',$adapter);
			Loader::load('db_adapters/DatabaseAdapter');
			Loader::load('db_adapters/'.VALET_DB_ADAPTER);			
		}		
	}
		
	/**
	 * Passthrough function to redirect to location. Exits application.
	 *
	 * @return void
	 * @access public
	 **/
	public static function redirect($uri){
		header("Location:$uri");
		exit();
	}
	
	/**
	 * Route and run Application
	 *
	 * @return void
	 * @access public
	 **/
	public function run(){
		
		$router = new Router();
		$router->route();
		
		$view = new View();
		$view->render();
	}
	
	
}

?>