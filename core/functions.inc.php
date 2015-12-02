<?php
/**
 * @file
 * General functions
 */

/**
 * Checks if an array is assoc
 * Based on Captain kurO's answer on: http://stackoverflow.com/a/4254008/4760754
 * @param  array $array An array to check
 * @return bool        Whether array is assoc or not
 */
function array_is_assoc($array) {
	return (bool)count(array_filter(array_keys($array), 'is_string'));
}

/**
 * Creates a unique hash for every callback
 * @param  callable $callback Callback
 * @return string           Hash for this callback
 */
function callback_hash($callback) {
	if (is_string($callback)) {
		return md5($callback);
	} elseif (is_array($callback)) {
		if (count($callback) !== 2) throw new Exception('Callables must have 2 elements.');
		if (!is_string($callback[1])) throw new Exception('Second element of callable must be a string.');
		if (is_object($callback[0])) {
			return md5(spl_object_hash($callback[0]) . '::' . $callback[1]);
		} elseif (is_string($callback[0])) {
			return md5($callback[0] . '::' . $callback[1]);
		}
	} elseif (is_object($callback)) {
		return md5(spl_object_hash($callback));
	}
	throw new Exception('Callable is in a wrong format.');
}

/**
 * Retrieves a global variable
 * @param  string $name    Name of the variable
 * @param  mixed $default Default value if not found. Default: NULL
 * @return mixed          The value of the variable
 */
function var_get($name, $default=null) {
	global $conf;
	if (isset($conf[$name])) {
		return $conf[$name];
	}
}

?>