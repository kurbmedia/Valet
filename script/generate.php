<?php 

class Generate extends Phake{
	
	public $vars;
	public $flags;
	
	public function model(){
		
		$model_name = $this->vars[0];
		
	}
	
	public function migration(){
		
		Loader::load('components/inflector');
		
		$name 	= strtolower(str_replace(" ","_", $this->vars[0]));
		$file 	= CONFIG_PATH."/migrations/".date('YmdHis')."_".$name.".php";
		$stream = fopen($file, "w");
		$class  = Inflector::camelize($name);

		$file_data  = "<?php \n\n";
		$file_data .= "class $class extends Migration{ \n\n \t";
		$file_data .= "public function up(){ \n\n\t";
		$file_data .= "}\n\n \t";
		$file_data .= "public function down(){ \n\n\t";
		$file_data .= "}\n\n";
		$file_data .= "\n\n}";
		$file_data .= "?>";
		
		
		fwrite($stream,$file_data);
		fclose($stream);		
		$this->output("Created migration: $class.");
		
	}
	
	
} 

?>