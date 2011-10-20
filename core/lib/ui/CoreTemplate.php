<?php

namespace UI;

class CoreTemplate extends BaseTemplate {

	public function __construct ( $template = null ) {
		
		if ($template) {
		
			$file = CORE_PATH . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . str_replace( '.', DIRECTORY_SEPARATOR, $template ) . '.tpl';
			
			parent::__construct( $file );
			
		}
		
	}

}
