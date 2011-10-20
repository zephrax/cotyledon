<?php

namespace UI;

class BaseTemplate {

	protected $_file;
	
	protected $_variables = array();
	
	public function __construct( $file = null ) {
	
		if (file_exists($file)) {
		
			$this->_file = $file;
			
		} else {
		
			throw new \Core\CoreException( sprintf ( 'Template \'%s\' not found.', $file ) );
			
		}
		
	}
	
	public function setFile( $file ) {
	
		$this->_file = $file;
	
	}

	public function setVar($name, $value) {
	
		$this->_variables[$name] = $value;
		
	}
	
	public function setVars( $vars ) {
	
		$this->_variables = $vars;
	
	}
	
	public function addVars( $vars ) {
	
		$this->_variables = array_merge ( $this->_variables, $vars );
		
	}
	
	public function clearVars() {
	
		$this->_variables = array ();
	
	}
	
	public function display() {
	
		ob_start();
	
		extract ( $this->_variables );
		
		require_once( $this->_file );
		
		$data = ob_get_contents();
		
		ob_end_clean();
		
		return $data;
		
	}
	
}
