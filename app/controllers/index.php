<?php 

class indexController extends ControllerBase{
	
//	public $before_filter = "";
	
	function index($args){
		$this->assign('var', 'hello');
	}
	
} 

?>