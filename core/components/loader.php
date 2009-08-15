<?php 


class Loader{
	
	public static function load($path){
		
		$items  = explode("/", $path);
		$object = Inflector::underscore(array_pop($items));
		
		$path 	= (empty($items))? "" : implode("/", $items)."/";
		$file 	= $path.$object.".php";

		try{
			require_once($file);
		}catch(Exception $e){
			throw new Error("Unable to load '$class'");
		}
		
	}
	
	
}
 

?>