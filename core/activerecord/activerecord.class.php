<?php

class ActiveRecord {

	protected $columns       = array();
	protected $attributes    = array();
	protected $column_types  = array();
	protected $associations  = array();
	protected $is_modified = false;
	protected $frozen = false;
	protected $primary_key = 'id';
	protected $table_name;
	protected static $query_count = 0;
	protected static $dbh;
	public $new_record  = true;

	private $assoc_types = array('belongs_to', 'has_many', 'has_one');

 	public function __construct($params=null, $new_record=true, $is_modified=false) {
		
		$this->table_name  = Inflector::tableize(get_class($this));
		$this->columns	   = self::get_columns($this);
		$this->primary_key = (isset($this->primary_key) && $this->primary_key != "")? $this->primary_key : "id";
		
		// Associations
		foreach ($this->assoc_types as $type){
			if(isset($this->$type)){
				$class_name = Inflector::classify($type);
				foreach ($this->$type as $assoc){
					$assoc = self::decode_if_json($assoc);
					// Handle association sent in as array with options
					if(is_array($assoc)){
						$key = key($assoc);
						$this->$key = new $class_name($this, $key, current($assoc));
					}else{
						$this->$assoc = new $class_name($this, $assoc);
					}
        		}
      		}
    	}

		// Attributes
		if(is_array($params)){
			foreach ($params as $key => $value) $this->$key = $value;
      		$this->is_modified = $is_modified;
			$this->new_record  = $new_record;
		}
	}

	public function __get($name) {
		
		$name = Inflector::underscore($name);	// Add camelCase support.
		
		if(array_key_exists($name, $this->attributes)){
			 return $this->attributes[$name];
			
		}elseif(array_key_exists($name, $this->associations)){
			return $this->associations[$name]->get($this);
			
		}elseif(in_array($name, $this->columns)){
			return null;
		
		}elseif (preg_match('/^(.+?)_ids$/', $name, $matches)){
			//Allow for $p->comment_ids type gets on HasMany associations
			$assoc_name = Inflector::pluralize($matches[1]);
			if ($this->associations[$assoc_name] instanceof HasMany) return $this->associations[$assoc_name]->get_ids($this);
		}
		
		throw new ActiveRecordException("attribute called '$name' doesn't exist", ActiveRecordException::AttributeNotFound);
	}

	public function __set($name, $value) {
		if($this->frozen) throw new ActiveRecordException("Can not update $name as object is frozen.", ActiveRecordException::ObjectFrozen);

		$name = Inflector::underscore($name); // Add camelCase support.

		// allow for $p->comment_ids type sets on HasMany associations
		if(preg_match('/^(.+?)_ids$/', $name, $matches)){
			$assoc_name = Inflector::pluralize($matches[1]);
		}

		if (in_array($name, $this->columns)){
			$this->attributes[$name] = $value;
			$this->is_modified = true;
		
		}elseif ($value instanceof Association){
			//Call from constructor to setup association
			$this->associations[$name] = $value;
		
		}elseif (array_key_exists($name, $this->associations)){
			//Call like $comment->post = $mypost
			$this->associations[$name]->set($value, $this);
		
		}elseif (array_key_exists($assoc_name, $this->associations) && $this->associations[$assoc_name] instanceof HasMany){
			//Allow for $p->comment_ids type sets on HasMany associations
			$this->associations[$assoc_name]->set_ids($value, $this);
		
		}else{
			throw new ActiveRecordException("attribute called '$name' doesn't exist",ActiveRecordException::AttributeNotFound);
		}
	}

	/* 
	  On any ActiveRecord object we can make method calls to a specific assoc.
	  Example:
	    $p = Post::find(1);
	    $p->comments_push($comment);
	  This calls push([$comment], $p) on the comments association
	
	*/

	public function __call($name, $args){
		list($assoc, $func) = explode("_", $name, 2);
		if (array_key_exists($assoc, $this->associations)){
	    	return $this->associations[$assoc]->$func($args, $this);
		}else{
			throw new ActiveRecordException("method or association not found ($assoc, $func)", ActiveRecordException::MethodOrAssocationNotFound);
		}
	}

