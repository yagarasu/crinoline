<?php

	/**
	 * Abstract class. Event trigger object.
	 *
	 * @version 0.1.0
	 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
	 */
	abstract class EventTrigger {

		// Holds the event-callback array for the bindings
		private $triggers = array();
		// Holds the callbacks for ALL special event
		private $triggerAll = array();

		/**
		 * Binds an event to a callback. If multiple callbacks are binded to one event, callbacks are called in sequence.
		 * @param  string $event    String identifier
		 * @param  Callable $callback Callback to bind to the event
		 */
		public function bindEvent($event, $callback)	{
			if($event==='ALL') {
				array_push($this->triggerAll, $callback);
			} elseif( array_key_exists($event, $this->triggers) ) {
				array_push( $this->triggers[$event] , $callback );
			} else {
				$this->triggers[$event] = array($callback);
			}
		}

		/**
		 * Unbinds an event.
		 * @param  string $event    String identifier
		 * @param  Callable $callback Callback to unbind.
		 */
		public function unbindEvent($event, $callback) {
			if($event==='ALL') {
				$search = array_keys( $this->triggerAll , $callback );
				array_walk($search, function($val, $key) {
					unset($this->triggerAll[$key]);
				});
			} elseif( array_key_exists($event, $this->triggers) ) {
				$search = array_keys( $this->triggers[$event] , $callback );
				array_walk($search, function($val, $key) {
					unset($this->triggers[$event][$key]);
				});
				if(count($this->triggers[$event])==0) {
					unset($this->triggers[$event]);
				}
			}
		}

		/**
		 * Triggers an event.
		 * @param  string $event String identifier
		 * @param  array  $args  Arguments to pass to the callback
		 */
		public function triggerEvent($event, $args=array())	{
			$args['source'] = &$this;
			$args['event'] = $event;
			foreach ($this->triggerAll as $callback) {
				@call_user_func($callback, $args);
			}
			if( array_key_exists($event, $this->triggers) ) {
				foreach ($this->triggers[$event] as $val) {
					@call_user_func($val, $args);
				}
			}
		}
		
		/**
		 * Gets the event queue
		 * @param string $event String identifier
		 * @return array Currently bound events
		 */
		public function getEventsFor($event) {
			if($event==='ALL') {
				return $this->triggerAll;
			} else {
				return $this->triggers[$event];
			}
		}
	}

?>