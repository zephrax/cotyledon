<?php

namespace Core;

class Router {

    protected $routes = array();
    private $controller;
    private $request;

    public function __construct(&$request) {
	$this->request = $request;
    }

    public function getRequest() {

	return $this->request;
    }

    /**
     * Maps a URL pattern to a callback function.
     *
     * @param string $pattern URL pattern to match
     * @param callback $callback Callback function
     */
    public function map($pattern, $callback) {
	list($method, $url) = explode(' ', trim($pattern), 2);

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
     *
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
				    } else if ($str{0} == '@') {
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
     *
     * @param object $request Request object
     */
    public function route(&$request) {
	$none_match = false;
	$params = array();
	$routes = (isset($this->routes[$request->method]) ? : array()) + (isset($this->routes['*']) ? : array());

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
	    return $this->getDefaultRouteFile();
	}

	return false;
    }
    
    private function getDefaultRouteFile() {
	$base_dir = APP_PATH . '/modules/';
	$base_class = '\\App';
	$params = array();
	$method = '';
	
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
     * DEPRECATED
     */
    private function getDefRouteFile() {
	$params = array();

	if (isset($this->request->data[0])) {
	    $current_try = APP_PATH . '/modules/' . ucwords($this->request->data[0]);

	    if (file_exists($current_try)) { // check dir
		if (is_dir($current_try)) {
		    if (isset($this->request->data[1])) {
			$current_try .= '/' . ucwords($this->request->data[1]) . '.php';
			if (file_exists($current_try)) {
			    return array(array('\\App\\' . $this->request->data[0] .
				    '\\' . $this->request->data[1],
				    ( isset($this->request->data[2]) ? $this->request->data[2] : 'process' )), $params);
			} else {
			    $current_try = APP_PATH . '/modules/' . ucwords($this->request->data[0]) . '/Main.php';
			    if (file_exists($current_try)) {
				return array(array('\\App\\' . $this->request->data[0] . '\\Main',
					( isset($this->request->data[1]) ? $this->request->data[1] : 'process' )), $params);
			    } else {
				throw new \Core\CoreException('Controller not found: "' . $current_try . '"', \Core\CoreException::CONTROLLER_NOT_FOUND);
			    }
			}
		    } else {
			$current_try .= '/Main.php';

			if (file_exists($current_try)) {
			    return array(array('\\App\\' . $this->request->data[0] . '\\Main',
				    ( isset($this->request->data[1]) ? $this->request->data[1] : 'process' )), $params);
			} else {
			    throw new \Core\CoreException('Controller not found: "' . $current_try . '"', \Core\CoreException::CONTROLLER_NOT_FOUND);
			}
		    }
		}
	    } else { // dir not found => check file
		$current_try .= '.php';

		if (file_exists($current_try)) {
		    return array(array('\\App\\' . $this->request->data[0],
			    ( isset($this->request->data[1]) ? $this->request->data[1] : 'process' )), $params);
		} else {
		    $current_try = APP_PATH . '/modules/Main.php';

		    if (file_exists($current_try)) {
			return array(array('\\App\\Main',
				( isset($this->request->data[0]) ? $this->request->data[0] : 'process' )), $params);
		    } else {
			throw new \Core\CoreException('Controller not found: "' . $current_try . '"', \Core\CoreException::CONTROLLER_NOT_FOUND);
		    }
		}
	    }
	} else { // Default
	    $current_try = APP_PATH . '/modules/Main.php';

	    if (file_exists($current_try)) {
		return array(array('\\App\\Main', 'process'), $params);
	    } else {
		throw new \Core\CoreException('Controller not found: "' . $current_try . '"', \Core\CoreException::CONTROLLER_NOT_FOUND);
	    }
	}
    }

    /**
     * Gets mapped routes.
     *
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
