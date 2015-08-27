<?php
/**
 * MySQL bound Data Map
 */
abstract class MySQLDataMap extends DataMap implements IPersistable {
    
    public $database = NULL;

    protected $schema_table = '';
    protected $schema_primary = '';
    
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
            && $this->database instanceof MySQLDriver
            && $this->schema_table !== ''
            && $this->schema_primary !== ''
        );
    }
    
    /**
     * Load an entity from database
     * @param mixed $id
     *   Primary key for the search
     */
    public function load($id) {
        if (!$this->isPersistable())
            throw new Exception('This model is not persistable.');
        $tbl = $this->schema_table;
        $pid = $this->schema_primary;
        $res = $this->database->queryFirst("SELECT * FROM {$tbl} WHERE {$pid} = '{$id}';");
        if ($res === FALSE) {
            return FALSE;
        }
        $this->fromArray($res);
        return TRUE;
    }
    
    /**
     * Saves the entity into the database. If id exists, updates it; if not,
     * inserts it.
     */
    public function save() {
        if (!$this->isPersistable())
            throw new Exception('This model is not persistable.');
        $tbl = $this->schema_table;
        $pid = $this->schema_primary;
        if (isset($this->$pid)) {
            $cnt = $this->database->countRows($tbl, "{$pid} = '{$id}'");
            if ($cnt > 0) {
                $res = $this->database->update($tbl, $this->toArray(), "{$pid} = '{$id}'");
                if ($res === FALSE) {
                    $this->database->rollback();
                    throw new Exception('Error. Unable to save model. ' . $this->database->getLastError());
                } else {
                    $this->database->commit();
                    return $this->pid;
                }
            }
        }
        $res = $this->database->insert($tbl, $this->toArray());
        if ($res === FALSE) {
            $this->database->rollback();
            throw new Exception('Error. Unable to save model. ' . $this->database->getLastError());
        } else {
            $this->database->commit();
            return $this->pid;
        }
    }
    
    /**
     * Destroys current entity
     */
    public function destroy() {
        if (!$this->isPersistable())
            throw new Exception('This model is not persistable.');
        $tbl = $this->schema_table;
        $pid = $this->schema_primary;
        if (!isset($this->$pid))
            throw new Exception('Can not destroy an unsaved model.');
        $res = $this->database->delete($tbl, "{$pid} = '{$id}'");
        if ($res === FALSE) {
            $this->database->rollback();
            throw new Exception('Error. Unable to save model. ' . $this->database->getLastError());
        } else {
            $this->database->commit();
            return TRUE;
        }
    }
    
}
?>