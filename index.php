<?php
/**
 * @file
 * Start here
 *
 *	@version 1.0.0 Ajolote
 * 
 */

// Absolute path
define('ABS_PATH', dirname(__FILE__));
define('DOMAIN' , $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);

// Require config
if (!is_readable(ABS_PATH . '/config.inc.php')) die('Error: config.inc.php file not found.');
require( ABS_PATH . '/config.inc.php' );

// Error reporting
if ($conf['env'] === 'DEV') {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// Start bootstrap
require( ABS_PATH . '/core/bootstrap.inc.php' );

?>
