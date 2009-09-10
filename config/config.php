<?php

$config = Configure::instance();

$config->project = array(
	'name'			=> 'Valet',
	'description'	=> 'Valet framework sample site.'	
);


$config->options = array(
	'layout'		=> 'main',
	'caching'		=> false
);


$config->authentication = array(	
	'admin/?(.*)?'	=> array('allow' => 'admin', 'on_fail' => '/')	
);


$config->plugins = array();


?>
