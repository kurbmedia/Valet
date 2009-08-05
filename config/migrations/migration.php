<?php 

abstract class Migration extends DB{
	
	abstract function up();
	abstract function down();
	
	/**
	 * Holds the final SQL query to be completed
	 *
	 * @var array
	 **/
	public $sql_queries = array();
	
	/**
	 * Add a column to a table
	 *
	 * @return void
	 * @access protected
	 **/
	protected final function add_columns($table, $cols){
		
		$query = array();
		$keys  = array();
		
		foreach($cols as $k =>$v){
			
			if(is_array($v) && isset($v['primary'])) $keys[] = "PRIMARY KEY ($k)";
			$field   = $this->create_field($k, $v);			
			$query[] = $field;
			
			$this->output("     Create column on $table: '$field'");			
		}
		
		$this->sql_queries[] = "ALTER TABLE `$table` ADD " . implode(", ADD ", $query);
		
	}
	
	/**
	 * Generates a table based on properties
	 *
	 * @return void
	 * @access protected
	 **/
	protected final function create_table($name, $cols, $options = null){
		
		$this->output("     -- Create table $name");
		$query = array();
		$keys  = array();
		
		foreach($cols as $k => $v){
			
			if(is_array($v) && isset($v['primary'])) $keys[] = "PRIMARY KEY ($k)";
			$field   = $this->create_field($k, $v);			
			$query[] = $field;
			
			$this->output("          - $field");
		}
		
		if(!empty($keys)) $this->output("          - " . implode(",", $keys));
		
		$this->sql_queries[] = "CREATE TABLE `$name` (" . implode("," , $query) . ", " . implode(",", $keys) . ")";
		

	}
	
	/**
	 * Creates a single field.
	 *
	 * @return string
	 **/
	private function create_field($name, $props, $length = null){
		
		if(is_array($props)){
			
			$field = array();
			
			if(isset($props['length'])){				
				$field[] = $this->create_field($name, $props['type'], $props['length']);
			}else{
				$field[] = $this->create_field($name, $props['type']);
			}
			
			if(isset($props['null']) && $props['null'] == false){
				$field[] = "NOT NULL";
			}else{
				if(isset($props['default'])){
					$field[] = "NULL default '" . $props['default'] . "'";
				}else{
					$field[] = "default NULL";
				}
			}
			
			if(isset($props['options'])) $field[] = $props['options'];

			return implode(" ", $field);
			
		}else{
			
			switch($props){
				case "string":
					$len = (isset($length))? $length : "255";
					return "`$name` VARCHAR($len)"; 
				break;
				
				case "integer":
					$len = (isset($length))? $length : "11";
					return "`$name` INT($len)"; 
				break;
				
				default:
					return "`$name` $props";				
				break;
			}
			
		}
	}
	
	
	
	/**
	 * Drop a table
	 *
	 * @return void
	 * @access protected
	 **/
	protected final function drop_table($table){
		$this->sql_queries[] = "DROP TABLE IF EXISTS `$table`";
	}
	
	/**
	 * Remove a column from a table
	 *
	 * @return void
	 * @access protected
	 **/
	protected final function remove_columns($table, $column){
		if(is_array($column)){
			$query = array();
			foreach($column as $col){
				$this->output("     Remove column from $table: '$col'");
				$query[] = "DROP COLUMN $col";
			}
			
			$query = implode(" ", $query);
			
		}else{
			$this->output("     Remove column from $table: '$column'");
			$query = "DROP COLUMN ".$column;
		}
		
		$this->sql_queries[] = "ALTER TABLE `$table` ". $query;
		
	}
	
	/**
	 * Remove a column from a table
	 *
	 * @return void
	 * @access public
	 **/
	public final function run($db_conn){

	 	foreach($this->sql_queries as $q){
			$result = mysql_query($q);
			if(!$result) $this->fail('Completed migration with errors: '.mysql_error());
		}
		
		$this->output('');
		$this->output('Database transaction complete.');
	}

	
}
 

?>