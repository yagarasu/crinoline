<?php
class PluginsRegistry extends SQLite3 {
	function __construct($filename) {
		if(!is_file($filename)) {
			throw new Exception('Unable to load "' . $filename . '" plugins registry.');
		} else {
			$this->open($filename);
		}
	}

	function getPlugins() {
		$res = $this->query('SELECT * FROM plugins');
		$plugins = array();
		while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
			$plugins[] = $row;
		}
		return $plugins;
	}

	function getPlugin($name) {
		$res = $this->querySingle('SELECT * FROM plugins WHERE name="'.$name.'"', true);
		if(count($res) === 0) return false;
		return $res;
	}

	function installPlugin($name, $version) {
		return $this->exec('INSERT INTO plugins (name,version) VALUES ("'.$name.'",'.$version.')');
	}

	function uninstallPlugin($name) {
		return $this->exec('DELETE FROM plugins WHERE name="'.$name.'"');
	}

	function updatePlugin($name) {
		return $this->exec('UPDATE plugins SET version=version + 1 WHERE name="'.$name.'"');
	}
}
?>