<?

namespace Core;

class Session {
	private $sessionState = false;
	private static $instance;
	 
	private function __construct() { }

	public static function getInstance() {
		if ( !isset(self::$instance)) {
			self::$instance = new self;
		}

		self::$instance->startSession();

		return self::$instance;
	}

	public function startSession() {
		if (!$this->sessionState) {
			$this->sessionState = session_start();
		}

		return $this->sessionState;
	}

	public function __set( $name , $value ) {
		$_SESSION[$name] = $value;
	}

	public function __get($name) {
		return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
	}

	public function __isset( $name ) {
		return isset($_SESSION[$name]);
	}

	public function __unset( $name ) {
		unset( $_SESSION[$name] );
	}

	public function destroy() {
		if ( $this->sessionState) {
			$_SESSION = array();
			unset( $_SESSION );
			$this->sessionState = !session_destroy();

			return !$this->sessionState;
		}

		return false;
	}
}
