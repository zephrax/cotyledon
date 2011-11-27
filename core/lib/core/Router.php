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
        $params = array();
        $routes = (isset($this->routes[$request->method]) ? : array()) + (isset($this->routes['*']) ? : array());

        if (!empty($routes)) {
            foreach ($routes as $pattern => $callback) {
                if ($pattern === '*' || $request->url === $pattern || self::match($pattern, $request->url, $params)) {
                    $request->matched = $pattern;
                    return array($callback, $params);
                }
            }
        } else {
            $params = array();
            return array( array ( '\\App\\' . ( isset($request->data[0]) ? $request->data[0] : 'Main' ) .
                '\\' . ( isset($request->data[1]) ? $request->data[1] : 'Default' ), 
                ( isset($request->data[2]) ? $request->data[2] : 'process' ) ), $params);
        }

        return false;
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