	// Misc get functions.

	private function get_columns($item) { 
		$props = get_object_vars($item);
		
		if(!isset($props['columns']) || empty($props['columns'])){
			
			// Read columns from configuration.
			
			$config = Configure::read('db_schema');
			$config = $config[$item];
			
			$columns 	= array();
			$properties = array(); 
			
			foreach($config as $k => $v){
				$columns[] 		= $k;
				$properties[$k] = $v;
			}
			
			$item->column_types = $properties;
			return $columns;
			
		}else{
			return $props['columns'];
		}
	}
	
	private function get_primary_key() { return $this->primary_key; }
	private function is_frozen() { return $this->frozen; }
	private function is_new_record() { return $this->new_record; }
	private function is_modified() { return $this->is_modified; }
	private function set_modified($val) { $this->is_modified = $val; }
	static function get_query_count() { return self::$query_count; }

	// Database specific functions.

	private static function &get_dbh() {
		if(!self::$dbh){
			$db = Environment::getDB();
			$db_mode = $db['driver'];
			
			self::$dbh = call_user_func_array(array(DB_ADAPTER."Adapter", __FUNCTION__),
				array($db['host'], $db['db'], $db['user'], $db['pass'], $db_mode));
		}
		
		return self::$dbh;
	}
	
	private static function get_called_class(){
		$bt = debug_backtrace(); 
		    $lines = file($bt[1]['file']); 
		    preg_match('/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/', 
		               $lines[$bt[1]['line']-1], 
		               $matches); 
		    return $matches[1];
	}

	public static function query($query) {
		$dbh =& self::get_dbh();
		#var_dump($query);
		self::$query_count++;
		return call_user_func_array(array(DB_ADAPTER."Adapter", __FUNCTION__),array($query, $dbh));
	}
  
	private static function quote($string, $type = null){
		$dbh =& self::get_dbh();
		return call_user_func_array(array(DB_ADAPTER."Adapter", __FUNCTION__), array($string, $dbh, $type));
	}

	public static function last_insert_id($resource = null){
		$dbh =& self::get_dbh();
		return call_user_func_array(array(DB_ADAPTER."Adapter", __FUNCTION__), array($dbh, $resource));
	}

	public function update_attributes($attributes) {
		foreach ($attributes as $key => $value)	$this->$key = $value;
		//return $this->save();
	}

	public function save() {
		if(method_exists($this, 'before_save')) $this->before_save();
		
		$check = $this->validate();
		if($check != true) return $check;

		foreach ($this->associations as $name => $assoc){
			if($assoc instanceOf BelongsTo && $assoc->needs_saving()){
        		//Save the object referenced by this association
        		$this->$name->save();
        		
				// After save, $this->$name might have new id; We want to update the foreign key of $this to match;
				//We update this foreign key already as a side-effect when calling set() on an association
        		$this->$name = $this->$name;
			}
		}
		
		if ($this->new_record){
			if(method_exists($this,'before_create')) $this->before_create();
			
			// Insert a new record.
			
			foreach ($this->columns as $column){
				if ($column == $this->primary_key) continue;
				$columns[] = '`' . $column . '`';
				
				if (is_null($this->$column)){
					$values[] = 'NULL';
				}else{
					$values[] = self::quote($this->$column);
				}
			}
			
			$columns = implode(", ", $columns);
			$values  = implode(", ", $values);
			$query = "INSERT INTO {$this->table_name} ($columns) VALUES ($values)";
			$res = self::query($query);
			$this->{$this->primary_key} = self::last_insert_id();
			$this->new_record = false;
			$this->is_modified = false;
		
			if (method_exists($this, 'after_create')) $this->after_create();
		
		}elseif ($this->is_modified){
			if(method_exists($this, 'before_update')) $this->before_update();
			
			// Update existing record.
      		$col_vals = array();
      		foreach ($this->columns as $column){
				if($column == $this->primary_key) continue;
				
				$value = is_null($this->$column) ? 'NULL' : self::quote($this->$column);
				$col_vals[] = "`$column` = $value";
			}
			
			$columns_values = implode(", ", $col_vals);
			$query = "UPDATE {$this->table_name} SET $columns_values ".
					 " WHERE {$this->primary_key} = {$this->{$this->primary_key}}".
					 " LIMIT 1";
					
      		$res = self::query($query);
			$this->new_record = false;
			$this->is_modified = false;
			if(method_exists($this, 'after_update')) $this->after_update();
		}
		
		foreach ($this->associations as $name => $assoc){
			if($assoc instanceOf HasOne && $assoc->needs_saving()){
	        	// Update foreign key as needed.
				$this->$name = $this->$name;
	        	// Save this object.
				$this->$name->save();
			
			}elseif($assoc instanceOf HasMany && $assoc->needs_saving()){
				$assoc->save_as_needed($this);
			}
		}
		if (method_exists($this, 'after_save')) $this->after_save();
	}

