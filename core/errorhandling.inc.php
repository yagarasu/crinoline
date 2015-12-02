<?php
/**
 * @file
 * Error handler
 */

/**
 * Set the custom error handler
 */
set_error_handler(function($errno, $errstr, $errfile, $errline, $errctx) {
	hook_invoke('error', array(
		'errno' => $errno,
		'errstr' => $errstr,
		'errfile' => $errfile,
		'errline' => $errline,
		'errctx' => $errctx,
	));

	// Fallback to a hardcoded exception if no error handler function is bound.
	// Only for DEV env.
	global $_hooks;
	if (ENVIRONMENT === 'DEV') {
		if (isset($_hooks['error']) && is_array($_hooks['error']) && count($_hooks['error']) > 0) {
			return;
		}
		throw new Exception('Error ' . $errno . ': ' . $errstr . '(' . $errfile . '@' . $errline . ')');
	}
	die();
});

?>