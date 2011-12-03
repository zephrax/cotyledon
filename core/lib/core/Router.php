<?php
/**
 * Cotyledon Router
 * 
 * Some code was taken from Flight PHP MicroFramework (http://flightphp.com/)
 * 
 * @author Jonatan Bravo <zephrax@gmail.com>
 */

namespace Core;

class Router {
    
    protected $routes = array();
    private $controller;
    private $request;
    
    /**
     * Router Constructor
     * @param \Core\Request $request Object with the request data
     */
    public function __construct(&$request = false) {
	if ($request)
	    $this->request = $request;
    }

    /**
     * Maps a URL pattern to a callback function.
     * @param string $pattern URL pattern to match
     * @param callback $callback Callback function
     */
    public function map($pattern, $callback) {
	$parts = explode(' ', trim($pattern), 2);
	if (count($parts) == 1)
	    $url = null;
	else {
	    $method = $parts[0];
	    $url = $parts[1];
	}

	if (!is_null($url)) {
	    foreach (explode('|', $method) as $value) {
		$this->routes[$value][$url] = $callback;
	    }
	} else {
	    $this->routes['*'][$pattern] = $callback;
	}
    }

    /**
     * Tries to match a requst to a route. Also parses named parameters in the url.
     * @param string $pattern URL pattern
     * @param string $url Request URL
     * @param array $params Named URL parameters
     */
    public function match($pattern, $url, array &$params = array()) {
	$ids = array();

	$regex = '/^' . implode('\/', array_map(
				function($str) use (&$ids) {
				    if ($str == '*') {
					$str = '(.*)';
				    } else if (isset($str{0}) && $str{0} == '@') {
					if (preg_match('/@(\w+)(\:([^\/]*))?/', $str, $matches)) {
					    $ids[$matches[1]] = true;
					    return '(?P<' . $matches[1] . '>' . (isset($matches[3]) ? $matches[3] : '[^(\/|\?)]+') . ')';
					}
				    }
				    return $str;
				}, explode('/', $pattern)
			)) . '\/?(?:\?.*)?$/i';
	
	if (preg_match($regex, $url, $matches)) {
	    if (!empty($ids)) {
		$params = array_intersect_key($matches, $ids);
	    }
	    return true;
	}

	return false;
    }

    /**
     * Routes the current request.
     * @param object $request Request object
     */
    public function route(\Core\Request &$request) {
	$none_match = false;
	$params = array();
	$routes = (isset($this->routes[$request->method]) ? $this->routes[$request->method] : array()) + (isset($this->routes['*']) ? $this->routes['*'] : array());

	if (!empty($routes)) {
	    foreach ($routes as $pattern => $callback) {
		if ($pattern === '*' || $request->url === $pattern || self::match($pattern, $request->url, $params)) {
		    $request->matched = $pattern;
		    return array($callback, $params);
		}
	    }

	    $none_match = true;
	}

	if (empty($routes) || $none_match) {
	    try {
		$result = $this->getDefaultRouteFile();
	    } catch (\Core\CoreException $e) {
		switch ($e->getCode()) {
		    case \Core\CoreException::CONTROLLER_NOT_FOUND:
			return false;
			break;
		}
	    }
	}

	return $result;
    }
    
    /**
     * Get Controller Class and method for the current request url
     * @return mixed
     */
    private function getDefaultRouteFile() {
	$base_dir = APP_PATH . '/modules/';
	$base_class = '\\App';
	$params = array();
	$method = '';
	
	if (!isset($this->request->data[0]))
		return array(array('\\App\\Main', 'process'), $params);
	
	foreach ($this->request->data as $index => $part) {
	    if (file_exists($base_dir . $part) && is_dir($base_dir . $part)) {
		$base_dir .= ucwords($part) . '/';
		$base_class .= '\\' . ucwords($part);
	    } else {
		if (file_exists($base_dir . ucwords($part) . '.php')) {
		    $base_class .= '\\' . ucwords($part);
		    $method = isset($this->request->data[$index + 1]) ? $this->request->data[$index + 1] : 'process';
		    $index++;
		} else {
		    if (file_exists($base_dir . 'Main.php')) {
			$base_class .= '\\Main';
			$method = $part;
		    } else {
			throw new \Core\CoreException('Controller not found', \Core\CoreException::CONTROLLER_NOT_FOUND);
		    }
		}
	    }
	    
	    if ($method) {
		$params = array_slice($this->request->data, ++$index);
		break;
	    }
	}
	
	return array (array($base_class, $method), $params);
    }
    
    /**
     * Gets mapped routes.
     * @return array Array of routes
     */
    public function getRoutes() {
	return $this->routes;
    }

    /**
     * Resets the router.
     */
    public function clear() {
	$this->routes = array();
    }

}
