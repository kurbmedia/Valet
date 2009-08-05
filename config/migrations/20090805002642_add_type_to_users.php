<?php 

class AddTypeToUsers extends Migration{ 

 	public function up(){ 
		$this->add_columns('users',
			array(
				"type" => 'string'
			));
	}

 	public function down(){ 
		
		$this->remove_columns('users', 'type');
		
	}



}?>