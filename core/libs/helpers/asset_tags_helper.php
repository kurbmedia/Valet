<?php

namespace View;

class AssetTagsHelper extends Helper{
	
	/**
	 * Creates an image tag.
	 *
	 * @return void
	 **/
	public function image_tag($file, $options = array()){

		$options['alt'] = (isset($options['alt']))? $options['alt'] : basename($file);
		$options['src'] = $file;
		
		return $this->create_tag("img", $options);
		
	}
	
	/**
	 * Creates an image tag.
	 *
	 * @return void
	 **/
	public function stylesheet_tag($file, $options = array()){

		$options['href'] 	= $file;
		$options['rel']  	= 'stylesheet';
		$options['type'] 	= 'text/css';
		$options['media'] 	= (isset($options['media']))? $options['media'] : 'screen';

		return $this->create_tag("link", $options);
		
	}
	
	
}


?>