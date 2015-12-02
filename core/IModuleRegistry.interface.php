<?php
/**
 * Module registry interface
 */
interface IModuleRegistry {
	public function getModules();
	public function getModule($name);
	public function installModule($name, $version);
	public function uninstallModule($name);
	public function updateModule($name);
}

?>