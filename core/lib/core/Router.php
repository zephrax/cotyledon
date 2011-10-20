<?php

namespace Core;

class Router {

	protected static $instance;
	private $controller;
	private $request;

	public function __construct() {
	
		$this->request = new Request();
	
	}
	
	public static function getInstance() {
	
		if (!self::$instance) {
			self::$instance = new self();
		}
                
		return self::$instance;
		
	}
	
	public function getRequest() {
		
		return $this->request;
		
	}
	
	public function getController() {
	
		return $this->controller;
	
	}
	
	public function loadController() {
	
		$controller = new \App\Main\Controller( $this->request );
		
		if ($controller instanceof \App\Main\Controller) {
			
			$this->controller = $controller;
                        
			$this->controller->configure();
			
		} else {
			
			throw new CoreException ( 'Main module doesn\'t seems to be a Cotyledon controller.' );
			
		}
		
	}
	
	public function dispatch() {
	
		$event = new Event( $this, 'core.before_dispatch', array ( ) );
		Cotyledon::getInstance()->getEventDispatcher()->notify( $event );

		$this->controller->process();
		
		$event = new Event( $this, 'core.after_dispatch', array ( ) );
		Cotyledon::getInstance()->getEventDispatcher()->notify( $event );
		
	}

}
