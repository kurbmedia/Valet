<?php 

class InitialSetup extends Migration{ 

	public $description  = "Setup initial database schema.";

 	public function up(){ 
		$this->create_table('users',
			array(
				'id' 	=> array('type' => 'integer', 'length' => '100', 'options' => 'AUTO_INCREMENT', 'primary' => true),
				'name' 	=> 'string', 
			)
		);
		
	}

 	public function down(){ 
		$this->drop_table('users');
	}



}?>