<?php


abstract class ApplicationController extends Controller\Base{
	
	/*
	
	Set any default controller methods here, these will be accessible to all controllers.
		
	Controller Filters:
		Filters can be added by creating a variable instance:
			$before_filter = "function_name"  or  $before_filter = array('function_one_name', 'function_two_name')
	 	The available filter types are (and are run in the following order):
			before_filter : called during object __construct
			around_filter : called once during object __construct and again on __destruct
			after_filter  : called during object __destruct (controllers are destructed immediately following their action calls)
	*/
	
	
}


?>