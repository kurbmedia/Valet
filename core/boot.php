<?php

ini_set('display_errors', true);


define("BASE_PATH", 	   dirname(dirname(__FILE__)));		// Do not change.
define("APPLICATION_PATH", BASE_PATH."/app");		// Points to the application directory
define("CORE_PATH", 	   BASE_PATH."/core");		// Points to the core directory
define("CONFIG_PATH",	   BASE_PATH."/config");	// Points to configuration files
define("WEBROOT", 		   BASE_PATH."/public");	// Points to the site root


require_once(CORE_PATH.'/application.class.php');


?>