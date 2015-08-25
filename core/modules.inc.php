<?php
/**
 * @file
 * Main module functions
 */

/**
 * Loads all modules
 */
function modules_load() {
	global $conf;
	if (!isset($conf['modules_registry'])) throw new Exception('Unable to load modules registry. Check your config.inc.php file.');
	if (!isset($conf['modules_registry']['class'])) throw new Exception('Modules registry class not set. Check your config.inc.php file.');

	$modulesRegistryClass = $conf['modules_registry']['class'];
	$modulesRegistryArgs = isset($conf['modules_registry']['args']) ? $conf['modules_registry']['args'] : array();
	$modulesRegistry = new $modulesRegistryClass($modulesRegistryArgs);

	$modules = $modulesRegistry->getModules();
	foreach ($modules as $m) {
		$module = $m['name'];
		$filename = ABS_PATH . '/modules/' . $module . '/' . $module . '.module.php';
		if(is_file($filename)) {
			include($filename);
		} else {
			throw new Exception('Unable to load module "' . $module . '".');
		}
		if(is_callable($module . '::init')) {
			call_user_func($module . '::init');
		} else {
			throw new Exception('Unable to start module "' . $module . '".');
		}
	}
}

/**
 * Includes, executes install routine and updates module registry for $module.
 * If module is already in module registry, aborts.
 * @param  string $module Name of the module
 * @return array         Install log.
 */
function modules_install($module) {
	global $conf;
	if (!isset($conf['modules_registry'])) throw new Exception('Unable to load modules registry. Check your config.inc.php file.');
	if (!isset($conf['modules_registry']['class'])) throw new Exception('Modules registry class not set. Check your config.inc.php file.');

	$modulesRegistryClass = $conf['modules_registry']['class'];
	$modulesRegistryArgs = isset($conf['modules_registry']['args']) ? $conf['modules_registry']['args'] : array();
	$modulesRegistry = new $modulesRegistryClass($modulesRegistryArgs);

	if($modulesRegistry->getModule($module) !== FALSE) {
		$log = array('Module "'.$module.'" is already installed. Aborting.');
		return $log;
	}
	$log = array('Installing module "'.$module.'".');
	$filename = ABS_PATH . '/modules/' . $module . '/' . $module . '.module.php';
	if(is_file($filename)) {
		include($filename);
	} else {
		$log[] = 'Module file not found. Aborting.';
		return $log;
	}
	if(is_callable($module.'::install')) {
		if(call_user_func($module.'::install')) {
			$install = $module::install();
			if ($install !== FALSE) {
				$modulesRegistry->installModule($module, $install);
				$log[] = 'Module "'.$module.'" installed.';
				return $log;
			} else {
				$log[] = 'Module "' . $module . '" installation failed.';
				return $log;
			}
		}
	} else {
		$log[] = 'Module install function not found. Aborting.';
		return $log;
	}
}

/**
 * Uninstalls a module by name.
 * 
 * @param  string $module Name of the module to uninstall
 * @return array 		  List of log strings
 */
function modules_uninstall($module) {
	global $conf;
	if (!isset($conf['modules_registry'])) throw new Exception('Unable to load modules registry. Check your config.inc.php file.');
	if (!isset($conf['modules_registry']['class'])) throw new Exception('Modules registry class not set. Check your config.inc.php file.');

	$modulesRegistryClass = $conf['modules_registry']['class'];
	$modulesRegistryArgs = isset($conf['modules_registry']['args']) ? $conf['modules_registry']['args'] : array();
	$modulesRegistry = new $modulesRegistryClass($modulesRegistryArgs);

	if($modulesRegistry->getModule($module) === FALSE) {
		$log[] = 'Module "'.$module.'" is not installed. Aborting.';
		return $log;
	}
	$log[] = 'Uninstalling module "'.$module.'".';
	if(!class_exists($module)) {
		// Class not defined. Include module.
		$filename = ABS_PATH . '/modules/' . $module . '/' . $module . '.module.php';
		if(is_file($filename)) {
			include($filename);
		} else {
			$log[] = 'module file not found. Aborting.';
			return $log;
		}
	}
	if(is_callable($module.'::uninstall')) {
		if(call_user_func($module.'::uninstall')) {
			$uninstall = $modulesRegistry->uninstallmodule($module);
			if (!$uninstall) {
				$log[] = 'Module "' . $module . '" could not be uninstalled.';
				return $log;
			}
			$log[] = 'Module "'.$module.'" uninstalled.';
			return $log;
		}
	} else {
		$log[] = 'Module uninstall function not found. Aborting.';
		return $log;
	}
}

/**
 * Checks the module Registry and compares it with the module's update number.
 * If they doesn't match, the update functions are executed.
 * @return array Update log after the execution of all functions.
 */
function modules_update() {
	global $conf;
	if (!isset($conf['modules_registry'])) throw new Exception('Unable to load modules registry. Check your config.inc.php file.');
	if (!isset($conf['modules_registry']['class'])) throw new Exception('Modules registry class not set. Check your config.inc.php file.');

	$modulesRegistryClass = $conf['modules_registry']['class'];
	$modulesRegistryArgs = isset($conf['modules_registry']['args']) ? $conf['modules_registry']['args'] : array();
	$modulesRegistry = new $modulesRegistryClass($modulesRegistryArgs);

	$modules = $modulesRegistry->getModules();
	foreach ($modules as $m) {
		$module = $m['name'];
		if($m['version'] < $module::$update) {
			$log[$module] = array('Outdated. Updating ' . ($module::$update - $m['version']) . ' version(s)...');
			$updateFile = ABS_PATH . '/modules/' . $module . '/' . $module . '.update.php';
			if(!is_file($updateFile)) {
				$log[$module] = array('Warning! Update file "' . $updateFile . '" not found.');
				break;
			}
			require($updateFile);
			for ($v = $m['version'] + 1; $v <= $module::$update; $v++) { 
				if (is_callable('modules_'.$module.'_update_'.$v)) {
					$res = call_user_func('modules_'.$module.'_update_'.$v);
					$verbose = isset($conf['modules_verbose_update']) ? $conf['modules_verbose_update'] : FALSE;
					if ($verbose) {
						foreach ($res['verbose'] as $verboseLog) {
							$log[$module][] = $verboseLog;
						}
					}
					$log[$module][] = $res['result'];
					if ($res['updated']) {
						$modulesRegistry->updatemodule($module);
					}
				} else {
					$log[$module][] = 'Warning! Update to version '.$v.' not found in update script. Skipping.';
				}
			}
			
		} elseif ($m['version'] > $module::$update) {
			$log[$module] = array('Warning! Your installation seems higher than the one in your module. Please check that your files are up to date.');
		} else {
			$log[$module] = array('Up to date.');
		}
	}
	return $log;
}

/**
 * Constructs the base path to a module
 * @param  string $module module name
 * @return string         module base path
 */
function modules_getBasePath($module) {
	return MODULES_DIR . '/' . $module;
}

/**
 * Constructs the filename of a module
 * @param  string $module module name
 * @return string         module filename
 */
function modules_getFilename($module) {
	return $module . '.module.php';
}

/**
 * Constructs the full path to a module
 * @param  string $module module name
 * @return string         Full path to module file
 */
function modules_getPath($module) {
	return modules_getBasePath($module) . '/' . modules_getFilename($module);
}

?>