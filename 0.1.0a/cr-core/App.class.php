<?php

	/**
	 * Singleton. App class to bootstrap
	 *
	 * @version 0.1.0
	 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
	 */
	class App extends EventTrigger {

		// Holds the unique instance
		protected static $instance;

		// Hold config variables
		private $sessionName = 'crinoline_app';
		private $routes = array();

		// Hold the control objects
		private $session;
		private $router;

		/**
		 * Constructor
		 */
		protected function __construct() {}

		/**
		 * Unique access point to this Singleton
		 */
		final public static function getInstance() {
			if( !isset(static::$instance) ) {
				static::$instance = new static();
			}
			return self::$instance;
		}

		/**
		 * Initializes the main objects
		 */
		public function init() {
			$this->session = new Session($this->sessionName);
			$this->router = new Router($this->routes);
			$this->router->bindEvent('ALL', function($args) {
				// Bubble all router events up
				$this->triggerEvent($args['event'], $args);
			});
			$this->triggerEvent("INIT");
		}

		/**
		 * Returns the session object. Protects the uninstantiated session object.
		 * @return Session Current session object
		 */
		public function getSession() {
			if( isset($this->session) ) {
				return $this->session;
			} else {
				throw new Exception("Can not get the uninstantiated Session object. Init the Singleton first.");
			}
		}

		/**
		 * Includes a file from the child Apps
		 * @param  string $incName The file name (without prefix)
		 * @return boolean          Whether include opperation was successful or not.
		 */
		protected function loadInclude($incName)
		{
			if( strpos($incName, 'http://') ) throw new Exception("For your security, loadInclude does not include files from other domains.");
			global $cfg;
			if( strpos($incName, '/')!==false ) {
				if( is_readable( $cfg['dirs']['app'].$incName.'.inc.php' ) ) {
					include($cfg['dirs']['app'].$incName.'.inc.php');
					return true;
				}
			}
			foreach ($cfg['dirs']['secondary'] as $dir)
			{
				if( is_readable( $dir.$incName.'.inc.php' ) ) {
					include($dir.$incName.'.inc.php');
					return true;
				} else {
					continue;
				}
			}
			return false;
		}

		/**
		 * Sends a route to the router to parse
		 * @param  string $route Route to parse. If null given, takes $_GET['_r'] with the current HTTP method
		 */
		protected function parseRoute($route=null) {
			if($route === null) {
				$method = $_SERVER['REQUEST_METHOD'];
				$method = ($method==='GET'||$method==='POST'||$method==='PUT'||$method==='DELETE') ? $method : 'GET';
				$r_url = (isset($_GET['_r'])&&$_GET['_r']!=='') ? $_GET['_r'] : '/';
				$r_url = $method.':'.$r_url;
			} else {
				$r_url = $route;
			}
			$this->router->parseRoute($r_url);
		}

		/**
		 * Sets the session name
		 *
		 * @param string sessionName New name to set
		 */
		protected function setSessionName($sessionName) {
			if(!isset($this->session)) {
				$this->sessionName = $sessionName;
			} else {
				throw new Exception("Cannot set the session name after App initialization.");
			}
		}

		/**
		 * Returns the current session name
		 * @return string Current session name
		 */
		public function getSessionName() {
			return $this->sessionName;
		}

		/**
		 * Synchronizes the App list of routes and the router.
		 * This redundance was necesary to mantain a coherent initialization order in child Apps
		 * @param array $routes The routes to be used in the app.
		 */
		protected function setRoutes($routes) {
			$this->routes = $routes;
			if(isset($this->router)) {
				$this->router->setRoutes($routes);
			}
		}

		/**
		 * Returns the current routes (if Router is initialized, returns its routes)
		 * @return [type] [description]
		 */
		public function getRoutes()	{
			if(isset($this->router)) {
				return $this->router->getRoutes();
			} else {
				return $this->routes;
			}
		}

		/**
		 * Returns the App path in config.
		 * @return string Path to App.
		 */
		public function getAppPath()
		{
			global $cfg;
			return $cfg['dirs']['app'];
		}

		/**
		 * Clone handler to prevent Singleton clonation
		 */
		public function __clone() {
			trigger_error("Invalid operation: You can't clone ".get_class($this)." class instances. Use getInstance() instead.", E_USER_ERROR);
		}

		/**
		 * Wakeup handler to prevent Singleton unserialization
		 */
		public function __wakeup() {
			trigger_error("Invalid operation: You can't unserialize ".get_class($this)." class instances. Use getInstance() instead.", E_USER_ERROR);
		}

	}

?>