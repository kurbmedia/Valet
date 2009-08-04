<?php 

abstract class Migration extends Phake{
	
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
	protected final function add_column($table, $column, $type, $options = null){
		
	}
	
	/**
	 * Generates a table based on properties
	 *
	 * @return void
	 * @access protected
	 **/
	protected final function create_table($name, $cols, $options = null){
		
	}
	
	/**
	 * Drop a table
	 *
	 * @return void
	 * @access protected
	 **/
	protected final function drop_table($table){
		
	}
	
	/**
	 * Remove a column from a table
	 *
	 * @return void
	 * @access protected
	 **/
	protected final function remove_column($table, $column){
		
	}

	
}
 

?>