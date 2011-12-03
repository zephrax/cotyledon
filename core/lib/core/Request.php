<?php
/**
 * Cotyledon Request abstraction class
 * 
 * @author Jonatan Bravo <zephrax@gmail.com>
 */

namespace Core;

class Request {
    /**
     * Request constructor
     * @param array $config Configuration variables for initialization
     */
    public function __construct($config = array()) {
        if (empty($config)) {
            $config = array(
		'domain' => $_SERVER['HTTP_HOST'],
		'address' => $_SERVER['SERVER_ADDR'],
		'port' => $_SERVER['SERVER_PORT'],
		'method' => $_SERVER['REQUEST_METHOD'],
		'url' => $_SERVER['REQUEST_URI'],
		'data' => $this->getData()
            );
        }
	
        self::init($config);
    }

    /**
     * Initializes this request object with the given data
     * @param array $data Configuration variables for initialization
     */
    private function init($data) {
	foreach ($data as $key => $value) {
	    $this->{$key} = $value;
	}
    }
    
    /**
     * Get the data passed in the 'q' parameter (http rewrite)
     */
    private function getData() {
	$data = isset($_GET['q']) ? $_GET['q'] : '';
	$data = explode('/', $data);

	if ($data[count($data) - 1] == '')
	    array_pop($data);

	return $data;
    }

    /**
     * Returns true if is a XML Http Request a.k.a. Ajax
     */
    public function isXmlHttpRequest() {
	return $_SERVER["HTTP_X_REQUESTED_WITH"] == 'XMLHttpRequest';
    }

    /**
     * Returns true if is a POST Reuest
     */
    public function isPost() {
	return $this->method == 'POST';
    }

    /**
     * Returns true if is a GET Request
     */
    public function isGet() {
	return $this->method == 'GET';
    }
}
