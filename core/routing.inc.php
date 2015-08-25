<?php
/**
 * Creates a request string from the current $_GET['_r'], then sends it to the router.
 */
function routing_enroute() {
	$method = $_SERVER['REQUEST_METHOD'];
    $method = ($method==='GET'||$method==='POST'||$method==='PUT'||$method==='DELETE') ? $method : 'GET';
	$r_url = (isset($_GET['_r']) && $_GET['_r'] !== '') ? $_GET['_r'] : '/';
	$r_url = $method . ':' . $r_url;
	$r_url = filter_invoke('router_route', $r_url, array(
		'method' => $method,
		'path' => (isset($_GET['_r']) && $_GET['_r'] !== '') ? $_GET['_r'] : '/'
	));
	hook_invoke('router_enroute', array(
		'route' => $r_url
	));
	$res = Router::enroute($r_url);
	hook_invoke('router_enrouted', array(
		'route' => $r_url,
		'result' => $res
	));
	if (!$res) {
		hook_invoke('router_notfound', array(
			'route' => $r_url
		));
	}
}
?>