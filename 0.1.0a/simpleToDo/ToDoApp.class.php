<?php

	class ToDoApp extends App {

		public function init() {
			$this->bindEvent( "INIT", array($this,'onInited') );
			$this->bindEvent( "INIT_ERROR", array($this, 'onInitError') );
			$this->bindEvent( "NOTFOUND" , array($this,'on404') );

			$this->setSessionName("simpleToDo");
			$this->setRoutes(array(
				"GET:/" 				=> array( "TodoPresenter" , "main" ),
				"POST:/create/"			=> array( "TodoPresenter" , "create" ),
				"GET:/%id%/" 			=> array( "TodoPresenter" , "single" ),
				"GET:/%id%/toggle/" 	=> array( "TodoPresenter" , "toggle" ),
				"POST:/%id%/update/" 	=> array( "TodoPresenter" , "update" ),
				"GET:/%id%/destroy/" 	=> array( "TodoPresenter" , "destroy" )
			));

			if( !$this->loadInclude('includes/dbconfig') ) {
				throw new Exception("Error loading the database configuration.");
			}

			parent::init();
		}

		public function onInited($evtArgs) {
			$this->parseRoute();
		}

		public function onInitError($evtArgs)
		{
			die("Initialization error.");
		}

		public function on404($evtArgs) {
			$err = new ErrorMap(array(
				'route'		=> $evtArgs['route']
			));
			$v = new View();
			$v->registerModel('error', $err);
			if(!$v->loadTemplate('templates/404.crml')) {
				die('404 error and could not find the template resource.');
			}
			$v->render();
		}

	}

?>