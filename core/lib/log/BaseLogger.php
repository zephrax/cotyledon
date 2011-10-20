<?php

namespace Log;

abstract class BaseLogger {

	protected $_data; 

	public function BaseLogger() {
	}
	
	protected function setData( $data ) {
		
		$this->_data = $data;
		
	}
	
	protected function getData() {
	
		if (is_object($this->_data) || is_array($this->_data)) {
		
			return print_r ( $this->_data, true );

		} else {
		
			return $this->_data;
		
		}
		
	}
	
	abstract public function log( $data );
		
}
