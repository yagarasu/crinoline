<?php
class AppConfigDriver extends ConfigDriverHardcode {
    
    public function fetch() {
        $this->db = array(
            'mainDb' => array(
                'host' => 'localhost',
                'name' => 'test',
                'user' => 'root',
                'pass' => 'root'
            )
        );
    }
    
}
?>