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
require( ABS_PATH . '/config.inc.php' );

// Start bootstrap
require( ABS_PATH . '/core/bootstrap.inc.php' );

?>