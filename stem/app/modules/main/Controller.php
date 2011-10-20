<?php

namespace App\Main;

class Controller extends \Core\BaseController {

	protected $request;

	public function __construct( \Core\Request $request ) {

		parent::__construct( $request );
	
	}
	
	public function configure() {
	
		\Core\Cotyledon::bindEvent ( 'core.before_dispatch', array ( $this, 'onBeforeDispatch' ) );
	
		\Core\Cotyledon::bindEvent ( 'core.after_dispatch', array ( $this, 'onAfterDispatch' ) );
	
		$this->tpl->setLayout( 'core.generic_layout' );
	
	}
	
	public function onBeforeDispatch( \Core\Event $event ) {

		echo "Before dispatch...<br />";

	}
	
	public function onAfterDispatch( \Core\Event $event ) {
	
		echo "After dispatch...<br />";
	
	}
	
	public function process() {
	
		echo $this->tpl->display();
	
		//$tpl = new \UI\CoreTemplate( 'layout' );
		
		//$tpl->setVars(array('content'=>'Hello world!'));
		
		//$logger = new \Log\FileLogger('/tmp/a.txt');
		
		//$logger->log($tpl->display());
		
		//echo $tpl->display();
		
	}
	
}
