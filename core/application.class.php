<?php

class Application{
	
	/**
	 * Gather all necessary environment files.
	 *
	 * @return void
	 * @access public
	 **/
	public function __construct(){

		// Require core classes.
		require_once(CORE_PATH."/components/loader.php");
		require_once(CORE_PATH.'/controller.class.php');
		require_once(CORE_PATH.'/router.class.php');
		require_once(CORE_PATH.'/view.class.php');
		
		Loader::load('components/inflector');
		Loader::load('components/environment');
		Loader::load('components/cache');
		Loader::load('components/error');
		Loader::load('components/configure');
		Loader::load('controllers/controller_base');
		
		
		// Handle errors
		set_exception_handler(array('Error','handle'));
		
		// Allow short tags
		ini_set('short_open_tag', true);
		
		
		// Load Configuration
		Configure::load();
		
		// Load Environment
		Environment::load();

		
		// Handle errors
		set_exception_handler(array('Error','handle'));
		if(Environment::get() != "production"){
			error_reporting(E_ALL);
			ini_set('display_errors', true);
		}else{
			ini_set('display_errors', false);
		}
		
	}
	
	/**
	 * Allows enabling of certain Application functionality.
	 *
	 * @return void
	 * @access public
	 **/
	public function enable($options){
		$options = explode(",", $options);
		
		// Load enabled options
		if(in_array('auth', $options)){	
			Loader::load('components/auth'); 
			Loader::load('components/flash');
		}
		
		if(in_array('flash', $options))	Loader::load('components/flash');
		
		if(in_array('db', $options)){
			
			require_once(CORE_PATH.'/activerecord/activerecord.class.php');
			require_once(CORE_PATH.'/activerecord/activerecordexception.class.php');
			
			$behaviors = array('association','belongsto','hasmany','hasone');
			foreach($behaviors as $behavior) require_once(CORE_PATH.'/activerecord/'.$behavior.".class.php");
			
			$db = Configure::read('db_config');
			$adapter = $db['adapter'];
			define('DB_ADAPTER',$adapter);
			
			require_once CORE_PATH.'/db_adapters/databaseadapter.php';
			require_once CORE_PATH.'/db_adapters/'.DB_ADAPTER.'.php';			
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
		$router->process_request();
	}
	
	
}

?>