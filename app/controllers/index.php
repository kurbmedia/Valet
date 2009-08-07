<?php 

class indexController extends ControllerBase{
	
//	public $before_filter = "";
	
	function index($args){
		Loader::load('models/user');
		$user = User::find('all');
		print_r($user);
	}
	
} 

?>