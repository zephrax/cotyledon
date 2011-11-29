<?php
/**
 * Cotyledon: PHP Framework
 * 
 * @author Jonatan Bravo <zephrax@gmail.com>
 */

namespace Core;

class Cotyledon {

    protected static $instance;
    protected $event_dispatcher;
    protected $error_handler;
    protected $config_manager;
    protected $request;
    protected $router;
    protected $response;
    public $session;

    /**
     * Constructor
     * Initializes everything we need
     */
    public function __construct() {

        set_exception_handler(array('Core\ExceptionHandler', 'process'));

        $this->error_handler = ErrorHandler::getInstance();
        $this->event_dispatcher = new EventDispatcher();
        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->config_manager = ConfigManager::getInstance();
        $this->response = new Response();
    }
    
    public function init() {
        
        $result = $this->router->route($this->request);
        
        if ($result !== false) {
	    list($callback, $params) = $result;
	    
	    $theparams = array_values($params);
	    $this->execute($callback, $theparams);
                
        } else {
            $this->notFound();
        }
    }
    
    public function notFound() {
        die('Not found');
        //$this->response->status('404');
    }
    
    public static function getInstance() {

        if (!self::$instance instanceof self)
            self::$instance = new self;

        return self::$instance;
    }

    public static function bindEvent($event_name, $callback) {

        self::getInstance()->event_dispatcher->connect($event_name, $callback);
    }

    public function getEventDispatcher() {

        return $this->event_dispatcher;
    }

    public function getErrorHandler() {

        return $this->error_handler;
    }

    public function getRouter() {

        return $this->router;
    }

    public function getResponse() {

        return $this->response;
    }

    public function getConfigManager() {

        return ConfigManager::getInstance();
    }

    public function setupDBConnection() {

        $cfg = $this->config_manager->getDomainConfig();

        if ($cfg['use_db']) {
            \DB\ORM::configure($cfg['db']['type'] . ':host=' . $cfg['db']['server'] . ';dbname=' . $cfg['db']['database']);
            \DB\ORM::configure('username', $cfg['db']['username']);
            \DB\ORM::configure('password', $cfg['db']['password']);
        }
    }

    public function getSessionHandler() {
        return Session::getInstance();
    }

    public function processRequest() {
        $this->router->dispatch();
    }

    /**
     * Executes a callback function.
     *
     * @param callback $callback Callback function
     * @param array $params Function parameters
     * @return mixed Function results
     */
    public function execute($callback, array &$params = array()) {
        if (is_callable($callback)) {
            return is_array($callback) ?
                    $this->invokeMethod($callback, $params) :
                    $this->callFunction($callback, $params);
        }
    }

    /**
     * Calls a function.
     *
     * @param string $func Name of function to call
     * @param array $params Function parameters 
     */
    public function callFunction($func, array &$params = array()) {
	
        switch (count($params)) {
            case 0:
                return $func();
            case 1:
                return $func($params[0]);
            case 2:
                return $func($params[0], $params[1]);
            case 3:
                return $func($params[0], $params[1], $params[2]);
            case 4:
                return $func($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return $func($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                return call_user_func_array($func, $params);
        }
    }

    /**
     * Invokes a method.
     *
     * @param mixed $func Class method
     * @param array $params Class method parameters
     */
    public function invokeMethod($func, array &$params = array()) {
        list($class, $method) = $func;
	$object = new $class($this->request);
	$object->configure();
	
	if (method_exists($object, $method)) {
	    $res = call_user_func_array(array ( $object, $method), $params);
	}
    }

}
