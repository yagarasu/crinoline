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
			$this->bindEvent( "NOTFOUND" , array($this,'on404') );

			parent::init();
		}

		public function onInited($evtArgs) {
			$this->parseRoute();
		}

		public function on404($evtArgs) {
			echo "404<br>";
		}

	}

?>