<?php
class SQLite3ModulesRegistry extends SQLite3 implements IModuleRegistry {
	function __construct($args) {
		if (!isset($args['filename'])) throw new Exception('SQLite3ModulesRegistry error. Filename not set in config.inc.php.');
		
		if(!is_file($args['filename'])) {
			throw new Exception('Unable to load "' . $args['filename'] . '" modules registry.');
		} else {
			$this->open($args['filename']);
		}
	}

	function getModules() {
		$res = $this->query('SELECT * FROM modules');
		$modules = array();
		while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
			$modules[] = $row;
		}
		return $modules;
	}

	function getModule($name) {
		$res = $this->querySingle('SELECT * FROM modules WHERE name="'.$name.'"', true);
		if(count($res) === 0) return false;
		return $res;
	}

	function installModule($name, $version) {
		return $this->exec('INSERT INTO modules (name,version) VALUES ("'.$name.'",'.$version.')');
	}

	function uninstallModule($name) {
		return $this->exec('DELETE FROM modules WHERE name="'.$name.'"');
	}

	function updateModule($name) {
		return $this->exec('UPDATE modules SET version=version + 1 WHERE name="'.$name.'"');
	}
}
?>