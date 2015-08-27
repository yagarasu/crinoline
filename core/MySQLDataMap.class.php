<?php
/**
 * MySQL bound Data Map
 */
class MySQLDataMap extends DataMap implements IPersistable {
    
    public $database = NULL;
    
    public function __construct($values = array(), &$database = NULL) {
        $this->database = $database;
        parent::__construct($values);
    }
    
    /**
     * Checks if current instance is persistable
     * @return boolean
     *   Whether this instance is persistable
     */
    private function isPersistable() {
        return (
            $this->database !== NULL
            && is_subclass_of($this->database, 'MySQLDriver')
        );
    }
    
    /**
     * Load an entity from database
     * @param mixed $id
     *   Primary key for the search
     */
    public function load($id) {
        
    }
    
    /**
     * Saves the entity into the database. If id exists, updates it; if not,
     * inserts it.
     */
    public function save() {
        
    }
    
    /**
     * Destroys current entity
     */
    public function destroy() {
        
    }
    
}
?>