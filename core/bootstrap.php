<?php

/**
 * Cotyledon Framework Initialization
 */

require_once( CORE_PATH . '/functions/constants.inc.php' );
require_once( CORE_PATH . '/lib/Autoload.class.php' );
require_once( CORE_PATH . '/functions/autoload.inc.php' );

$_cotyledon = Core\Cotyledon::getInstance();

$_cotyledon->init();

//$_cotyledon->registerCoreEvents();

//$_cotyledon->processRequest();

