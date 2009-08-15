<?php


class ResourceManager{
	
	/**
	 * Holds currently available models (System-wide).
	 *
	 * @var array
	 **/
	private static $_models = array();
	
	/**
	 * Holds currently available controllers. (Based on current route)
	 *
	 * @var string
	 **/
	private static $_controllers = array();
	
	/**
	 * Holds currently available helpers. (Based on current controller)
	 *
	 * @var string
	 **/
	private static $_helpers = array();
	
	/**
	 * Gets all available controllers for route.
	 *
	 * @return array
	 **/
	public static function get_controller($route){
	
		
	}
	
	/**
	 * Gets all helpers available to the current controller.
	 *
	 * @return array
	 **/
	public static function get_helpers($controller_path = null){
		
		if(!empty(self::$_helpers)) return self::$_helpers;
		
		$helpers  = array();
		$defaults = glob(VALET_ROOT."/core/view/helpers/*.php");
		
		// Get all of the defaults.
		
		foreach($defaults as $file){
			
			$class_name = Inflector::camelize(str_replace(".php","",basename($file)));
			$helpers[$file] = $class_name;
		}
		
		// Get the controller helpers.
		
		if(isset($controller_path)){
			
			if(isset($plugins['configure']) && !empty($plugins['configure'])){
				
				$paths = array();
				foreach($plugins['configure'] as $plugin) $paths[] = VALET_PLUGIN_PATH."/$plugin/app/helpers/";
				array_unshift($paths, VALET_APPLICATION_PATH."/helpers/");
				
				foreach($paths as $path){

					if(!is_dir($path)) continue;
					
					$file = $path.$controller_path."_helper.php";
					
					if(file_exists($file)){
						$helpers[$file] =  self::get_class_name($file);
						break;
					}

				}
			}
		}
		
		$helpers[VALET_APPLICATION_PATH."/helpers/application_helper.php"] =  "ApplicationHelper";
		
		self::$_helpers = $helpers;
		return $helpers;
		
	}
	
	/**
	 * Get all available models.
	 *
	 * @return void
	 **/
	public static function get_models(){
		
		if(!empty(self::$_models)) return self::$_models;
		
		$plugins = Configure::read('plugins');
		$models  = array();
		
		if(isset($plugins['configure']) && !empty($plugins['configure'])){

			foreach($plugins['configure'] as $plugin){
				
				$dir = VALET_PLUGIN_PATH."/$plugin/app/models/";
				if(!is_dir($dir)) continue;
				
				$model_list = glob($dir."*.php");
				foreach($model_list as $file) $models[$file] = self::get_class_name($file);
				
			}
		}
		
		$model_list = glob(VALET_APPLICATION_PATH."/models/*.php");
		foreach($model_list as $file) $models[$file] = self::get_class_name($file);
		
		self::$_models = $models;
		return $models;
		
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	private static function get_class_name($file){
		return Inflector::camelize(str_replace(".php","",basename($file)));
	}
	
	
	
}

?>