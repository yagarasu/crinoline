<?php

	/**
	 * Router class
	 * 
	 * @version 0.3.1
	 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
	 */
	class Router extends EventTrigger {

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
			$this->triggerEvent("ADDROUTE", array(
				'route'			=> $route,
				'presenter'		=> $presenter,
				'action'		=> $action
			));
		}

		/**
		 * Sets the routes to be used by the parser. Overrides previous routes.
		 * @todo cache regexed routes
		 * @param array $routes Routes to register
		 */
		public function setRoutes($routes) {
			$this->routes = $routes;
			$this->triggerEvent("RESETROUTES", array(
				'routes'			=> $routes
			));
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
			$pattern = '';
			foreach ($this->routes as $r=>$p) {
				$colPos = strpos($route, ":");
				if($colPos===false) {
					$method = 'GET';
				} else {
					$method = substr($route, 0, $colPos);
				}
				$pattern = $this->regexFromRoute($r);
				$matches = null;
				if(preg_match($pattern, $route, $matches)) {
					
					$this->triggerEvent("BEFOREROUTING", array(
						'route'			=> $route,
						'routePattern'	=> $pattern,
						'method'		=> $method,
						'presenter'		=> $p[0],
						'action'		=> $p[1],
						'args'			=> $matches
					));

					$this->enroute($p[0], $p[1], $method, $matches);
					
					$this->triggerEvent("AFTERROUTING", array(
						'route'			=> $route,
						'routePattern'	=> $pattern,
						'method'		=> $method,
						'presenter'		=> $p[0],
						'action'		=> $p[1],
						'args'			=> $matches
					));

					return;
				}
			}
			$this->triggerEvent("NOTFOUND", array(
				'route'			=> $route,
				'routePattern'	=> $pattern
			));
		}

		/**
		 * Takes a presenter, instantiates it and calls the action
		 * @param  string $presenter Name of the Presenter class
		 * @param  string $action    Method to be called from the Presenter
		 * @param string $method HTTP Method
		 * @param array $params Matched params from %foo% var types
		 */
		public function enroute( $presenter , $action, $method= 'GET', $params=null ) {
			// Get params from the HTTP method
			switch ($method) {
				case 'GET':
					$reqArgs = $_GET;
					break;
				case 'POST':
					$reqArgs = $_POST;
					break;
				case 'PUT':
				case 'DELETE':
					$reqArgs = array();
					parse_str(file_get_contents("php://input"), $reqArgs);
					break;
				default:
					$reqArgs = array();
					break;
			}
			// Get the presenter and call it
			$presenter = ucfirst($presenter);
			if( class_exists($presenter) ) {
				$p = new $presenter($params);
				if( method_exists($p, $action) ) {
					$p->$action($reqArgs);
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
			$route = str_replace(':', '\:', $route);
			return '/^'.$route.'$/i';
		}
		
	}

?>