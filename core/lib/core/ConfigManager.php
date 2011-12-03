<?php
/**
 * Cotyledon Config Manager
 *	for loading configuration
 * 
 * @author Jonatan Bravo <zephrax@gmail.com>
 */

namespace Core;

class ConfigManager {
	private $_config;
	static protected $_instance;

	/**
	 * Singleton constructor
	 */
	public function __construct() {
		$this->loadAppConfig();
	}
	
	/**
	 * Return ConfigManager singleton
	 */
	public static function getInstance() {
		if (!self::$_instance)
			self::$_instance = new ConfigManager();
		
		return self::$_instance;
	}
	
	/**
	 * Load application config file and saves it into a class variable
	 */
	public function loadAppConfig() {
		include_once ( APP_PATH . '/config.php' );
		$this->_config = $config;
	}
	
	/**
	 * Return configuration for the given domain or the requested one
	 * @param string $domain Domain name to get config from
	 * @return array
	 */
	public function getDomainConfig( $domain = false ) {
	
		if (!$domain)
			$domain = Cotyledon::getInstance()->getRequest()->domain;
		
		if (!isset($this->_config['domains'][$domain]))
			throw new CoreException( sprintf ( 'Configuration for domain \'%s\' not found.', $domain ) );
		
		return $this->_config['domains'][$domain];
		
	}
	
	/**
	 * Get config item
	 * @param string $config Config path in the way 'parent.something[...]'
	 * @param mixed $default Default value if the given configuration not found
	 * @param string $domain Option Domain
	 */
	public function get($config, $default = '', $domain = false) {
	    $result = $this->getDomainConfig($domain);
	    
	    $parts = explode('.', $config);
	    foreach ($parts as $key => $part) {
		if (isset($result[$part]))
		    $result = $result[$part];
		else {
		    $result = $default;
		    break;
		}
	    }
	    
	    return $result;
	}
}
