<?php

namespace App;

class Main extends \Core\BaseController {

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
		$tpl = new \UI\CoreTemplate( 'generic_layout' );
		$data = \Core\ConfigManager::getInstance()->get('db', array('lala'));
		$tpl->setVars(array('content' => 'Hello world!'));
		echo $tpl->display();
	}
	
	public function customRoute() {
		$tpl = new \UI\CoreTemplate( 'generic_layout' );
		$tpl->setVars(array('content' => 'Hello custom routes world!'));
		echo $tpl->display();
	}
}