	public function destroy() {
		if (method_exists($this, 'before_destroy')) $this->before_destroy();
		
		foreach ($this->associations as $name => $assoc) $assoc->destroy($this);

		$query = "DELETE FROM {$this->table_name} ".
				 "WHERE {$this->primary_key} = {$this->{$this->primary_key}} ".
				 "LIMIT 1";
				
		self::query($query);
		$this->frozen = true;
		if(method_exists($this, 'after_destroy')) $this->after_destroy();
		
		return true;
	}

	/* transform_row -- transforms a row into its various objects
	    accepts: row from SQL query (array), lookup array of column names
	    return: object keyed by table names and real columns names
	*/
	public static function transform_row($row, $col_lookup) {
		$object = array();
		foreach ($row as $col_name => $col_value){
			/* set $object["table_name"]["column_name"] = $col_value */
			$object[$col_lookup[$col_name]["table"]][$col_lookup[$col_name]["column"]] = $col_value;
		}
		return $object;
	}

	public static function find($id, $options=null) {

		$class = (function_exists('get_called_class'))? get_called_class() : self::get_called_class();

		$query = self::generate_find_query($class, $id, $options);
		$rows = self::query($query['query']);

		$base_objects = array();
		foreach ($rows as $row) {
      	/* if we've done a join we have some fancy footwork to do
	          we're going to process one row at a time.
	          each row has a "base" object and objects that've been joined.
	          the base object is whatever class we've been passed as $class.
	          we only want to create one instance of each unique base object.
	          as we see more rows we may be re-using an exising base object to
	          append more join objects to its association.
	      */
			if(count($query['column_lookup']) > 0){
        		$objects = self::transform_row($row, $query['column_lookup']);
        		$ob_key = md5(serialize($objects[Inflector::tableize($class)]));

        		// Set cur_object to base object for this row; reusing if possible
        		if (array_key_exists($ob_key, $base_objects)){
          			$cur_object = $base_objects[$ob_key];
        		}else{
          			$cur_object = new $class($objects[Inflector::tableize($class)], false);
          			$base_objects[$ob_key] = $cur_object;
        		}

        		// Add association data.

				foreach ($objects as $table_name => $attributes) {
					if($table_name == Inflector::tableize($class)) continue;
					foreach ($cur_object->associations as $assoc_name => $assoc){
						if($table_name == Inflector::pluralize($assoc_name)) $assoc->populate_from_find($attributes);
					}
				}
				
			}else{
				$item = new $class($row, false);
				array_push($base_objects, $item);
			}
		}
	
		if (count($base_objects) == 0 && (is_array($id) || is_numeric($id))) return null;
		return (is_array($id) || $id == 'all') ? array_values($base_objects) : array_shift($base_objects);
		
		if (method_exists($this, 'on_load')) $this->on_load();
	}

