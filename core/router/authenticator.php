<?php

namespace Router;

class Authenticator{

	private static $_instance;
	private static $_type;

	public static function instance(){
		if(!isset(self::$_instance) || !self::$_instance instanceof Authenticator) self::$_instance = new Authenticator;
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
	}
	
	public static function enable($userType, $attr = null){
		$_SESSION['auth']['type'] = strtolower($userType);
		$_SESSION['auth']['data'] = $attr;
	}
	
	public static function get(){
		
		// Returns the current user type
		
		if(isset($_SESSION['auth']['type'])){
			return $_SESSION['auth']['type'];
		}else{
			return null;
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

		$config  = Configure::get_instance();		
		$data	 = $config->authentication;
		
		if(empty($data)) return null;
		
		
		foreach($data as $url => $values){

			$redirect = (isset($values['on_fail']))? $values['on_fail'] : "/"; 

			if(preg_match('@'.$url.'/?@i', $request)){	// Match the requested url.
				
				if(!isset($values['allow'])) throw new Exception('Invalid user type or user type not set for protected url '.$request);

				$valid_user = $values['allow'];

				if(is_array($valid_user)){
					
					// Access list is an array of user types.
					$pass = false;
					foreach($valid_user as $user) if(self::get() == $user) $pass = true;	// Check list of users for match
					
					if($pass == false){
						if(!preg_match('@'.$request.'/?@i', $redirect)){
							Flash::error('You must be logged in to access this page.');
							Application::redirect($redirect);
							break;
						}
					}
					
				}else{
					
					// Access list is a single user type.
					
					$valid_user = strtolower($valid_user);
					if(self::get() != $valid_user){	// If user isn't allowed, redirect.

						if(!preg_match('@'.$request.'/?@i', $redirect)){
							Flash::error('You must be logged in to access this page.');
							header('HTTP/1.0 401 Unauthorized');
							header("Location:".$redirect);							
							exit();
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