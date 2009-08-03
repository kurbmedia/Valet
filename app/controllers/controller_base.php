<?php


abstract class ControllerBase extends Controller{
	
	/*
	
	Set any default controller methods here, these will be accessible to all controllers.
	If you need to override construct or destruct make sure you call parent::__method within it.
	
	Controller Filters:
		Filters can be added by creating a variable instance:
			$before_filter = "function_name"  or  $before_filter = array('function_one_name', 'function_two_name')
	 	The available filter types are (and are run in the following order):
			before_filter : called during object __construct
			around_filter : called once during object __construct and again on __destruct
			after_filter  : called during object __destruct (controllers are destructed immediately following their action calls)
	*/
	
	function index($args){}
	
	
}


?>