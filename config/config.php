<?php

$config->project = array(
	'name'			=> 'Valet',
	'description'	=> 'Valet sample framework setup.'	
);


$config->options = array(
	'layout'		=> 'main',
	'caching'		=> false
);


$config->authentication = array(	
	'/admin/(.*)'	=> array('allow' => 'user_type', 'on_fail' => 'rediret_here_on_fail')	
);


$config->plugins = array();


?>
