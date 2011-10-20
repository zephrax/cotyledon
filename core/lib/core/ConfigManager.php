<?php

namespace Core;

class ConfigManager {

	private $_includeDirs;
	private $_config;
	private $_domainconfig;
	
	static protected $_instance;

	public function __construct() {
		
		$this->loadAppConfig();
		
	}
	
	public static function getInstance() {
	
		if (!self::$_instance)
			self::$_instance = new ConfigManager();
		
		return self::$_instance;
	
	}
	
	public function loadAppConfig() {

		include_once ( APP_PATH . '/config.php' );
		
		$this->_config = $config;
		
	}
	
	public function getDomainConfig( $domain = false ) {
	
		if (!$domain)
			$domain = Cotyledon::getInstance()->getRequestHandler()->getRequest()->domain;
		
		if (!isset($this->_config['domains'][$domain]))
			throw new CoreException( sprintf ( 'Configuration for domain \'%s\' not found.', $domain ) );
		
		return $this->_config['domains'][$domain];
		
	}
	
	public function getIncludeDirs() {
		
	}
}
