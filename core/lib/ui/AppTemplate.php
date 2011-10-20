<?php

namespace UI;

class AppTemplate extends BaseTemplate {

	public function __construct ( $template = null ) {

		if ($template) {
		
			$file = APP_PATH . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . str_replace( '.', DIRECTORY_SEPARATOR, $template ) . '.tpl';
			
			parent::BaseTemplate( $file );
			
		}

	}

}
