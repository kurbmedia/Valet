<?php 

class Generate extends Phake{
	
	public $vars;
	public $flags;
	
	public function model(){
		
		Loader::load('components/inflector');
		$file_name  = $this->vars[0];
		$model_name = Inflector::camelize($file_name);
		
		$file 	= APPLICATION_PATH."/models/".$file_name.".php";
		$stream = fopen($file, "w");

		$file_data  = "<?php \n\n";
		$file_data .= "class $model_name extends ActiveRecord{ \n\n";
		$file_data .= "}\n\n";
		$file_data .= "?>";		
		
		fwrite($stream,$file_data);
		fclose($stream);		
		$this->output("Created model: $model_name.");
		
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
	
	/**
	 * Help information for DB class.
	 *
	 * @return void
	 **/
	public function help(){
		
	}
	
	
	
} 

?>