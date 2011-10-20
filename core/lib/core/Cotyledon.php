<?php

namespace Core;

class Cotyledon {
	
	protected static $instance;
	
	protected $event_dispatcher;
	protected $error_handler;
	protected $request_handler;
	protected $config_manager;
	public $session;
	
	/**
	 * Constructor
	 * Initializes everything we need
	 */
	public function __construct() {
		
		set_exception_handler( array('Core\ExceptionHandler', 'process') );
		
		$this->error_handler = ErrorHandler::getInstance();
		$this->event_dispatcher = new EventDispatcher();
		$this->request_handler = RequestHandler::getInstance();
		$this->config_manager = ConfigManager::getInstance();
		
	}
	
	public static function getInstance() {
	
		if (!self::$instance instanceof self)
			self::$instance = new self;
		
		return self::$instance;
		
	}
	
	public static function bindEvent( $event_name, $callback ) {
	
		self::getInstance()->event_dispatcher->connect( $event_name, $callback );
		
	}
	
	public function getEventDispatcher() {
	
		return $this->event_dispatcher;
		
	}
	
	public function getErrorHandler() {
	
		return $this->error_handler;
		
	}
	
	public function getRequestHandler() {
		
		return $this->request_handler;
		
	}
	
	public function getConfigManager() {
	
		return ConfigManager::getInstance();
		
	}
	
	public function setupDBConnection() {
	
		$cfg = $this->config_manager->getDomainConfig();
		
		if ( $cfg['use_db'] ) {
			\DB\ORM::configure( $cfg['db']['type'] . ':host='.$cfg['db']['server'].';dbname=' . $cfg['db']['database'] );
			\DB\ORM::configure( 'username', $cfg['db']['username'] );
			\DB\ORM::configure( 'password', $cfg['db']['password'] );
		}
		
	}
	
	public function getSessionHandler() {
		return Session::getInstance();
	}


	public function processRequest() {
		$this->request_handler->dispatch();	
	}	
}
