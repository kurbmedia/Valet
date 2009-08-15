<?php

/**
 * Helper base.
 *
 * @package default
 * @author Brent Kirby
 **/

class HelperBase{
	
	/**
	 * Holds all open html tags.
	 *
	 * @var string
	 **/
	private $_open_tags = array();
	
	/**
	 * We found a dead function call.
	 *
	 * @return void
	 **/
	public function __call($name, $args){
		throw new Error("Invalid helper method '$name'");
	}
	
	/**
	 * Creates a single line HTML tag
	 *
	 * @return void
	 **/
	protected final function create_tag($tag, $options, $multi_line = false){
		
		foreach ($options as $k => &$v) {
            if($v === null || $v === false)	unset($options[$k]);
        }

		if(!empty($options)){
		
			foreach ($options as $k => &$v) $v = $k . '="'.$v.'"';
			sort($options);
			$tag_vars = implode(' ', $options);
		
		}else{
			$tag_vars = "";
		}
		
		$end = ($multi_line == true)? ">" : " />";			
		return "<$tag ".$tag_vars.$end;
	}
	
	/**
	 * Opens a html tag.
	 *
	 * @return void
	 * @access protected
	 **/
	protected final function open_tag($tag, $options){
		array_push($this->_open_tags, $tag);
		return $this->create_tag($tag, $options, true);
	}
	
	
	/**
	 * Output the last opened html tag.
	 *
	 * @return void
	 **/
	public final function end(){
		return "</".array_pop($this->_open_tags).">";
	}

}


?>