<?php

namespace Core;

class ExceptionHandler {

	public static function process ( $exception ) {
		print_r( $exception );
	}
	
}
