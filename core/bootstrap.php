<?php

/**
 * Cotyledon Framework Initialization
 */

require_once( CORE_PATH . '/functions/constants.inc.php' );
require_once( CORE_PATH . '/lib/Autoload.class.php' );
require_once( CORE_PATH . '/functions/autoload.inc.php' );

$_cotyledon = Core\Cotyledon::getInstance();

$session = $_cotyledon->getSessionHandler();
//$session->peticiones++; echo $session->peticiones; exit;

$_cotyledon->getRouter()->loadController();

$_cotyledon->setupDBConnection();

//$_cotyledon->registerCoreEvents();

$_cotyledon->processRequest();

