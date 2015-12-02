<?php
/**
 * @file
 * Example site main presenter
 */
class ExampleSite_MainPresenter {

	static public function home($args) {
		/*template_name_set('index');*/
		template_load('index');		

		$db = new SQLite3Driver('foo.db', 'foo');
		$db->connect();

	}

}
?>