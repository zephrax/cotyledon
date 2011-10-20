<?php

function eh($errno, $errstr, $errfile, $errline) {
	echo "error";
}

function exh($ex) {
	print_r($ex);
}

set_exception_handler('exh');

throw new Exception('lala');

