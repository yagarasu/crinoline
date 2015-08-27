<?php
/**
 * @file
 * Example site main presenter
 */
class ExampleSite_MainPresenter {

	static public function home($args) {
		template_name_set('index');
		template_load('index');

		$dbs = var_get('database');

		$host = $dbs['default']['host'];
		$user = $dbs['default']['user'];
		$pass = $dbs['default']['pass'];
		$db =  $dbs['default']['name'];

		$db = new MySQLDriver($host, $user, $pass, $db);
		$db->connect();

		$c = new ContactMap(array(), $db);
		$c->load(2);

		var_dump($c->toArray());

	}

}
?>