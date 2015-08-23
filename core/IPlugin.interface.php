<?php
/**
 * Interface for plugins to implement
 */
interface IPlugin {
	static public function init();
	static public function install();
	static public function uninstall();
}
?>