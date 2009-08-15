<?php


if(!defined('VALET_ENV')){
	define('VALET_ENV', isset($_SERVER['VALET_ENV'])? $_SERVER['VALET_ENV'] : "development");
}

define("VALET_ROOT",	 	   		dirname(dirname(__FILE__)));		// Do not change.
define("VALET_APPLICATION_PATH",	VALET_ROOT . "/app");				// Points to application
define("VALET_CONFIG_PATH",	   		VALET_ROOT . "/config");			// Points to configuration files
define("VALET_PUBLIC_PATH",		  	VALET_ROOT . "/public");			// Points to the site root

// Set application include paths.

$include_paths = array(
	VALET_ROOT . '/core',
    VALET_ROOT . '/app/controllers',
    VALET_ROOT . '/app/helpers',
    VALET_ROOT . '/app/models',
	VALET_ROOT . '/app/views',
	VALET_ROOT . '/vendor/plugins',
    get_include_path()
);


set_include_path(implode(PATH_SEPARATOR, $include_paths));


// Set error handling.

require_once('components/loader.php');
require_once('components/inflector.php');

Loader::load('components/Error');
set_exception_handler(array('Error','handle'));

if(VALET_ENV != "production"){
	error_reporting(E_ALL |  E_STRICT);
	ini_set('display_errors', true);	
}else{
    error_reporting(0);
	ini_set('display_errors', false);
}

Loader::load('components/Configure');
Loader::load('Application');

// Setup configuration.

$config = Configure::get_instance();

include_once(VALET_CONFIG_PATH."/config.php");

// Setup database access.

$db_access = parse_ini_file(VALET_CONFIG_PATH."/database.ini", true);
$config->db_access = $db_access[VALET_ENV];


?>