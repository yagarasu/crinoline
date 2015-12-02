<?php
/**
 * Configuration Driver interface
 */
interface IConfigDriver {
    
    public function get($key, $default='');
    public function set($key, $value);
    public function exists($key);
    public function fetch();
    public function update();
    
}
?>