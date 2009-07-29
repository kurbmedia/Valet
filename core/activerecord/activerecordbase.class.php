<?php

abstract class ActiveRecordBase extends ActiveRecord{
	
	public static function find($id, $args){
		return parent::find(__CLASS__, $id, $args);
	}
	
}

?>