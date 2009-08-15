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
		require_once(VALET_CORE_PATH."/components/loader.php");
		require_once(VALET_CORE_PATH."/controller.php");
		require_once(VALET_CORE_PATH."/router/router.php");
		require_once(VALET_CORE_PATH."/view/view.php");
		require_once(VALET_CORE_PATH."/view/helper_base.php");
		
		Loader::load('components/resource_manager');
		Loader::load('components/inflector');
		Loader::load('components/environment');
		Loader::load('components/cache');
		Loader::load('components/error');
		Loader::load('components/object');
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
		
		if(Configure::read('autoload_models') == true){

			$models = ResourceManager::get_models();

			foreach($models as $k => $v){
				require_once($k);
			}
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
			
			require_once(VALET_CORE_PATH.'/activerecord/activerecord.php');
			require_once(VALET_CORE_PATH.'/activerecord/activerecordexception.php');
			
			$behaviors = array('association','belongsto','hasmany','hasone');
			foreach($behaviors as $behavior) require_once(VALET_CORE_PATH.'/activerecord/'.$behavior.".php");
			
			$db = Configure::read('db_config');
			$adapter = $db['adapter'];
			define('DB_ADAPTER',$adapter);
			
			require_once VALET_CORE_PATH.'/db_adapters/databaseadapter.php';
			require_once VALET_CORE_PATH.'/db_adapters/'.DB_ADAPTER.'.php';			
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