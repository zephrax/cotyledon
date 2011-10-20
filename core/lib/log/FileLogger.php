<?php

namespace Log;

class FileLogger extends BaseLogger {

	protected $_file;
	protected $_fp;

	public function FileLogger( $file ) {
		
		$this->_file = $file;
		
	}
	
	private function openFile() {
		
		$this->_fp = fopen( $this->_file, 'a+' );
		
	}

	private function closeFile() {
		
		$this->_fp = fopen( $this->_file, 'a+' );
		
	}
	
	private function writeToFile() {
	
		fwrite( $this->_fp, $this->getData() );
	
	}
	
	public function log($data) {
		
		$this->openFile();
		
		$this->setData( $data );
		
		$this->writeToFile();
	
		$this->closeFile();
	}

}
