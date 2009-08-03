<?php 

class SampleTask extends Phake{
	
	// If you wish to include command line arguments, use the public vars: $vars and $flags
	// These args need to be public so that Phake has access to them.
	
	// All methods MUST be public if you want access to them at the command line.

	/**
	 * Includes all variables set via flag in the command line ( -username=user ).
	 *
	 * @var array
	 * @access public
	 **/
	public $flags;
	
	/**
	 * Includes all variables not set by a flag.
	 *
	 * @var array
	 * @access public
	 **/
	public $vars;
	
	public function hello(){
		$this->output($this->flags['username']);
	}
	
	
}
 

?>