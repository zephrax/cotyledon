<?php

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
