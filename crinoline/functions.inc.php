<?php

	/**
	 * Global functions
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

	function appRoot() {
		global $config;
		return $config['appRoot'];
	}

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