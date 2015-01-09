<?php

	/**
	 * Router class
	 * 
	 * @version 0.2.0
	 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
	 **/
	class Router {

		private $routes = array();

		public function __construct( $routes=array() ) {
			$this->setRoutes($routes);
		}

		/**
		 * Appends a route to the rule set
		 * @param string $route     Route expression
		 * @param string $presenter Name of the Presenter class
		 * @param string $action    Method to be called from the Presenter
		 */
		public function addRoute( $route, $presenter, $action ) {
			$this->routes[$route] = array( $presenter , $action );
		}

		/**
		 * Sets the routes to be used by the parser. Overrides previous routes.
		 * @todo cache regexed routes
		 * @param array $routes Routes to register
		 */
		public function setRoutes($routes) {
			$this->routes = $routes;
		}

		/**
		 * Returns all the registered routes
		 * @return array All the registered routes
		 */
		public function getRoutes() {
			return $this->routes;
		}

		/**
		 * Takes a URL-like string and enroutes the request
		 * @param  string $route URL to parse
		 */
		public function parseRoute( $route ) {
			foreach ($this->routes as $r=>$p) {
				$pattern = $this->regexFromRoute($r);
				$matches = null;
				if(preg_match($pattern, $route, $matches)) {
					$this->enroute($p[0], $p[1], $matches);
				}
			}
		}

		/**
		 * Takes a presenter, instantiates it and calls the action
		 * @param  string $presenter Name of the Presenter class
		 * @param  string $action    Method to be called from the Presenter
		 */
		public function enroute( $presenter , $action, $params=null ) {
			$presenter = ucfirst($presenter);
			if( class_exists($presenter) ) {
				$p = new $presenter($params);
				if( method_exists($p, $action) ) {
					$p->$action();
				} else {
					throw new Exception("Routing error. {$presenter} can't perform '{$action}'.");
				}
			} else {
				throw new Exception("Routing error. {$presenter} class doesn't exist.");
			}
		}

		private function regexFromRoute($route='') {
			$route = preg_replace('/%([\w\d]+)%/i', '(?P<$1>[\w\d]+)', $route);
			$route = str_replace('/', '\/', $route);
			return '/^'.$route.'$/i';
		}
		
	}

?>