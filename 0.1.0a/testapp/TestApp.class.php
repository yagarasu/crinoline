<?php

	class TestApp extends App {

		public function init() {
			$this->bindEvent( "INIT", array($this,'onInited') );
			$this->bindEvent( "INIT_ERROR", array($this, 'onInitError') );
			$this->bindEvent( "NOTFOUND" , array($this,'on404') );

			$this->setSessionName("crinoline");
			$this->setRoutes(array(
				"/" 			=> array( "Home" , "main" ),
				"/login" 		=> array( "Home" , "login" ),
				"/logout" 		=> array( "Home" , "logout" ),
				"/news"			=> array( "News" , "listAll" ),
				"/news/test"	=> array( "News" , "test" ),
				"/news/new"		=> array( "News" , "newArticle"),
				"/news/%id%"	=> array( "News" , "showSingle" )
			));

			if( $this->loadInclude('includes/dbconfig') ) {
				//echo "<p>DBCONFIG Loaded</p>";
			} else {
				//echo "<p>DBCONFIG not loaded</p>";
			}

			parent::init();
		}

		public function onInited($evtArgs) {
			$this->parseRoute();
		}

		public function onInitError($evtArgs)
		{
			echo "Initialization error.";
		}

		public function on404($evtArgs) {
			echo "404<br>";
		}

	}

?>