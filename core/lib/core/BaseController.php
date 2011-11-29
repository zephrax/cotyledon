<?php
/**
 * Cotyledon Base Controller Class
 * Mostly of the app controllers should extend from this class
 * 
 * @author Jonatan Bravo <zephrax@gmail.com>
 */

namespace Core;

abstract class BaseController {

	protected $request;
	
	protected $tpl;

	public function __construct ( \Core\Request $request ) {
		
		$this->request = $request;
		
		$this->tpl = new \UI\Template();
		
	}

	abstract public function configure ();

	abstract public function process ();
	
	public function redirect( $url ) {
		
		header ( 'Location: ' . $url );
		
	}

}
