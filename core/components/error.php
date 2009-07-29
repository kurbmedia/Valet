<?php

class Error extends Exception{

	static function handle($e){
		if(Environment::get() != 'production'){					
			$front = ini_get('error_prepend_string');
			$back  = ini_get('error_append_string');
			$file = str_replace(BASE_PATH."/lib/","",$e->file);
			echo($front."<br><b>Application Error:</b> ".$e->message." &nbsp;&nbsp;[ <b>".$file."</b>, line <b>".$e->line."</b> ]".$back);
		}else{
			@touch(BASE_PATH."/log/application.log");
			@chmod(BASE_PATH."/log/application.log",0777);
			error_log( "Application Error [".date('m/d/Y h:i:s a')."]: ".$e->message." [ ".$e->file.", line ".$e->line." ]\n",3,APPLICATION_PATH."/log/application.log");	
		}
	}
	
}


?>