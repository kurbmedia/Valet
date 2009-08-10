<?php 


class Loader{
	
	public static function load($obj){
		
		$items = explode("/", $obj);
		
		if(count($items) == 1){
			
			if(!class_exists('Inflector') self::load('components/inflector');
			$file_name = Inflector::underscore($obj);
			
			$dirs = array(APPLICATION_PATH, CORE_PATH, CORE_PATH."/components");
			foreach($dirs as $dir){
				if(file_exists($dir."/".$file_name.".php")){
					require_once($dir."/".$file_name.".php");
					return null;
				}
			}
			
			throw new Error("The class '$obj' could not be found.");
			
		}else{
			
			$location = array_shift($items);
			$base_dir = "";

			if($location == "components"){
				$base_dir = CORE_PATH."/".strtolower($location);
			}else{
				$base_dir = APPLICATION_PATH."/".strtolower($location);
			}

			require_once($base_dir."/".strtolower(implode("/", $items)).".php");			
			
		}
	}
	
}
 

?>