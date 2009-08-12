<?php

class Cache{
	
	static function cleanup($dir = null){
		// Removes session based files from the cache.
		
		if(!isset($dir)) $dir = BASE_PATH.'/cache/';
		
		$dir = opendir($dir);
		
		while (false !== ($file = readdir($dir))) {
	        if($file != "." && $file != ".."){
	            if(is_dir($file)){
					self::cleanup($file);
				}else{
					if(stristr($file,session_id()) !== false) unlink($file);
				}
	        }
	    }
	
	    closedir($dir);
	}
	
	static function read($fileName){
		
		$fileName = BASE_PATH.'/cache/'.$fileName.'.cache';
		
		if(self::readable($fileName)){
			$file = fopen($fileName,'r');
			$data = fread($file,filesize($fileName));
			fclose($file);
			return unserialize($data);
		}else{
			return null;
		}
	}
	
	static function readable($file){
		return is_readable($file);
	}
	
	static function write($fileName,$data){
		
		$tempFile = BASE_PATH.'/cache/'.$fileName.'.tmp';
		$fileName = BASE_PATH.'/cache/'.$fileName.'.cache';
		
		if(self::writable()){
			$file = fopen($tempFile,"wb");
			fwrite($file,serialize($data));
			fclose($file);
			if(!@rename($tempFile,$fileName)){
				@unlink($fileName);
				@rename($tempFile,$fileName);
			}
			
			@chmod($fileName,0666);
			
		}else{
			throw new Exception('Cache Error: Cache directory: /cache is not writable by the server.');
		}
	}
	
	static function writable(){
		return is_writable(APPLICATION_PATH.'/cache/');
	}
		
}

?>