<?php
/**
 * @file
 * Example site main presenter
 */
class ExampleSite_MainPresenter {

	static public function home($args) {
		template_name_set('index');
		template_load('index');

		$hcdb = new HardcodedDriver(array(
			'tbl' => array(
				array(
					'id' => 'foo',
					'name' => 'foo',
					'text' => 'barbar'
				),
				array(
					'id' => 'fiz',
					'name' => 'foo',
					'text' => 'barbarbar'
				),
				array(
					'id' => 'foo',
					'name' => 'foasdasdasdouuu',
					'text' => 'barbarbasdfsdfsdfr'
				),
			)
		));
		
		$hcdb->insert('tbl', array(
			'id' => 'feee',
			'name' => 'foo',
			'text' => 'asdasdasd'
		));
		$hcdb->commit();
		
		$hcdb->delete('tbl', array(
			'id' => 'fiz'
		));
		$hcdb->commit();
		
		echo '<pre>';
		var_dump($hcdb->select('tbl', '*', array(
			'name' => 'foo'
		)));
		echo '</pre>';

	}

}
?>