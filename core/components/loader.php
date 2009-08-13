<?php 


class Loader{
	
	public static function load($obj, $check = false){
		
		$items = explode("/", $obj);
		
		if(count($items) == 1){
			
			if(!class_exists('Inflector')) self::load('components/inflector');
			$file_name = strtolower(Inflector::underscore($obj));
						
			$dirs = array(VALET_APPLICATION_PATH."/helpers", VALET_APPLICATION_PATH."/models", VALET_CORE_PATH, VALET_CORE_PATH."/components");
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
				case "components" : $base_dir = VALET_CORE_PATH."/".strtolower($location); break;
				case "core"		  : $base_dir = VALET_CORE_PATH;
				default: $base_dir = VALET_APPLICATION_PATH."/".strtolower($location); break;
			}
			
			$file = $base_dir."/".strtolower(implode("/", $items)).".php";
			
			if(file_exists($file) && is_readable($file)){
				if($check == true) return true;				
				require_once($file);			
								
			}else{
				if($check == true) return false;				
				throw new Error("Unable to find the object or file: '$file'");
			}			
		}
	}
	
	public static function check($obj){
		return self::load($obj, true);
	}
	
}
 

?>