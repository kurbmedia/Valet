<?php

class Flash{
	
	static private $_error;
	static private $_warn;
	static private $_notice;
	static private $_format;
	
	static public function error($message){
		if(!is_array(self::$_error)){
			self::$_error = array($message);
		}else{
			self::$_error[] = $message;
		}
		
		$_SESSION['flash']['error'] = self::$_error;
	}
	
	static public function warn($message){
		if(!is_array(self::$_warn)){
			self::$_warn = array($message);
		}else{
			self::$_warn[] = $message;
		}
		
		$_SESSION['flash']['warn'] = self::$_warn;
	}
	
	static public function notice($message){
		if(!is_array(self::$_notice)){
			self::$_notice = array($message);
		}else{
			self::$_notice[] = $message;
		}
		
		$_SESSION['flash']['notice'] = self::$_notice;
	}
	
	static public function getMessages(){

		$messages = array();
		$spl = self::$_format;
		
		if(isset($_SESSION['flash']['notice']) && !empty($_SESSION['flash']['notice'])) $messages['notice'] = implode($spl,$_SESSION['flash']['notice']);
		if(isset($_SESSION['flash']['warn']) && !empty($_SESSION['flash']['warn'])) $messages['warn'] = implode($spl,$_SESSION['flash']['warn']);
		if(isset($_SESSION['flash']['error']) && !empty($_SESSION['flash']['error'])) $messages['error'] = implode($spl,$_SESSION['flash']['error']);
		
		unset($_SESSION['flash']);
		return $messages;
		
	}
	
	static public function clear(){
		if(isset($_SESSION['flash'])) unset($_SESSION['flash']);
	}
	
	static public function setFormat($format){
		self::$_format = $format;
	}
	
}

?>