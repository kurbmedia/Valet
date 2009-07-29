<?php

$prepend = "<div style=\"padding:0px 10px 10px 10px; border:1px solid #BD4128; ". 
		   "color:#8D2611; background:#F9DBD5; margin-bottom:10px;\">".
		   "<span style=\"font:11px 'Lucida Grande','Lucida Sans Unicode'\">";
$append  = "</span></div>";

ini_set("error_prepend_string",$prepend);
ini_set("error_append_string",$append);


define("BASE_PATH", 	   dirname(dirname(__FILE__)));		// Do not change.
define("APPLICATION_PATH", BASE_PATH."/app/");		// Points to the application directory
define("CORE_PATH", 	   BASE_PATH."/core/");		// Points to the core directory
define("WEBROOT", 		   BASE_PATH."/public/");	// Points to the site root


// Setup autoload

function __autoload($name){
	$name = strtolower($name);
	$final = (strpos($name,'helper') === false)? "" : str_replace('helper','',$name);
	
	if(file_exists(APPLICATION_PATH."/helpers/".$final.".php")){ require_once(APPLICATION_PATH."helpers/".$final.".php"); return; }
	if(file_exists(APPLICATION_PATH."/models/".$name.".php")){ require_once(APPLICATION_PATH."models/".$name.".php"); return; }
	if(file_exists(APPLICATION_PATH."/vendor/".$name.".php")){ require_once(APPLICATION_PATH."models/".$name.".php"); return; }
	
}

require_once(CORE_PATH.'application.class.php');


?>