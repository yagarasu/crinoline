<?php

	/**
	 * Global functions
	 */

	/**
	 * Relocate the browser even if the headers were already sent
	 * @param  string $url Address to redirect the browser to
	 */
	function relocate($url) {
		if(!headers_sent()) {
			header('location:'.$url);
			exit();
		} else {
			echo '<script>window.location.href="'.$url.'";</script>';
			exit();
		}
	}

	/**
	 * $app wrapper
	 * @return App Main app object
	 */
	function app() {
		global $app;
		return $app;
	}

	/**
	 * appRoot wrapper
	 * @return string App root path setted in $config
	 */
	function appRoot() {
		global $config;
		return $config['appRoot'];
	}

	/**
	 * Current Route wrapper
	 * @return string Current route
	 */
	function currentRoute() {
		global $app;
		return $app->getCurrentRoute();
	}

	/**
	 * Plugin wrapper
	 * @param  string $plugin Plugin to retrieve
	 * @return IPlugin         Plugin requested or null
	 */
	function plg($plugin) {
		global $plugins;
		return $plugins->plugin($plugin);
	}

	/**
	 * Includes a file searching the alternative directories
	 * @param  string $file File to include
	 */
	function includeFile($file) {
		if(is_readable($file)) {
			include $file;
		} else {
			global $config;
			$d = $config['altDirs'];
			array_push($d, CRINOLINE_CORE);
			foreach ($d as $dir) {
				$fn = $dir . $file;
				if(is_readable($fn)) {
					include $fn;
					return;
				} else {
					continue;
				}
			}
			throw new Exception('Unable to load "' . $file . '"');
		}
	}

?>