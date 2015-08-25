<?php
class Hello implements IModule {

	static public $update = 0;

	static public function init() {
	}

	static public function install() {
		return 0;
	}

	static public function uninstall() {
		return TRUE;
	}
}
?>