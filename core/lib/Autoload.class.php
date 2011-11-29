<?php
/**
 * Cotyledon Class Autoloader
 *
 * @author Jonatan Bravo <zephrax@gmail.com>
 */

class AutoLoader {

    public function __construct() {
	
    }

    public static function process($name) {
	$parts = explode('\\', $name);
	$parts[0] = strtolower($parts[0]);

	switch ($parts[0]) {
	    case 'app':
		$file = APP_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . ucwords($parts[1]);

		if (file_exists($file) && is_dir($file)) {
		    $file = APP_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . ucwords($parts[1]) . DIRECTORY_SEPARATOR . $parts[2] . '.php';
		} else {
		    if (file_exists($file . '.php')) {
			$file .= '.php';
		    }
		}

		if (file_exists($file)) {
		    require_once( $file );
		} else {
		    throw new Exception(sprintf('Class \'%s\' not found.', $parts[1]));
		}
		break;

	    default:
		if (is_dir(CORE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $parts[0])) {
		    $file = CORE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $parts[0] . DIRECTORY_SEPARATOR . $parts[1] . '.php';
		    if (file_exists($file)) {
			require_once( $file );
		    } else {
			throw new Exception(sprintf('Class \'%s\' not found.', $parts[1]));
		    }
		}
	}
    }

}
