<?php

class Application{
	
	function __construct(){

		// Require core classes.
		require_once(CORE_PATH.'controller.class.php');
		require_once(CORE_PATH.'router.class.php');
		require_once(CORE_PATH.'view.class.php');
		require_once(CORE_PATH.'inflector.class.php');
		require_once(CORE_PATH.'components/error.php');
		require_once(CORE_PATH.'components/environment.php');
		require_once(CORE_PATH.'components/cache.php');
		
		// Requre helper classes
		require_once(APPLICATION_PATH."controllers/controller_base.php");
		
		// Handle errors
		set_exception_handler(array('Error','handle'));
		
		// Allow short tags
		ini_set('short_open_tag', true);
		
		
		// Get config information
		require_once(CORE_PATH.'configure.class.php');		
		include_once(BASE_PATH.'config/config.php');
		
		// Get environment information
		include_once(BASE_PATH.'config/environments.php');
		include_once(BASE_PATH.'config/routes.php');
		
		Configure::load($config);
		Environment::set();
		
		// Handle errors
		set_exception_handler(array('Error','handle'));
		if(Environment::get() != "production"){
			error_reporting(E_ALL);
			ini_set('display_errors', true);
		}else{
			ini_set('display_errors', false);
		}
		
	}
	
	
	function enable($options){
		$options = explode(",", $options);
		
		// Load enabled options
		if(in_array('auth', $options)){	
			require_once(CORE_PATH.'components/auth.php'); 
			require_once(CORE_PATH.'components/flash.php');
		}
		
		if(in_array('flash', $options))	require_once(CORE_PATH.'components/flash.php');
		
		if(in_array('db', $options)){
			
			require_once(CORE_PATH.'activerecord/activerecord.class.php');
			require_once(CORE_PATH.'activerecord/activerecordbase.class.php');
			require_once(CORE_PATH.'activerecord/activerecordexception.class.php');
			
			$behaviors = array('association','belongsto','hasmany','hasone');
			foreach($behaviors as $behavior) require_once(CORE_PATH.'activerecord/'.$behavior.".php");
			
			$db = Environment::$db;
			$adapter = $db['adapter'];
			define('DB_ADAPTER',$adapter);
			
			require_once CORE_PATH.'db_adapters/databaseadapter.php';
			require_once CORE_PATH.'db_adapters/'.DB_ADAPTER.'.php';			
		}		
	}
	
	function redirect($uri){
		header("Location:$uri");
		exit();
	}
	
	function run(){
	
	}
	
	
}

?>