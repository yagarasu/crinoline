<?php

	/**
	 * Tailor App
	 *
	 * Crinoline automatic builder. This app helps the user to create the primary
	 * structure for his app.
	 */
	class TailorApp extends App {
		
		/**
		 * Init the app
		 */
		public function init() {
			
			$this->setRoutes();
			$this->bindEvents();    

			$this->handleRequest();

		}

		/**
		 * Sets the routes and binds them to the presenters
		 */
		private function setRoutes() {
			$this->addRoute('ALL:/', 'HomePresenter', 'main');
			$this->addRoute('ALL:/about/', 'HomePresenter', 'about');
			$this->addRoute('POST:/builder/', 'HomePresenter', 'build');
			$this->addRoute('GET:/download/', 'HomePresenter', 'download');
		}

		/**
		 * Binds some events to the main app
		 */
		private function bindEvents() {
			$this->bindEvent('PARSE', array($this, 'hnd_parse')); // To prepare some data into context before parsing
			$this->bindEvent('NOTFOUND', array($this, 'hnd_404')); // To handle 404 errors
		}

		/**
		 * Handle 404s
		 * @param  array $args Event array
		 */
		public function hnd_404($args) {
			plg('CRLaces')->loadAndRender('templates/404.ltp');
		}

		/**
		 * Handle the CRLaces PARSE event
		 * @param  array $args Event array
		 */
		public function hnd_parse($args) {
			
			plg('CRSession')->coupleWith(plg('CRLaces'));
			
		}
		
	}

?>