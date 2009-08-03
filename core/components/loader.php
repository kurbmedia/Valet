<?php 


class Loader{
	
	public static function load($obj){
		
		$items = explode("/", $obj);
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
 

?>