<?php

ini_set('display_errors', true);

define("VALET_BASE_PATH", 	   		dirname(dirname(__FILE__)));		// Do not change.
define("VALET_APPLICATION_PATH", 	VALET_BASE_PATH."/app");		// Points to the application directory
define("VALET_CORE_PATH", 	   		VALET_BASE_PATH."/core");		// Points to the core directory
define("VALET_CONFIG_PATH",	   		VALET_BASE_PATH."/config");	// Points to configuration files
define("VALET_WEBROOT", 		  	VALET_BASE_PATH."/public");	// Points to the site root
define("VALET_VIEW_PATH",  			VALET_APPLICATION_PATH."/views");	// Points to configuration files

require_once(VALET_CORE_PATH.'/application.php');


?>