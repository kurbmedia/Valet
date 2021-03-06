<?php
 
class Error extends Exception{
 
	public static function handle($e){
 
		if(VALET_ENV != 'production'){		
			$file = explode(VALET_ROOT, $e->file);
			$file = str_replace(VALET_ROOT."/lib/","",$file[1]);
			$str  = "<div class='error'><b>Application Error:</b> ".$e->message."</div>";
			$str .= "<div class='detail'><b>Thrown in: ".$file."</b>, line <b>".$e->line."</b>";
			$str .= "</div>";			
			$str .= "<div class='trace'><h4>Stack Trace:</h4>".str_replace("\n", "<br>", $e->getTraceAsString()).'</div>';
			self::dump_html($str, $file, $e);			
		}else{
			@touch(VALET_ROOT."/log/application.log");
			@chmod(VALET_ROOT."/log/application.log",0777);
			error_log( "Application Error [".date('m/d/Y h:i:s a')."]: ".$e->message." [ ".$e->file.", line ".$e->line." ]\n",3, VALET_ROOT."/log/application.log");	
		}
	}
 
	private static function dump_html($str, $file, $e){
		echo <<<STR
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
				<title>Application Error</title>
				<style type="text/css">
					body{ font:12px 'Lucida Grande', 'Lucida Sans Unicode', sans-serif; }
					h2, div.detail{ padding:10px; background:#efefef; line-height:25px}
					div.trace{ padding:10px}
					div.error{ background:#F9DBD5; color:#550000; padding:10px; margin-bottom:10px }
				</style>
			</head>
			<body>
				<h2>Application Error: $file</h2>
				$str
			</body>
			</html>
 
STR;
	}
 
}
 
 
?>