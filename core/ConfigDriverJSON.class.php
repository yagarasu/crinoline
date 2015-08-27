<?php
/**
 * JSON Configuration Driver
 */
class ConfigDriverJSON implements IConfigDriver {
    
    protected $file = '';
    protected $db = array();
    
    public function __construct($file) {
        $this->file = $file;
        $this->fetch();
    }
    
    public function get($key, $default='') {
        if(isset($this->db[$key])) return $this->db[$key];
        return $default;
    }
    
    public function set($key, $value) {
        $this->db[$key] = $value;
    }
    
    public function exists($key) {
        return isset($this->db[$key]);
    }
    
    public function fetch() {
        if(!is_readable($this->file)) throw new Exception('Unable to read the JSON file "'.$this->file.'" on Configuration Driver.');
        $cont = @file_get_contents($this->file);
        if($cont===false) throw new Exception('Unable to read the JSON file "'.$this->file.'" on Configuration Driver.');
        $this->db = json_decode($cont, true);
        if($this->db===null) {
            $this->db = array();
            throw new Exception('Unable to parse the JSON file "'.$this->file.'" on Configuration Driver.');
        }
    }
    
    public function update() {
        if(!is_writable($this->file)) throw new Exception('Unable to save into the JSON file "'.$this->file.'" on Configuration Driver.');
        $res = @file_put_contents($this->file, json_encode($this->db));
        if($res===false) throw new Exception('Unable to save into the JSON file "'.$this->file.'" on Configuration Driver.');
    }
    
}
?>