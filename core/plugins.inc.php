<?php
/**
 * @file
 * Main plugin functions
 * @todo : REVIEW ALL PROCESSES. MIGRATED, BUT NOT REVIEWED.
 */

/**
 * Loads all plugins
 */
function plugins_load() {
	global $conf;
	$pluginsRegistry = new PluginsRegistry($conf['plugins_registry']);
	$plugins = $pluginsRegistry->getPlugins();
	foreach ($plugins as $p) {
		$plugin = $p['name'];
		$filename = ABS_PATH . '/plugins/' . $plugin . '/' . $plugin . '.plugin.php';
		if(is_file($filename)) {
			include($filename);
		} else {
			throw new Exception('Unable to load plugin "' . $plugin . '".');
		}
		if(is_callable($plugin . '::init')) {
			call_user_func($plugin . '::init');
		} else {
			throw new Exception('Unable to start plugin "' . $plugin . '".');
		}
	}
}

/**
 * Includes, executes install routine and updates plugin registry for $plugin.
 * If Plugin is already in plugin registry, aborts.
 * @param  string $plugin Name of the plugin
 * @return array         Install log.
 */
function plugins_install($plugin) {
	global $conf;
	$pluginsRegistry = new PluginsRegistry($conf['plugins_registry']);
	if($pluginsRegistry->getPlugin($plugin) !== FALSE) {
		$log = array('Plugin "'.$plugin.'" is already installed. Aborting.');
		return $log;
	}
	$log = array('Installing plugin "'.$plugin.'".');
	$filename = ABS_PATH . '/plugins/' . $plugin . '/' . $plugin . '.plugin.php';
	if(is_file($filename)) {
		include($filename);
	} else {
		$log[] = 'Plugin file not found. Aborting.';
		return $log;
	}
	if(is_callable($plugin.'::install')) {
		if(call_user_func($plugin.'::install')) {
			$pluginsRegistry->installPlugin($plugin,$plugin::$update);
			$log = array('Plugin "'.$plugin.'" installed.');
			return $log;
		}
	} else {
		$log[] = 'Plugin install function not found. Aborting.';
		return $log;
	}
}

function plugins_uninstall($plugin) {
	global $conf;
	$pluginsRegistry = new PluginsRegistry($conf['plugins_registry']);
	if($pluginsRegistry->getPlugin($plugin) === FALSE) {
		$log = array('Plugin "'.$plugin.'" is not installed. Aborting.');
		return $log;
	}
	$log = array('Uninstalling plugin "'.$plugin.'".');
	if(!class_exists($plugin)) {
		// Class not defined. Include plugin.
		$filename = ABS_PATH . '/plugins/' . $plugin . '/' . $plugin . '.plugin.php';
		if(is_file($filename)) {
			include($filename);
		} else {
			$log[] = 'Plugin file not found. Aborting.';
			return $log;
		}
	}
	if(is_callable($plugin.'::uninstall')) {
		if(call_user_func($plugin.'::uninstall')) {
			$pluginsRegistry->uninstallPlugin($plugin);
			$log = array('Plugin "'.$plugin.'" uninstalled.');
			return $log;
		}
	} else {
		$log[] = 'Plugin uninstall function not found. Aborting.';
		return $log;
	}
}

/**
 * Checks the Plugin Registry and compares it with the Plugin's update number.
 * If they doesn't match, the update functions are executed.
 * @return array Update log after the execution of all functions.
 */
function plugins_update() {
	global $conf;
	$pluginsRegistry = new PluginsRegistry($conf['plugins_registry']);
	$plugins = $pluginsRegistry->getPlugins();
	$log = array();
	foreach ($plugins as $p) {
		$plugin = $p['plugin'];
		if($p['update'] < $plugin::$update) {
			$log[$plugin] = array('Outdated. Updating '.($plugin::$update - $p['update']).' version(s)...');
			$updateFile = ABS_PATH . '/plugins/' . $plugin . '/' . $plugin . '.update.php';
			if(!is_file($updateFile)) {
				$log[$plugin] = array('Warning! Update file "'.$updateFile.'" not found.');
				break;
			}
			require($updateFile);
			for ($v=$p['update']+1; $v <= $plugin::$update; $v++) { 
				if(is_callable('plugins_'.$plugin.'_update_'.$v)) {
					$res = call_user_func('plugins_'.$plugin.'_update_'.$v);
					if($conf['plugins_verbose_update']) {
						foreach ($res['verbose'] as $verboseLog) {
							$log[$plugin][] = $verboseLog;
						}
					}
					$log[$plugin][] = $res['result'];
					if($res['updated']) {
						$pluginsRegistry->updatePlugin($plugin);
					}
				} else {
					$log[$plugin][] = 'Warning! Update to version '.$v.' not found in update script. Skipping.';
				}
			}
			
		} elseif ($p['update'] > $plugin::$update) {
			$log[$plugin] = array('Warning! Your installation seems higher than the one in your plugin. Please check that your files are up to date.');
		} else {
			$log[$plugin] = array('Up to date.');
		}
	}
	return $log;
}

/**
 * Constructs the base path to a plugin
 * @param  string $plugin plugin name
 * @return string         plugin base path
 */
function plugins_getBasePath($plugin) {
	return PLUGINS_DIR . '/' . $plugin;
}

/**
 * Constructs the filename of a plugin
 * @param  string $plugin plugin name
 * @return string         plugin filename
 */
function plugins_getFilename($plugin) {
	return $plugin . '.plugin.php';
}

/**
 * Constructs the full path to a plugin
 * @param  string $plugin plugin name
 * @return string         Full path to plugin file
 */
function plugins_getPath($plugin) {
	return plugins_getBasePath($plugin) . '/' . plugins_getFilename($plugin);
}

?>