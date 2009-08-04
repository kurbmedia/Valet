<?php 

class indexController extends Controller{
	
	public $before_filter = "dothis";
	
	function index($args){
		echo($this->before_filter);
	}
	
} 

?>