<?php

/**
 * Crinoline Bootstrap
 * 
 * Sets the main variables and routes the request to the correct file.
 * 
 * @version 0.1.0
 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
 */

/**
 * __autoload
 * 
 * Requires the correct file when a class is intantiated
 * 
 * @param string $ClassName The name of the class to be required
 */
function __autoload($className) {
	global $cfg;
	$filename = $className.'.class.php';
	if( is_readable($cfg['dirs']['core'].$filename) ) {
		require($cfg['dirs']['core'].$filename);
	} else {
		foreach ($cfg['dirs']['secondary'] as $dir) {
			if( is_readable( $dir.$filename ) ) {
				require($dir.$filename);
				return true;
			} else {
				continue;
			}
		}
		throw new Exception("Error loading the class ".$className, 100);
	}
}

/**
 * bootstrap
 * 
 * Tries to start the App singleton. Catches fatal uncatched exceptions from other scopes.
 */
try {

	global $cfg;
	if( !is_readable($cfg['dirs']['core'].'functions.inc.php') ) throw new Exception("Functions file not found.", 100);
	require_once($cfg['dirs']['core'].'functions.inc.php');

	$appClass = $cfg['app_class'];

	$app = $appClass::getInstance();
	$app->init();

} catch( Exception $e ) {
	die($e);
}
?>