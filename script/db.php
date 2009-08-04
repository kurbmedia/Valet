<?php 

class Db extends Phake{
	
	public $vars;
	public $flags;
	
	private $db_conn;
	private $initialized = false;
	
	public function create(){
		$db_name = PROJECT."_".ENVIRONMENT;
		$result = $this->input("Create database $db_name? [Y,n]");

		if($result == "Y"){
			$this->init();
			
			if($this->connect()){
				$check = $this->input("Database '$db_name' already exists. Drop and remake? [Y,n]");
				if($check !== 'Y') $this->fail('Quitting...');
				mysql_query("DROP DATABASE $db_name");
			}
			
			$this->output("Creating database $db_name.....");			
			mysql_query("CREATE DATABASE $db_name");
			$db = $this->connect();
			
			$this->output('Creating migrations table..');
			mysql_query("CREATE TABLE db_migrations (id INT AUTO_INCREMENT, version VARCHAR(255) NOT NULL, PRIMARY KEY (id))", $this->db_conn);
						
			$this->output("Finished. Run 'phake db:migrate' to load your schema.");
		}
	}
	

	public function migrate(){
		
		Loader::load('components/inflector');
		$db = $this->connect();
		
		if(!$db) $this->fail('The database: '.PROJECT."_".ENVIRONMENT." does not exist.");
		$migrations = mysql_query("SELECT * FROM db_migrations", $this->db_conn);
		
		$completed_migrations = array();
		
		if($migrations){
			while($result = mysql_fetch_assoc($migrations))	$completed_migrations[] = $result['version'];
			mysql_free_result($migrations);
		}
		
		
		$migrations = glob(CONFIG_PATH."/migrations/*.php");
		natsort($migrations);
		
		$new_versions = array();
		
		foreach($migrations as $file){
			if(basename($file) == "migration.php" || empty($file)) continue;			

			$parts   		= explode("_", basename($file));
			$version 		= array_shift($parts);
			$migration_name = str_replace(".php","", implode("_",$parts));
			$class_name		= Inflector::camelize($migration_name);
			
			if(!in_array($version, $completed_migrations)){
				require_once($file);
				
				$this->output("\n________________________________________\n");
				$this->output("Running migration: '".ucwords(str_replace("_", " ", $migration_name))."'");

				$class = new $class_name;
				$class->up();
				
				$new_versions[] = $version;
			}
			
			
		}
		
		
		$this->output("\n________________________________________\n");
		
		if(count($new_versions) > 0){
			mysql_query("INSERT INTO db_migrations VALUES ('','".implode("'),('','", $new_versions)."')", $this->db_conn) or die(mysql_error());			
			$this->output('Migrations complete.');
			return null;
		}
		
		$this->output('Up to date. No action necessary.');
	}
	
	public function reset(){
		$db = $this->connect();
		mysql_query("DELETE FROM db_migrations; ALTER TABLE db_migrations AUTO_INCREMENT = 1", $this->db_conn);
		
		$this->create();
		$this->migrate();
		
	}
	
	
	// Private
	
	private function connect(){
		if(!$this->initialized) $this->init();
		return mysql_select_db(PROJECT."_".ENVIRONMENT, $this->db_conn);
	}
	
	private function init(){		
		$data = parse_ini_file(BASE_PATH."/config/environments.ini", true);
		
		if(!isset($data['database:'.ENVIRONMENT])){
			$this->fail("Unable to find database configuration for environment '$env'");
		}
		
		$db = $data['database:'.ENVIRONMENT];
		$this->db_conn = mysql_connect($db['host'], $db['user'], $db['pass']);
		
		if(!$this->db_conn){
			$this->fail('Unable to connect to mysql server.');
		}
		
		$this->initialized = true;
	}
	
	
	
} 

?>