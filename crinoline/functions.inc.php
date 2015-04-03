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

?>