<?php

namespace Core;

class ErrorHandler {

	public static $instance;

	public function __construct() {
		//register_shutdown_function( array ( $this, 'handleShutdown' ) );
		set_error_handler ( array ( $this, 'handleError' ) );
	}
	
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function handleError($errno, $errstr, $errfile, $errline) {
		echo sprintf('Error #%s "%s" <br />', $errno, $errstr);
		echo sprintf('File "%s", line #%s<br />', $errfile, $errline);
	}

	public function handleShutdown () {
        $error = error_get_last();
        
        if($error !== NULL){
            $info = "[SHUTDOWN] file: " . $error['file'] . " | ln: " . $error['line']." | msg:" . $error['message'] . PHP_EOL;
            echo $info;
		}
	}
}
