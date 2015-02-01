<?php

	/**
	 * Home Presenter
	 */
	class TodoPresenter extends Presenter {
		public function main($reqArgs) {
			error_reporting(E_ALL ^ E_STRICT);

			// Create collection and load DB
			$tc = new ToDoCollection();
			$tc->load();

			// Create view
			$mView = new View();
			$mView->loadTemplate('templates/listAll.crml');
			// Register collection
			$mView->registerModel('todos', $tc);
			// Render
			$mView->render();
		}
		public function single($reqArgs)
		{
			error_reporting(E_ALL ^ E_STRICT);

			// Get the current ID
			$id = $this->getArg("id");

			// Create Data Map and load DB Row
			$t = new ToDoMap();
			$t->load($id);
			// Create view
			$mView = new View();
			$mView->loadTemplate('templates/single.crml');
			// Register model
			$mView->registerModel('todo', $t);
			// Render
			$mView->render();
		}
		public function destroy($reqArgs)
		{
			error_reporting(E_ALL ^ E_STRICT);

			// Get the current ID
			$id = $this->getArg("id");

			// Create Map
			$t = new ToDoMap();
			$t->idTodo = $id;
			// Destroy
			$t->destroy();

			// Relocate to root
			relocate(rootPath());
		}
		public function create($reqArgs)
		{
			error_reporting(E_ALL ^ E_STRICT);

			// Get form data
			$cap = (isset($reqArgs['caption'])) ? $reqArgs['caption'] : '';

			// Create Map
			$t = new ToDoMap();
			$t->caption = $cap;
			//Save
			$t->save();

			// Relocate to root
			relocate(rootPath());
		}
		public function update($reqArgs)
		{
			error_reporting(E_ALL ^ E_STRICT);

			// Get the current ID
			$id = $this->getArg("id");

			// Get form data
			$cap = (isset($reqArgs['caption'])) ? $reqArgs['caption'] : '';
			$chk = (isset($reqArgs['checked'])) ? $reqArgs['checked'] : '0';

			// Create Map
			$t = new ToDoMap();
			$t->load($id);
			$t->caption = $cap;
			$t->checked = $chk;
			//Save
			$t->save();

			// Relocate to root
			relocate(rootPath());
		}
		public function toggle($reqArgs)
		{
			// Get the current ID
			$id = $this->getArg("id");

			// Create Map
			$t = new ToDoMap();
			$t->load($id);
			$t->toggleCheck();
			//Save
			$t->save();

			// Relocate to root
			relocate(rootPath());
		}
	}

?>