	private function generate_find_query($class_name, $id, $options=null) {

		//$dbh =& $this->get_dbh();
		$item = new $class_name;
		
		$item->table_name = Inflector::tableize(get_class($item));
		if(!isset($item->columns) || empty($item->columns)) $item->columns = self::get_columns($item);
		
		// Sanitize
      	if (is_array($id)){
        	foreach ($id as $k => $v){
          		$id[$k] = self::quote($v);
        	}
		}elseif($id != 'all' && $id != 'first'){
			$id = self::quote($id);
		}
      
		// Regex on limit, order, group
		$regex = '/^[A-Za-z0-9\-_ ,\(\)]+$/';
		
		if(isset($options['limit']) && !preg_match($regex, $options['limit'])) unset($options['limit']);
	    if(isset($options['order']) && !preg_match($regex, $options['order'])) unset($options['order']);
		if(isset($options['group']) && !preg_match($regex, $options['group'])) unset($options['group']);
		if(isset($options['offset']) && !is_numeric($options['offset'])) unset($options['offset']);

		$select = '*';
		
		if(is_array($id)){
			$where = "{$item->primary_key} IN (" . implode(",", $id) . ")";
		}elseif($id == 'first'){
        	$limit = '1';
		}elseif ($id != 'all'){
        	$where = "{$item->table_name}.{$item->primary_key} = $id";
		}

		if(isset($options['conditions'])){
			$where = (isset($where)) ? $where . " AND (" . $options['conditions'] .")" : $options['conditions'];
		}

		if(isset($options['offset'])) $offset = $options['offset'];
		if(isset($options['limit']) && !isset($limit)) $limit = $options['limit'];
		if(isset($options['select'])) $select = $options['select'];
		
		$joins = array();
		$tables_to_columns = array();
      
		if(isset($options['include'])){
			array_push($tables_to_columns,array(Inflector::tableize(get_class($item)) => $item->get_columns($item)));
			$includes = preg_split('/[\s,]+/', $options['include']);
        
			// Get join part of query from association and column names
        
			foreach ($includes as $include) {
				if (isset($item->associations[$include])){
					list($cols, $join) = $item->associations[$include]->join();
					array_push($joins, $join);
					array_push($tables_to_columns, $cols);
				}
	        }

	        // Set the select variable so all column names are unique
        
			$selects = array();
	        $columnLookup = array();

			foreach ($tables_to_columns as $table_key => $columns){
				foreach ($columns as $table => $cols){
					foreach ($cols as $key => $col){
	              		array_push($selects, "$table.`$col` AS t{$table_key}_r$key");
						$columnLookup["t{$table_key}_r{$key}"]["table"] = $table;
						$columnLookup["t{$table_key}_r{$key}"]["column"] = $col;
					}
	            }
			}
			
	        $select = implode(", ", $selects);
		}

		if(!isset($columnLookup)) $columnLookup = array();

		// joins (?), include

		$query  = "SELECT $select FROM {$item->table_name}";
		$query .= (count($joins) > 0) ? " " . implode(" ", $joins) : "";
		$query .= (isset($where)) ? " WHERE $where" : "";
		$query .= (isset($options['group'])) ? " GROUP BY {$options['group']}" : "";
		$query .= (isset($options['order'])) ? " ORDER BY {$options['order']}" : "";
		$query .= (isset($limit)) ? " LIMIT $limit" : "";
		$query .= (isset($offset)) ? " OFFSET $offset" : "";
		return array('query' => $query, 'column_lookup' => $columnLookup);
	}
	
	
	public function validate(){		
		if(!property_exists($this,'validates')) return true;
		if(!is_array($this->validates)) return true;
	
		$invalid = array();
		
		foreach($this->validates as $k => $v){
			$value = $this->attributes[$k];
			
			if(isset($v['type'])){		// Checks variable type.
				switch($v['type']){	
					case "exists":
						if($value == null || $value == "" || !isset($value)) $invalid[] = $k;
					break;				

					case "email":
						if(!preg_match("@^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$@/i", $value)) $invalid[] = $k;
					break;
					
					case "currency":
						$this->attributes[$k] = number_format($value,2);
					break;
				}
			}
			
			if(!isset($v['length'])){	// Checks string lengths.
				
			}
		}
		
		return (count($invalid) > 0)? $invalid : true;
		
	}
	
	
}


?>
