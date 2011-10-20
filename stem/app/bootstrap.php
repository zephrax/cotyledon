<?php

define ( 'CORE_PATH', realpath ( dirname ( __FILE__ ) . '/../../core/' ) );
define ( 'APP_PATH', realpath( dirname(__FILE__) . '/../app' ) );
define ( 'WWW_PATH', realpath ( dirname(__FILE__) . '/../www' ) );

require_once ( CORE_PATH . '/bootstrap.php' );
