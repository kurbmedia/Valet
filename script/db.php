<?php 

class Db extends Phake{
	
	public $vars;
	public $flags;
	
	private $db_conn;
	private $initialized = false;
	
	
	/**
	 * Create a new databse.
	 *
	 * @return void
	 **/
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
			mysql_query("CREATE DATABASE $db_name", $this->db_conn);
			$db = $this->connect();
			
			$this->output('Creating migrations table..');
			mysql_query("CREATE TABLE `db_migrations` (`id` INT AUTO_INCREMENT, `version` VARCHAR(255) NOT NULL, `description` mediumtext, PRIMARY KEY (id))", $this->db_conn);
						
			$this->output("Finished. Run 'phake db:migrate' to load your schema.");
		}
	}
	
	/**
	 * Help information for DB class.
	 *
	 * @return void
	 **/
	public function help(){
		
	}
	
	/**
	 * Run migrations on the current database.
	 *
	 * @return void
	 **/
	public function migrate(){
		
		require_once(CONFIG_PATH."/migrations/migration.php");
		
		Loader::load('components/inflector');
		$db = $this->connect();
		
		$migrations = glob(VALET_CONFIG_PATH."/migrations/*.php");
		natsort($migrations);
		
		if(!$db) $this->fail('The database: '.PROJECT."_".ENVIRONMENT." does not exist.");

		if(isset($this->flags['v'])){
			
			$fallback = mysql_query("SELECT * FROM `db_migrations` WHERE version = '".$this->flags['v']."'", $this->db_conn);
			
			if(!$fallback){
				$this->fail("Version #".$this->flags['v']." was not found in the list of migrations.");
			}
			
			$fallback = mysql_fetch_assoc($fallback);
			$fallback = intval($fallback['id']);
			$migrations = array_reverse($migrations);
			$order = "DESC";
			
		}else{
			
			$order = "ASC";
			
		}
		
		$existing_migrations = mysql_query("SELECT * FROM `db_migrations` ORDER BY id $order", $this->db_conn);
		
		$completed_migrations = array();
		
		if($existing_migrations){
			while($result = mysql_fetch_assoc($existing_migrations))	$completed_migrations[] = $result['version'];
			mysql_free_result($existing_migrations);
		}
		
		$new_versions 		= array();
		$new_version_desc 	= array();

		
		foreach($migrations as $key => $file){
			if(basename($file) == "migration.php" || empty($file)) continue;			

			$parts   		= explode("_", basename($file));
			$version 		= array_shift($parts);
			$migration_name = str_replace(".php","", implode("_",$parts));
			$class_name		= Inflector::camelize($migration_name);
			
			
			if(isset($fallback)){
				
				if(array_search($version, $completed_migrations) === false) break;
				
				// We are migrating to a specific version.
				require_once($file);
				$class = new $class_name;	

				if($fallback >= intval($key + 1)){

					$this->add_heading();

					$this->output("Rollback migration $key: '".ucwords(str_replace("_", " ", $migration_name))."' \n");					

					$class->down();
					$class->run($this->db_conn);

					mysql_query("DELETE FROM db_migrations WHERE id = '".($key + 1)."'", $this->db_conn);
										
				}else{									
					$this->add_heading();
					$this->output('Migrations complete.');
					break;
				}
				
			}else{
				
				// Run newest migrations
				
				if(array_search($version, $completed_migrations) === false){
					require_once($file);
				
					$this->add_heading();
					$this->output("Running migration: '".ucwords(str_replace("_", " ", $migration_name))."' \n");

					$class = new $class_name;	
					$class->up();
					$class->run($this->db_conn);
					$new_versions[] 	 = $version;
					$new_versions_desc[] = (property_exists($class, 'description'))? $class->description : 'no description';
				}			
			}
			
		}
		
		
		if(count($new_versions) > 0){
			
			$this->add_heading();
			
			$query = array();
			
			for($z = 0; $z < count($new_versions); $z++){
				$query[] = "('','".$new_versions[$z]."', '".$new_versions_desc[$z]."')";
			}
			
			mysql_query("INSERT INTO db_migrations VALUES ".implode(",", $query), $this->db_conn) or die(mysql_error());			
		
			$this->sync();
			$this->output('Migrations complete.');
			return null;
		}
		
		if(!isset($fallback)) $this->output('Up to date. No action necessary.');
	}
	
	/**
	 * Undoes the last migration.
	 *
	 * @return void
	 **/
	public function rollback(){
		$this->connect();
		$fallback = mysql_query("SELECT * FROM `db_migrations` ORDER BY id DESC LIMIT 1", $this->db_conn);
		
		while($result = mysql_fetch_assoc($fallback)) $version = $result['version'];
		$this->flags['v'] = $version;
		$this->migrate();
		$this->sync();
	}
	
	
	/**
	 * Recreate the database and do all migrations.
	 *
	 * @return void
	 **/
	public function reset(){
		$db = $this->connect();
		mysql_query("DELETE FROM db_migrations; ALTER TABLE db_migrations AUTO_INCREMENT = 1", $this->db_conn);
		
		$this->create();
		$this->migrate();
		
	}
	
	
	/**
	 * Syncs the schema file with the current database.
	 *
	 * @return void
	 **/
	public function sync(){
		
		$this->output('Syncing DB Schema in config/shema.ini');
		
		$this->connect();
		
		$file = fopen(VALET_CONFIG_PATH."/schema.ini","wb");
		fwrite($file, ";Database Schema: ".PROJECT."\n");
		fwrite($file, ";Automatically generated with 'phake db:sync' DO NOT MODIFY.\n\n\n");
		
		$result = mysql_query('SHOW TABLES', $this->db_conn);
		$tables = array();
		
		while($table = mysql_fetch_array($result)){
			$tables[] = $table[0];
		}
				
		foreach($tables as $k =>$v){
			
			if($v == "db_migrations") continue;
			
			$columns = mysql_query("DESCRIBE $v", $this->db_conn);
			
			fwrite($file, "[$v]\n");
			
			while($column = mysql_fetch_assoc($columns)){
				fwrite($file, "\t".$column['Field']." = \"".$column['Type']."\"\n");
			}
			
			fwrite($file, "\n\n");
		}
		
		fclose($file);
		
		$this->output('Sync complete.');
		
	}
	
	
	/**
	 * Output all processed migrations
	 *
	 * @return void
	 **/
	public function versions(){
		$this->connect();
		$migrations = mysql_query("SELECT * FROM `db_migrations` ORDER BY id DESC", $this->db_conn);
		
		if(!$migrations) $this->fail('No migrations found.');
		
		while($result = mysql_fetch_array($migrations)){
			$this->add_heading();
			$this->output('Version: '.$result['version']);
			$this->output("     ".$result['description']);
		}
		
		$this->add_heading();
	}
	
	
	// Private	
	
	/**
	 * Connect to the selected database.
	 *
	 * @return void
	 **/
	private function connect(){
		if(!$this->initialized) $this->init();
		return mysql_select_db(PROJECT."_".ENVIRONMENT, $this->db_conn);
	}
	
	/**
	 * Initialize database connection.
	 *
	 * @return void
	 **/
	private function init(){		
		$data = parse_ini_file(VALET_BASE_PATH."/config/environments.ini", true);
		
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