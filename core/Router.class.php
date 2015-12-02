<?php
/**
 * Static class router
 */
class Router {
	// Setted routes
	static private $routes = array();

	static public function route_bind($route, $callback) {
		$regex = self::compile($route);
		self::$routes[$regex] = $callback;
	}

	static private function compile($route) {
		$route = str_replace('/', '\/', $route);
		$route = str_replace(':', '\:', $route);
		$route = preg_replace('/\*/', '[a-zA-Z0-9-_]+', $route);
		$route = preg_replace('/^ALL\\\:/', '(?:GET|POST|PUT|DELETE)\\\:', $route);
		$route = preg_replace('/%([\w\d]+)%/i', '(?P<$1>[^\/]+)', $route);
		$route .= (substr($route, -2, 2)==='\/') ? '?' : '\/?';
		return '/^'.$route.'$/i';
	}

	static public function enroute($route) {
		$method = array();
		preg_match('/^(GET|POST|PUT|DELETE)\:/', $route, $method);
		switch ($method[1]) {
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
		foreach (self::$routes as $regex => $callback) {
			$m = array();
			if(preg_match_all($regex, $route, $m)) {
				array_shift($m);
				$args = array_merge($m, $reqArgs);
				if(is_callable($callback)) {
					call_user_func($callback,$args);
					return TRUE;
				} else {
					throw new Exception('Callback is not callable for this route.');
				}
			}
		}
		return FALSE;
	}
}
?>