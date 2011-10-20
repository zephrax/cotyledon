<?php

namespace UI;

class Template {

	protected $layout;
	
	public function __construct( $layout = null ) {

		if ($layout)
			$this->setLayout( $layout );
			
	}
	
	public function setLayout( $layout = null ) {
		
		$parts = explode( '.', $layout );
		$space = array_shift( $parts );
		
		switch ( $space ) {
		
			case 'core':
				$this->layout = new CoreTemplate( implode ( '.', $parts ) );
				break;
			
			case 'leaf': // TODO leaf templates
				break;
			
			case 'app':
				$this->layout = new AppTemplate( implode ( '.', $parts ) );
				break;
			
			default:
				$this->layout = new AppTemplate( 'app.' . $layout );

		}
		
	}
	
	public function display () {
	
		return $this->layout->display ();
	
	}
	
}
