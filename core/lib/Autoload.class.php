<?php

class AutoLoader {

	public function __construct() {
	}
	
	public static function process($name) {
		
		$parts = explode( '\\' , $name );
		$parts[0] = strtolower($parts[0]);
		
		switch ( $parts[0] ) {
			case 'app':
				if ( is_dir(APP_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . strtolower($parts[1]) ) ) {
					$file = APP_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . strtolower($parts[1]) . DIRECTORY_SEPARATOR . $parts[2] . '.php';
					if (file_exists( $file )) {
						require_once( $file );
					} else {
						throw new Exception( sprintf ( 'Class \'%s\' not found.', $parts[1] ) );
					}
				}
				break;
			
			default:
				if ( is_dir(CORE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $parts[0]) ) {
					$file = CORE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $parts[0] . DIRECTORY_SEPARATOR . $parts[1] . '.php';
					if (file_exists( $file )) {
						require_once( $file );
					} else {
						throw new Exception( sprintf ( 'Class \'%s\' not found.', $parts[1] ) );
					}
				}
		}

	}

}
