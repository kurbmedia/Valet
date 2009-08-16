<?php

namespace Components;

class Exception extends \Exception{
	
	/**
	 * 
	 *
	 * @return void
	 **/
	protected function output($error, $obj){
		if(VALET_ENV == "production"){
			@touch(VALET_ROOT."/log/application.log");
			@chmod(VALET_ROOT."/log/application.log",0777);
			error_log( $error['title']." [".date('m/d/Y h:i:s a')."]: ".$obj->message." [ ".$obj->file.", line ".$obj->line." ]\n",3, VALET_ROOT."/log/application.log");
		}else{
			$message = $this->get_html($error, $obj);
			parent::__construct($message);
		}
	}
	
	
	/**
	 * Get HTML output to return to the browser.
	 *
	 * @return void
	 **/
	private final function get_html(){
		private static function dump_html($error_message, $error_object){
			
			extract($error_message);
			
			$trace = "<div class='trace'><h4>Stack Trace:</h4>".str_replace("\n", "<br>", $error_object->getTraceAsString()).'</div>';
			
			echo <<<STR
				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
					<title>$title</title>
					<style type="text/css">
						body{ font:12px 'Lucida Grande', 'Lucida Sans Unicode', sans-serif; }
						h2, div.detail{ padding:10px; background:#efefef; line-height:25px}
						div.trace{ padding:10px}
						div.error{ background:#F9DBD5; color:#550000; padding:10px; margin-bottom:10px }
					</style>
				</head>
				<body>
					<h2>Application Error: $file</h2>
					$message
					$trace
				</body>
				</html>

STR;
		}
	}
	
}


?>