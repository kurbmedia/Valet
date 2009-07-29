<?php

abstract class ActiveRecordBase{
	
	public static function find($id, $args){
		return ActiveRecord::find(get_class($this), $id, $args);
	}
	
}

?>