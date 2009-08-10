<?php 


class Loader{
	
	public static function load($obj){
		
		$items = explode("/", $obj);
		
		if(count($items) == 1){
			
			if(!class_exists('Inflector')) self::load('components/inflector');
			$file_name = strtolower(Inflector::underscore($obj));
						
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

			switch($location){
				case "components" : $base_dir = CORE_PATH."/".strtolower($location); break;
				case "core"		  : $base_dir = CORE_PATH;
				default: $base_dir = APPLICATION_PATH."/".strtolower($location); break;
			}

			require_once($base_dir."/".strtolower(implode("/", $items)).".php");			
			
		}
	}
	
}
 

?>