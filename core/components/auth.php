<?php

class Auth{

	private static $_instance;
	private static $_type;

	public static function instance(){
		if(!isset(self::$_instance) || !self::$_instance instanceof AuthComponent) self::$_instance = new AuthComponent;
		return self::$_instance;
	}
	
	public static function init(){
		if(!isset($_SESSION)) @session_start();
		
		if(isset($_SESSION['auth']) && is_array($_SESSION['auth'])){
			self::enable($_SESSION['auth']['type'], $_SESSION['auth']['data']);
		}	
	}
	
	public static function destroy(){
		if(isset($_SESSION) && !empty($_SESSION)) session_destroy();
	}
	
	public static function disable($userType){
		$userType = strtolower($userType);
		if(isset($_SESSION['auth'])) unset($_SESSION['auth']);
		Configure::write('App.session', null); 
	}
	
	public static function enable($userType, $attr = null){
		$_SESSION['auth']['type'] = strtolower($userType);
		$_SESSION['auth']['data'] = $attr;
		Configure::write('App.session', $_SESSION['auth']);
	}
	
	public static function get(){
		
		// Returns the current user type
		
		if(isset($_SESSION['auth']['type'])){
			return $_SESSION['auth']['type'];
		}else{
			return false;
		}
	}
	

	public static function read($attribute = null){
		
		// Read user attribute from session
		
		if(!isset($_SESSION['auth']['data'])) return null;
		
		if($attribute == null){
			return $_SESSION['auth']['data'];
		}else{
			if(isset($_SESSION['auth']['data'][$attribute])){
				return $_SESSION['auth']['data'][$attribute];
			}else{
				return null;
			}
		}
	}
	
	
	public static function validate($request){
		$data 	 = Configure::read('authenticate');
		if(!isset($data['protect'])) return null;
		
		$urls 	 = $data['protect'];
		$default = (isset($data['redirect']))? $data['redirect'] : "/";
		foreach($urls as $value){
			
			$value 	  = explode(",", $value);
			$redirect = (isset($value[2]))? $value[2] : $default; 
			
			if(preg_match('@'.$value[0].'/?@i', $request)){	// Match the requested url.
				if(!isset($value[1])) throw new Error('Invalid user type or user type not set for protected url '.$value[0]);
				$validUser = $value[1];

				if(is_array($validUser)){
					
					// Access list is an array of user types.
					$pass = false;
					foreach($validUser as $user) if(self::get() == $user) $pass = true;	// Check list of users for match
					
					if($pass == false){
						if(!preg_match('@'.$request.'/?@i', $redirect)){
							Flash::error('You must be logged in to access this page.');
							Application::redirect($redirect);
							break;
						}
					}
					
				}else{
					
					// Access list is a single user type.
					
					$validUser = strtolower($validUser);
					if(self::get() != $validUser){	// If user isn't allowed, redirect.

						if(!preg_match('@'.$request.'/?@i', $redirect)){
							Flash::error('You must be logged in to access this page.');
							Application::redirect($redirect);
							break;
						}
					}
				}
			}
		}
	}
	
	public static function write($attribute, $value = null){
		
		// Write user data to session.
		
		if(!is_array($attribute)){
			$_SESSION['auth']['data'][$attribute] = $value;
		}else{
			foreach($attribute as $k => $v) $_SESSION['auth']['data'][$k] = $v;
		}
	}
	
	
	
}

?>