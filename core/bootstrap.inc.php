<?php
/**
 * @file
 * Initializates the system
 */

// Register autoloader for Core
require(ABS_PATH . '/core/autoloader.inc.php');

// Defines
define( 'HERE' , $_SERVER['REQUEST_URI'] );
define( 'MODULES_DIR' , $conf['modules_dir'] );
define( 'THEMES_DIR' , $conf['themes_dir'] );
define( 'THEME_NAME' , $conf['theme_name'] );
define( 'ENVIRONMENT' , $conf['env'] );

// Require general functions
require(ABS_PATH . '/core/functions.inc.php');

// Require hooks and filters system
require(ABS_PATH . '/core/hooks.inc.php');
require(ABS_PATH . '/core/filters.inc.php');

// Require error handling
require(ABS_PATH . '/core/errorhandling.inc.php');

// Require routing
require(ABS_PATH . '/core/routing.inc.php');

// Require templating
require(ABS_PATH . '/core/templating.inc.php');

// Main try-catch. Last resource to catch 
//   gracefully any exception.
try {

	// Require modules system
	hook_invoke('modules_load');
	require(ABS_PATH . '/core/modules.inc.php');
	modules_load();
	hook_invoke('modules_loaded', $_modules);
	
	// Start theme
	hook_invoke('theme_load');
	theme_load();
	hook_invoke('theme_loaded');

	hook_invoke('inited');

	// Enroute
	routing_enroute();

} catch(Exception $e) {
	echo '<pre>';
	echo 'Exception. ' . $e->getMessage() . "\n";
	echo '>> ' . $e->getFile() . ' @ line: ' . $e->getLine() . "\n";
	echo 'Trace: ' . "\n" . $e->getTraceAsString();
	echo '</pre>';
	die();
}

?>