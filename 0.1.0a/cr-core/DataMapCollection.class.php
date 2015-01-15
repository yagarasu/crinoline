<?php

	/**
	 * Abstract class. Represents a collection of DataMaps
	 */
	abstract class DataMapCollection extends EventTrigger
	{
		// Holds the main array
		protected $collection = array();
		// Default Data Map class
		protected $baseClass = null;

		/**
		 * Returns the collection array
		 * @return array The current collection
		 */
		public function toArray()
		{
			return $this->collection;
		}

		public function fromArray($collection)
		{
			foreach ($collection as $arrel) {
				$idx = $this->create($arrel);
				$this->at($idx)->bindEvent("ALL", function($evtArgs) {
					// Bubble all collection items events up
					$this->triggerEvent($evtArgs['event'], $evtArgs);
				});
			}
		}

		/**
		 * Appends an element to the main array
		 * @param  DataMap descendant $element The element to append
		 * @return int           The index pointer for the element
		 */
		public function append($element)
		{
			if(!$this->baseClassIsValid()||!is_a($element, $this->baseClass)) throw new Exception("'".get_class($element)."'' given; expecting '".$this->baseClass."'. You must supply a valid base class and the appended element must be a subclass of it.");
			array_push($this->collection, $element);
			return count($this->collection) - 1;
		}

		/**
		 * Creates and appends a new DataMap based on $this->baseClass
		 * @param  array $values Assoc array containing the values for the new element
		 * @return int         The index ponter of the new element
		 */
		public function create($values)
		{
			if(!$this->baseClassIsValid()) throw new Exception("You can't create new DataMaps if you don't define a valid base class."); 
			$baseClass = $this->baseClass;
			return $this->append(new $baseClass($values));
		}

		/**
		 * Removes an element from the collection
		 * @param  int $index The index to destroy
		 */
		public function remove($index)
		{
			if($index<0||$index>=count($this->collection)) throw new Exception("Index out of collection bounds. Expected number between 0 and ".count($this->collection));
			unset($this->collection[$index]);
		}

		/**
		 * Returns the element at $index
		 * @param  int $index The index to search for
		 * @return mixed        The element at $index or null id index out of bounds
		 */
		public function at($index)
		{
			return ($index>=0 && $index<count($this->collection)) ? $this->collection[$index] : null;
		}

		/**
		 * Executes a foreach calling $callback for every item
		 * @param  Callable $callback The function or method to run
		 */
		public function each(Callable $callback)
		{
			foreach ($this->collection as $key=>$value) {
				call_user_func($callback, $key, $value);
			}
		}

		/**
		 * Filters the collection and returns the result
		 * @param  array  $filters The array with the filter params
		 * @return array          The result of the search
		 * @todo  Create magic functions
		 */
		public function searchFor($filters=array())
		{
			echo "<h2>filtering</h2>";
			return array_filter($this->collection, function($el) use (&$filters) {
				echo "<hr><p>Element: ".$el->title."</p>";
				$returnEl = true;
				foreach ($filters as $key=>$value) {
					echo "<p>Checking filter: ".$key."-".$value."</p>";
					$returnEl = $returnEl && $this->searchFor_compare($el, $key, $value);
				}
				return $returnEl;
			});
		}

		/**
		 * Compares Key to Value and returns the result
		 * @param  string $key   A key descriptor
		 * @param  string $value A value descriptor
		 * @return boolean        Result of the descriptor operation
		 */
		private function searchFor_compare($el, $key, $value)
		{
			$comparator = substr($value, 0, 1);
			$value = (strpos('=!~/', $comparator)!==false) ? substr($value, 1) : $value;
			$comparator = (strpos('=!~/', $comparator)!==false) ? $comparator : '=';
			echo "<p>Comparator: ".$comparator."</p>";
			switch ($comparator) {
				case '!':
					return ($el->$key!==$value);
					break;
				case '~':
					return (strpos($el->$key, $value)!==false);
					break;
				case '/':
					return (preg_match($value, $el->$key));
					break;
				case '_':
					return (isset($el->$key));
					break;
				default:
					return ($el->$key===$value);
					break;
			}
		}

		/**
		 * Deletes every element in the collection
		 */
		public function clear()
		{
			$this->collection = array();
		}

		/**
		 * Checks if the base class providen is set and valid
		 * @return boolean Whether the base class is not null,
		 *                 the class exists (autoload if not done)
		 *                 and the class extends from DataMap
		 */
		private function baseClassIsValid()
		{
			$baseClass = $this->baseClass;
			return (
				$baseClass!==null 
				&& class_exists($baseClass) 
				&& is_subclass_of($baseClass, "DataMap")
			);
		}
	}

?>