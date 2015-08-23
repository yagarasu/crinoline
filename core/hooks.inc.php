<?php
/**
 * @file
 * Hook functions
 */

$_hooks = array();

/**
 * Binds a callback to the hook queue
 * @param  string  $hookName Hook name
 * @param  callable  $callback Callback to bind
 * @return string Callback hash
 */
function hook_bind($hookName, $callback) {
	global $_hooks;
	$cbhash = callback_hash($callback);
	$_hooks[$hookName][$cbhash] = $callback;
	return $cbhash;
}

/**
 * Unbinds callback from hook queue
 * @param  string $hookName Hook name
 * @param  callable $callback Callback to unbind
 */
function hook_unbind($hookName, $callback) {
	global $_hooks;
	$cbhash = callback_hash($callback);
	if (isset($_hooks[$hookName][$cbhash]))	unset($_hooks[$hookName][$cbhash]);
}

/**
 * Runs all the given hook queue
 * @param  string $hookName Hook name
 */
function hook_invoke($hookName) {
	global $_hooks;
	$args = func_get_args();
	array_shift($args);
	if (isset($_hooks[$hookName]) && is_array($_hooks[$hookName])) {
		foreach ($_hooks[$hookName] as $cbhash => $callback) {
			if (is_callable($callback)) {
				call_user_func_array($callback, $args);
			}
		}
	}
}
?>