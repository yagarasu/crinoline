<?php

	class TestApp extends App {

		public function init() {
			$this->setSessionName("crinoline");
			$this->setRoutes(array(
				"/" => array( "Home" , "main" ),
				"/login" => array( "Home" , "login" ),
				"/logout" => array( "Home" , "logout" )
			));

			$this->bindEvent( "INIT", array($this,'onInited') );

			parent::init();
		}

		public function onInited($evtArgs) {
			$this->parseRoute();
		}

	}

?>