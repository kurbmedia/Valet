<?php

function render($file){
	
	$file_path = VIEW_PATH."/".$file;
	
	if(!file_exists($file) || !is_readable($file)){
		throw new Error("Unable to render file '$file'");
	}
	
	include_once($file_path);
}


?>