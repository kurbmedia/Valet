<?php 

class indexController extends ControllerBase{
	
//	public $before_filter = "";
	
	function index($args){
		Loader::load('models/user');
		$user = User::find_by_name('brent');
		echo("<br>");
//		$user = User::find('all', array('conditions' => "name = 'brent'"));
		print_r($user);
	}
	
} 

?>