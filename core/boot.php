<?php

if(!defined(VALET_ENV)){
	define('VALET_ENV', isset($_SERVER['VALET_ENV']) ? $_SERVER['VALET_ENV'] : 'development');
}

if(VALET_ENV != "production"){
	error_reporting( E_ALL|E_STRICT );
	ini_set( 'display_errors', true );	
}else{
    error_reporting(0);
	ini_set( 'display_errors', false );
}


define("VALET_ROOT", dirname( dirname( __FILE__ ) ) );

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

require_once('router/dispatcher.php');
require_once('components/inflector.php');
require_once('components/exception.php');

set_include_path( implode( PATH_SEPARATOR, $include_paths ) );
spl_autoload_extensions(".php");

spl_autoload_register();


?>
