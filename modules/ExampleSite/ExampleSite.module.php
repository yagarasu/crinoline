<?php
/**
 * @file
 * An example site module
 */
class ExampleSite implements IModule {

	static public $update = 0;

	/**
	 * Initialize module
	 */
	static public function init() {
		// Register autoload dir.
		autoloader_register_dir(modules_getBasePath('ExampleSite') . '/presenters');

		// Routes
		Router::route_bind('ALL:/', 'ExampleSite_MainPresenter::home');

		// 404
		hook_bind('router_notfound', function($evt) {
			template_load('not-found');
		});
	}

	/**
	 * Install the module
	 * @return mixed Version number or FALSE on fail
	 */
	static public function install() {
		return 0;
	}

	/**
	 * Uninstall the module
	 * @return bool Whether the module was installed or not
	 */
	static public function uninstall() {
		return TRUE;
	}

}
?>