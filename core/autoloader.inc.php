<?php
/**
 * @file
 * Autoloader functions and register autoloader
 */

// Global autoloader queue variable.
$_autoloaderDirs = array( ABS_PATH . '/core' );

spl_autoload_register(function($className) {
	global $_autoloaderDirs;
	if (preg_match('/^I[A-Z]/', $className) === 1) {
		$filename = $className . '.interface.php';
	} else {
		$filename = $className . '.class.php';
	}
	foreach ($_autoloaderDirs as $dir) {
		$path = $dir . '/' . $filename;
		if (is_file($path)) {
			include($path);
			break;
		}
	}
});

/**
 * Appends a new directory in the main autoloader function queue
 * @param  string $dir Path to the dir to add (no trailing slash)
 */
function autoloader_register_dir($dir) {
	if (!is_dir($dir)) throw new Exception('Directory "' . $dir . '" is not readable.');
	global $_autoloaderDirs;
	$_autoloaderDirs[] = $dir;
}
?>