<?php

	/**
	 * Crinoline Official Laces Plugin
	 *
	 * @author Alexys Hegmann "Yagarasu" <http://alexyshegmann.com>
	 * @version 1.0.0
	 */
	class CRLaces extends EventTrigger implements IPlugin {

		private $context = null;
		private $laces = null;

		/**
		 * Retrieves metadata
		 * @return array Metadata array
		 */
		public function getInfo() {
			return array(
				'version' => '1.0.0',
				'name' => 'CRLaces Plugin',
				'desc' => 'Plugin to add support for laces.',
				'author' => 'Alexys Hegmann',
				'uri' => 'http://alexyshegmann.com',
				'license' => 'MIT',
				'licenceUri' => 'http://opensource.org/licenses/MIT',
			);
		}
		
		/**
		 * Setup the object
		 * @param  array $params Params from the Config file
		 */
		public function setup($params) {
			if(isset($params['lacesRoot'])&&!defined('LACES_ROOT')) {
				define('LACES_ROOT', $params['lacesRoot']);
			} else if(!defined('LACES_ROOT')) {
				define('LACES_ROOT', $params['path'] . 'laces/');
			}
			includeFile( LACES_ROOT . 'autoloader.inc.php' );
			laces_register_autoloader();
			$this->context = new Context();
			$this->laces = new Laces($this->context);
		}
		
		/**
		 * Binds all the needed events
		 * @param  App &$app The main app to listen to
		 */
		public function bind(&$app) {
		}

		// Bubble functions
		public function parse($template) {
			$this->triggerEvent('PARSE', array(
				'context' => $this->context,
				'laces' => $this->laces
			));
			return $this->laces->parse($template);
		}

		public function render($template) {
			$this->triggerEvent('PARSE', array(
				'context' => $this->context,
				'laces' => $this->laces
			));
			$this->triggerEvent('RENDER', array(
				'context' => $this->context,
				'laces' => $this->laces
			));
			$this->laces->render($template);
		}

		public function loadAndParse($url) {
			$this->triggerEvent('LOADANDPARSE', array(
				'context' => $this->context,
				'laces' => $this->laces,
				'url' => $url
			));
			$this->triggerEvent('PARSE', array(
				'context' => $this->context,
				'laces' => $this->laces
			));
			return $this->laces->loadAndParse($url);
		}

		public function loadAndRender($url) {
			$this->triggerEvent('LOADANDRENDER', array(
				'context' => $this->context,
				'laces' => $this->laces,
				'url' => $url
			));
			$this->triggerEvent('PARSE', array(
				'context' => $this->context,
				'laces' => $this->laces
			));
			$this->triggerEvent('RENDER', array(
				'context' => $this->context,
				'laces' => $this->laces
			));
			$this->laces->loadAndRender($url);
		}

		public function setIntoContext($name, $value) {
			$this->triggerEvent('CONTEXTSET', array(
				'context' => $this->context,
				'name' => $name,
				'value' => $value
			));
			$this->context->set($name, $value);
		}

		public function getFromContext($name) {
			$this->triggerEvent('CONTEXTGET', array(
				'context' => $this->context,
				'name' => $name
			));
			$this->context->get($name);
		}

		public function existsInContext($name) {
			return $this->context->exists($name);
		}

		public function registerHookInContext($hook, $callback) {
			$this->triggerEvent('CONTEXTREGISTERHOOK', array(
				'context' => $this->context,
				'hook' => $hook,
				'callback' => $callback
			));
			$this->context->registerHook($hook, $callback);
		}

		public function unregisterHookInContext($hook, $callback) {
			$this->triggerEvent('CONTEXTUNREGISTERHOOK', array(
				'context' => $this->context,
				'hook' => $hook,
				'callback' => $callback
			));
			$this->context->unregisterHook($hook, $callback);
		}
		
	}

?>