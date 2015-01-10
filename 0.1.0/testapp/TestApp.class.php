<?php

	class TestApp extends App {

		public function init() {
			$this->setSessionName("crinoline");
			$this->setRoutes(array(
				"/" 			=> array( "Home" , "main" ),
				"/login" 		=> array( "Home" , "login" ),
				"/logout" 		=> array( "Home" , "logout" ),
				"/news"			=> array( "News" , "listAll" ),
				"/news/%id%"	=> array( "News" , "showSingle" )
			));

			$this->bindEvent( "INIT", array($this,'onInited') );

			parent::init();
		}

		public function onInited($evtArgs) {
			$this->parseRoute();
		}

	}

?>