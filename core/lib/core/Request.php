<?php

namespace Core;

class Request {
	
	public $domain;
	public $address;
	public $port;
	public $request_method;
	public $request_uri;
	public $data;

	public function __construct() {
		$this->domain = $_SERVER['HTTP_HOST'];
		$this->address = $_SERVER['SERVER_ADDR'];
		$this->port = $_SERVER['SERVER_PORT'];
		$this->request_method = $_SERVER['REQUEST_METHOD'];
		$this->request_uri = $_SERVER['REQUEST_URI'];
		$this->method = $_SERVER['REQUEST_METHOD'];
		
		$data = isset($_GET['q']) ? $_GET['q'] : '';
		$data = explode('/', $data);
		
		if ($data[count($data)-1] == '')
			array_pop( $data );
		
		$this->data = $data;
	}
	
	public function isXmlHttpRequest() {
	
		return $_SERVER["HTTP_X_REQUESTED_WITH"] == 'XMLHttpRequest';
		
	}
	
	public function isPost() {
		
		return $this->request_method == 'POST';
		
	}
	
	public function isGet() {
		
		return $this->request_method == 'GET';
		
	}
	
}
