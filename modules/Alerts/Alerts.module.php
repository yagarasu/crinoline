<?php
/**
 * Alerts module
 */
class Alerts implements IModule {
	
	static public $update = 0;

	static public function init() {
		hook_bind('error', 'Alerts::hndError');
	}

	static public function add($message, $type='info') {
		$session = new Session();
		$alerts = $session->getData('alerts');
		if ($alerts === null) {
			$alerts = array(
				array(
					'message' => $message,
					'type' => $type
				)
			);
		} else {
			$a = array(
				'message' => $message,
				'type' => $type
			);
			if (!in_array($a, $alerts))	$alerts[] = $a;
		}
		$session->setData('alerts', $alerts);
	}

	static public function get() {
		$session = new Session();
		$alerts = $session->getData('alerts', array());
		$session->setData('alerts', array());
		return $alerts;
	}

	static public function hndError($args) {
		self::add('Error #'.$args['errno'].': ' . $args['errstr'] . '('.$args['errfile'].'@'.$args['errline'].')', 'error');
	}

	static public function install() {
		return self::update;
	}

	static public function uninstall() {
		return TRUE;
	}
}
?>