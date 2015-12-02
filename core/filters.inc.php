<?php
/**
 * @file
 * Filters functions
 */

$_filters = array();

/**
 * Binds a callback to the filter queue
 * @param  string  $filterName filter name
 * @param  callable  $callback Callback to bind
 * @return string Callback hash
 */
function filter_bind($filterName, $callback) {
	global $_filters;
	$cbhash = callback_hash($callback);
	$_filters[$filterName][$cbhash] = $callback;
	return $cbhash;
}

/**
 * Unbinds callback from filter queue
 * @param  string $filterName filter name
 * @param  callable $callback Callback to unbind
 */
function filter_unbind($filterName, $callback) {
	global $_filters;
	$cbhash = callback_hash($callback);
	if (isset($_filters[$filterName][$cbhash]))	unset($_filters[$filterName][$cbhash]);
}

/**
 * Runs all the given filter queue
 * @param  string $filterName filter name
 * @param string $input Input to the filter queye
 */
function filter_invoke($filterName, $input) {
	global $_filters;
	$args = func_get_args();
	$args = array_slice($args, 2);
	$output = $input;
	if (isset($_filters[$filterName]) && is_array($_filters[$filterName])) {
		foreach ($_filters[$filterName] as $cbhash => $callback) {
			if (is_callable($callback)) {
				array_unshift($args, $output);
				$output = call_user_func_array($callback, $args);
				array_shift($args);
			}
		}
	}
	return $output;
}
?>